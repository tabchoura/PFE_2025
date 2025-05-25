<template>
  <div class="page-wrapper">
    <div
      class="postuler-container"
      tabindex="-1"
      ref="postulerContainer"
      aria-live="polite"
    >
      <h1 class="title">Postuler à l'offre</h1>

      <!-- Chargement -->
      <div
        v-if="loading"
        class="loading-container"
        role="status"
        aria-live="polite"
        aria-busy="true"
      >
        <div class="loader" aria-hidden="true"></div>
        <p class="loading-text">Chargement ...</p>
      </div>

      <!-- Si pas de CV -->
      <div
        v-else-if="cvs.length === 0"
        class="empty-state"
        role="alert"
        aria-live="polite"
      >
        <div class="empty-icon" aria-hidden="true">📝</div>
        <h3>Vous n'avez pas encore de CV</h3>
        <p>Pour postuler à cette offre, vous devez d'abord créer votre CV.</p>
        <button
          @click="Creercv"
          class="primary-button create-cv-button"
          type="button"
          aria-label="Créer votre CV"
        >
          <span class="button-icon" aria-hidden="true">➕</span> Créer votre CV
        </button>
      </div>

      <!-- Etat de confirmation -->
      <div v-else class="confirmation-state" role="region" aria-live="polite">
        <div class="offre-info" v-if="detailsoffre">
          <h3 class="offre-title">{{ detailsoffre.titre }}</h3>
          <p class="offre-company">{{ detailsoffre.description }}</p>
          <p class="offre-location">{{ detailsoffre.salaire }}</p>
          <p>{{ detailsoffre.details }}</p>
        </div>

        <div class="cv-selection" v-if="cvs.length > 1">
          <h3>Sélectionnez le CV à utiliser</h3>
          <div class="cv-list" role="list">
            <div
              v-for="(cv, index) in cvs"
              :key="cv.id"
              :class="['cv-item', { selected: selectedCvId === cv.id }]"
              @click="selectedCvId = cv.id"
              @keyup.enter.space.prevent="selectedCvId = cv.id"
              role="listitem"
              tabindex="0"
              :aria-selected="selectedCvId === cv.id"
              :aria-label="`Sélectionner CV de ${cv.nom} ${
                cv.prenom
              }, mis à jour le ${formatDate(cv.updated_at || cv.created_at)}`"
            >
              <div class="cv-item-content">
                <span class="cv-name">{{ cv.nom }} {{ cv.prenom }}</span>
                <span class="cv-date"
                  >Mis à jour: {{ formatDate(cv.updated_at || cv.created_at) }}</span
                >
              </div>
              <div
                class="check-indicator"
                v-if="selectedCvId === cv.id"
                aria-hidden="true"
              >
                ✓
              </div>
            </div>
          </div>
        </div>

        <!-- Message de confirmation -->
        <div class="confirmation-message">
          <p>Êtes-vous sûr de vouloir postuler à cette offre ?</p>

          <div class="motivation-section">
            <label for="motivation" class="motivation-label"
              >Ajoutez un message (optionnel)</label
            >
            <textarea
              id="motivation"
              v-model="motivationText"
              class="motivation-textarea"
              placeholder="Expliquez brièvement pourquoi ce poste vous intéresse..."
              rows="5"
              aria-describedby="motivation-desc"
            ></textarea>
            <small id="motivation-desc" class="sr-only"
              >Champ optionnel pour ajouter un message de motivation</small
            >
          </div>
        </div>

        <div class="action-buttons">
          <button
            @click="confirmPostuler"
            class="primary-button confirm-button"
            :disabled="submitting"
            type="button"
            aria-live="polite"
            :aria-busy="submitting.toString()"
          >
            <span class="button-icon" v-if="!submitting" aria-hidden="true">✓</span>
            <span class="loader small-loader" v-else aria-hidden="true"></span>
            {{ submitting ? "Envoi en cours..." : "Confirmer ma candidature" }}
          </button>
          <button @click="cancel" class="secondary-button" type="button">
            ❌ Annuler
          </button>
        </div>
      </div>

      <!-- Message d'erreur -->
      <div
        v-show="error"
        class="error-message"
        role="alert"
        aria-live="assertive"
        tabindex="0"
      >
        <span class="error-icon" aria-hidden="true">⚠️</span>
        <p>{{ error }}</p>
        <button
          @click="error = null"
          class="close-error-button"
          aria-label="Fermer le message d'erreur"
          type="button"
        >
          ×
        </button>
      </div>

      <!-- Message de succès -->
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";

const toast = useToast();
const postulerContainer = ref(null);

const route = useRoute();
const router = useRouter();

const cvs = ref([]);
const loading = ref(true);
const error = ref(null);
const success = ref(null);
const submitting = ref(false);
const selectedCvId = ref(null);
const motivationText = ref("");
const detailsoffre = ref(null);

function formatDate(dateStr) {
  if (!dateStr) return "Date inconnue";
  const date = new Date(dateStr);
  return date.toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

onMounted(async () => {
  try {
    const offerId = route.params.id;
    const [offreResponse, cvsResponse] = await Promise.all([
      axios.get(`/api/offres/${offerId}`),
      axios.get(`/api/cv`),
    ]);
    detailsoffre.value = offreResponse.data;
    cvs.value = cvsResponse.data;
    if (cvs.value.length > 0) selectedCvId.value = cvs.value[0].id;
  } catch (e) {
    error.value =
      e.response?.data?.message || "Impossible de charger les données nécessaires";
    await nextTick();
    postulerContainer.value?.focus();
  } finally {
    loading.value = false;
  }
});

async function confirmPostuler() {
  error.value = null;
  success.value = null;
  const offerId = route.params.id;
  const cvId = selectedCvId.value || (cvs.value.length > 0 ? cvs.value[0].id : null);

  if (!cvId) {
    error.value = "Aucun CV disponible pour postuler";
    await nextTick();
    postulerContainer.value?.focus();
    return;
  }

  submitting.value = true;

  try {
    await axios.post(`/api/offres/${offerId}/postuler`, {
      cv_id: cvId,
      message: motivationText.value || null,
    });
    toast.success("Candidature envoyée avec succès !");
    router.push("/candidature");
  } catch (e) {
    error.value = e.response?.data?.message || "Erreur lors de la candidature";
    await nextTick();
    postulerContainer.value?.focus();
  } finally {
    submitting.value = false;
  }
}

function Creercv() {
  router.push({ name: "CreerCv", query: { from: "postuler", offreId: route.params.id } });
}

function cancel() {
  router.back();
}

function goToCandidatures() {
  router.push("/candidature");
}

function goToOffers() {
  router.push({ name: "Offres" });
}
</script>

<style scoped>
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  padding: 1rem;
  border-radius: 2%;
}

.postuler-container {
  max-width: 800px;
  margin: 6rem auto 2.5rem;
  padding: 2.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  outline: none;
}

.title {
  color: #1e3a8a;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  position: relative;
  padding-bottom: 0.5rem;
}
.title:after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 4px;
  background: #3b82f6;
  border-radius: 2px;
}

/* Loading */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
}
.loader {
  border: 4px solid rgba(52, 152, 219, 0.1);
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1.2s cubic-bezier(0.5, 0.1, 0.5, 0.9) infinite;
  margin-bottom: 1.5rem;
}
.small-loader {
  width: 22px;
  height: 22px;
  border-width: 2px;
  margin-right: 10px;
}
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
.loading-text {
  color: #7f8c8d;
  font-size: 1.2rem;
  font-weight: 500;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 4rem 0;
  animation: fadeIn 0.5s ease;
}
.empty-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
  color: #95a5a6;
  opacity: 0.8;
}
.empty-state h3 {
  font-size: 1.7rem;
  margin-bottom: 1.2rem;
  color: #2c3e50;
  font-weight: 600;
}
.empty-state p {
  color: #7f8c8d;
  margin-bottom: 2.5rem;
  font-size: 1.1rem;
  max-width: 80%;
  margin-left: auto;
  margin-right: auto;
}

/* Confirmation state */
.confirmation-state {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  animation: fadeIn 0.5s ease;
}
.offre-info {
  background: linear-gradient(to right, #f8f9fa, #f1f3f4);
  padding: 1.8rem;
  border-radius: 12px;
  margin-bottom: 1.2rem;
  border-left: 5px solid #3498db;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}
.offre-info:hover {
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
  transform: translateY(-2px);
}
.offre-title {
  margin: 0 0 0.7rem 0;
  font-size: 1.6rem;
  color: #2c3e50;
  font-weight: 700;
}
.offre-company {
  font-weight: 600;
  margin: 0 0 0.7rem 0;
  color: #34495e;
  font-size: 1.2rem;
}
.offre-location {
  color: #7f8c8d;
  margin: 0;
  display: flex;
  align-items: center;
  font-size: 1.05rem;
}
.offre-location:before {
  content: "📍";
  margin-right: 6px;
}

/* CV selection */
.cv-selection {
  margin: 1.5rem 0;
}
.cv-selection h3 {
  margin-bottom: 1.2rem;
  font-size: 1.3rem;
  color: #2c3e50;
  font-weight: 600;
}
.cv-list {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.cv-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.2rem 1.5rem;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  transition: all 0.25s ease;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}
.cv-item:hover,
.cv-item:focus-visible {
  border-color: #3498db;
  background: #f8f9fa;
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(52, 152, 219, 0.1);
  outline: none;
}
.cv-item.selected {
  border-color: #3498db;
  background: #ebf7fd;
  box-shadow: 0 5px 15px rgba(52, 152, 219, 0.15);
}
.cv-item-content {
  display: flex;
  flex-direction: column;
}
.cv-name {
  font-weight: 600;
  color: #2c3e50;
  font-size: 1.1rem;
}
.cv-date {
  font-size: 0.95rem;
  color: #7f8c8d;
  margin-top: 0.6rem;
}
.check-indicator {
  color: #3498db;
  font-size: 1.8rem;
  font-weight: bold;
  transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.cv-item.selected .check-indicator {
  transform: scale(1.2);
}

/* Confirmation message */
.confirmation-message {
  text-align: center;
  margin: 1.5rem 0;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 12px;
}
.confirmation-message p {
  font-size: 1.3rem;
  color: #2c3e50;
  margin-bottom: 1.5rem;
  font-weight: 500;
}

/* Motivation textarea */
.motivation-section {
  margin: 2rem 0;
  text-align: left;
}
.motivation-label {
  display: block;
  margin-bottom: 0.8rem;
  color: #34495e;
  font-weight: 500;
  font-size: 1.1rem;
}
.motivation-textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  min-height: 140px;
  resize: vertical;
  font-family: inherit;
  transition: all 0.3s ease;
  font-size: 1rem;
}
.motivation-textarea:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}
.motivation-textarea::placeholder {
  color: #b2bec3;
}

/* Buttons */
.action-buttons {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 2rem;
}
button {
  padding: 0.9rem 1.8rem;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  font-size: 1.05rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.primary-button {
  background: #3b82f6;
  color: white;
  box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
}
.primary-button:hover {
  background: linear-gradient(to right, #2980b9, #2471a3);
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
}
.primary-button:active {
  transform: translateY(-1px);
}
.primary-button:disabled {
  background: linear-gradient(to right, #95a5a6, #7f8c8d);
  cursor: not-allowed;
  transform: none;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.secondary-button {
  background: #f5f7fa;
  color: #34495e;
  border: 2px solid #dfe6e9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}
.secondary-button:hover {
  background: #e0e7ee;
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
.secondary-button:active {
  transform: translateY(-1px);
}
.create-cv-button {
  padding: 1.2rem 2.5rem;
  font-size: 1.15rem;
  border-radius: 12px;
}
.small-button {
  padding: 0.6rem 1.2rem;
  font-size: 0.95rem;
}
.button-icon {
  margin-right: 10px;
  font-size: 1.1em;
}
.confirm-button {
  padding: 1rem 2.5rem;
  min-width: 220px;
  background: linear-gradient(135deg, #20c599, #1fae8d, #178467);
}

/* Error message */
.error-message {
  display: flex;
  align-items: center;
  background: #fdedec;
  border-left: 5px solid #e74c3c;
  padding: 1.2rem;
  margin-top: 2rem;
  border-radius: 10px;
  color: #c0392b;
  position: relative;
  animation: slideIn 0.3s ease;
  box-shadow: 0 4px 10px rgba(231, 76, 60, 0.1);
}
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.error-icon {
  margin-right: 12px;
  font-size: 1.4rem;
}
.error-message p {
  font-size: 1.05rem;
  margin-right: 20px;
}
.close-error-button {
  position: absolute;
  right: 12px;
  top: 12px;
  background: none;
  border: none;
  color: #e74c3c;
  font-size: 1.4rem;
  cursor: pointer;
  padding: 0;
  transition: transform 0.2s ease;
}
.close-error-button:hover,
.close-error-button:focus-visible {
  transform: scale(1.2);
  outline: none;
}

@keyframes successPulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}
.success-message p {
  color: #2c3e50;
  font-size: 1.4rem;
  margin-bottom: 1.8rem;
  font-weight: 500;
}
.success-actions {
  display: flex;
  gap: 1.2rem;
  margin-top: 0.8rem;
}
.success-actions button {
  flex: 1;
}

/* Animations fadeIn */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Focus visible */
button:focus-visible,
.cv-item:focus-visible,
.motivation-textarea:focus-visible,
.close-error-button:focus-visible {
  outline: 3px solid #3498db;
  outline-offset: 2px;
}

/* Responsive */
@media (max-width: 768px) {
  .postuler-container {
    padding: 1.8rem;
    margin: 1rem;
    border-radius: 12px;
  }
  .title {
    font-size: 1.8rem;
  }
  .title:after {
    width: 80px;
  }
  .action-buttons {
    flex-direction: column;
    gap: 1rem;
  }
  .action-buttons button {
    width: 100%;
  }
  .success-actions {
    flex-direction: column;
    width: 100%;
    gap: 0.8rem;
  }
  .offre-title {
    font-size: 1.4rem;
  }
  .confirmation-message p {
    font-size: 1.2rem;
  }
  .empty-state p {
    max-width: 100%;
  }
  .empty-icon {
    font-size: 4rem;
  }
  .cv-item {
    padding: 1rem;
  }
  .motivation-textarea {
    min-height: 120px;
  }
}

/* Accessibility: hide visually but accessible */
.sr-only {
  position: absolute !important;
  width: 1px !important;
  height: 1px !important;
  padding: 0 !important;
  margin: -1px !important;
  overflow: hidden !important;
  clip: rect(0, 0, 0, 0) !important;
  white-space: nowrap !important;
  border: 0 !important;
}
</style>
