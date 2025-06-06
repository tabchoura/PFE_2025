<template>
  <div class="page-wrapper">
    <div class="profil-container mes-offres">
      <h1>Mes offres enregistrées</h1>

      <!-- Loading / Error / Empty -->
      <div v-if="loading" class="loading">
        <div class="loading-spinner"></div>
        <p>Chargement de vos offres...</p>
      </div>
      <div v-else-if="error" class="error-message">
        <span class="error-icon">⚠️</span>
        <p>{{ error }}</p>
        <button @click="Mesoffres" class="retry-button">Réessayer</button>
      </div>
      <div v-else-if="!loading && !error && offers.length === 0" class="no-offers">
        <div class="empty-state-icon">📋</div>
        <p>Vous n'avez pas encore d'offres enregistrées.</p>
        <router-link to="/offres" class="browse-offers-btn">
          Parcourir les offres
        </router-link>
      </div>

      <!-- Liste des offres -->
      <div v-else class="offers-grid">
        <div v-for="off in offers" :key="off.id" class="offer-card">
          <div class="offer-content">
            <h2>{{ off.titre }}</h2>
            <p class="offer-description">{{ off.description }}</p>
            <div class="offer-details">
              <p class="salary"><span>Salaire:</span> {{ off.salaire }}</p>
              <p class="details"><span>Détails:</span> {{ off.details }}</p>
            </div>
          </div>
          <div class="offer-actions">
            <button @click="confirmDeletion(off)" class="delete-btn">
              <span class="delete-icon">🗑️</span>
              <span>Supprimer</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Modal de confirmation -->
      <div v-if="showConfirmModal" class="modal-overlay" @click="cancelDeletion">
        <div class="modal-content" @click.stop>
          <h3>Confirmation de suppression</h3>
          <p>
            Êtes-vous sûr de vouloir supprimer l'offre
            <strong>{{ selectedOffer.titre }}</strong> ?
          </p>
          <p class="modal-warning">Cette action est irréversible.</p>
          <div class="modal-actions">
            <button @click="cancelDeletion" class="btn-cancel">Annuler</button>
            <button @click="confirmAndDelete" class="btn-confirm">Supprimer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";

const offers = ref([]);
const error = ref(null);
const loading = ref(true);
const showConfirmModal = ref(false);
const selectedOffer = ref(null);
const toast = useToast();

async function Mesoffres() {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get("/api/mesoffres");
    offers.value = data;
  } catch (err) {
    console.error("Erreur de chargement:", err);
    error.value = "Impossible de charger vos offres enregistrées. Veuillez réessayer.";
  } finally {
    loading.value = false;
  }
}

function confirmDeletion(offer) {
  selectedOffer.value = offer;
  showConfirmModal.value = true;
}

function cancelDeletion() {
  showConfirmModal.value = false;
  selectedOffer.value = null;
}

async function confirmAndDelete() {
  try {
    const id = selectedOffer.value.id;
    const response = await axios.delete(`/api/offreenregistrer/${id}`);
    if (response.status === 200) {
      offers.value = offers.value.filter((o) => o.id !== id);
      toast.success("Offre supprimée avec succès");
    }
  } catch (err) {
    console.error("Erreur lors de la suppression:", err);
    toast.error("Erreur lors de la suppression");
  } finally {
    cancelDeletion();
  }
}

onMounted(Mesoffres);
</script>

<style scoped>
/* === BASE STYLES === */
.page-wrapper {
  background: linear-gradient(135deg, #f0f7ff, #e6f0ff);
  min-height: 100vh;
  padding: 2rem;
}
.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 7.5rem;
}
.profil-container {
  max-width: 1600px;
  margin: 0 auto;
  padding: 2.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 10px 15px -3px rgba(0, 0, 0, 0.08);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.profil-container:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* === TYPOGRAPHY === */
.mes-offres h1 {
  color: #1e3a8a;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  position: relative;
  padding-bottom: 0.5rem;
}
.mes-offres h1::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 4px;
  background: #3b82f6;
  border-radius: 2px;
}

/* === LOADING STATE === */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 0;
}
.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(59, 130, 246, 0.1);
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1.5rem;
}
.loading p {
  color: #3b82f6;
  font-size: 1.1rem;
  font-weight: 500;
}

/* === ERROR STATE === */
.error-message {
  background: #fff5f5;
  border-left: 4px solid #ef4444;
  padding: 1.5rem;
  border-radius: 8px;
  text-align: center;
  margin-bottom: 2rem;
}
.error-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}
.retry-button {
  background: #ef4444;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}
.retry-button:hover {
  background: #dc2626;
  transform: translateY(-1px);
}

/* === EMPTY STATE === */
.no-offers {
  text-align: center;
  padding: 3rem;
  background: #f8fafc;
  border-radius: 12px;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
}
.empty-state-icon {
  font-size: 3.5rem;
  color: #9ca3af;
  margin-bottom: 1.5rem;
  opacity: 0.7;
}
.no-offers p {
  color: #64748b;
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
}
.browse-offers-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);
  color: white;
  padding: 0.75rem 1.75rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}
.browse-offers-btn:hover {
  filter: brightness(0.8);
}

/* === OFFER CARD === */
.offer-card {
  width: 400px;
  display: flex;
  flex-direction: column;
  background-color: #fff;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 10px 15px -3px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border-left: 3px solid #3b82f6;
}
.offer-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}
.offer-content {
  padding: 1.5rem;
  flex: 1;
}
.offer-content h2 {
  color: #1e3a8a;
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
}
.offer-description {
  color: #475569;
  font-size: 0.95rem;
  margin-bottom: 1.25rem;
}
.offer-details p {
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
  color: #64748b;
  display: flex;
}
.offer-details span {
  font-weight: 600;
  color: #334155;
  min-width: 70px;
  display: inline-block;
}
.salary span {
  color: #065f46 !important;
}
.offer-actions {
  padding: 1rem 1.5rem;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}
.delete-btn {
  background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
  border: none;
  border-radius: 8px;
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: white;
}
.delete-btn:hover {
  filter: brightness(0.8);
  transform: translateY(-1px);
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
  animation: fadeIn 0.2s ease-out;
}

.modal-content {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
.modal-warning {
  color: #b91c1c;
  font-weight: 600;
  margin-top: 0.5rem;
}
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}
.btn-cancel {
  background: transparent;
  border: 1px solid #64748b;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
}
.btn-confirm {
  background: #ef4444;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
}
.btn-confirm:hover {
  filter: brightness(0.9);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .page-wrapper {
    padding: 1rem;
  }
  .profil-container {
    padding: 1.75rem;
  }
  .mes-offres h1 {
    font-size: 1.75rem;
  }
  .offers-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}
@media (max-width: 480px) {
  .profil-container {
    padding: 1.5rem;
  }
  .no-offers {
    padding: 2rem 1.5rem;
  }
}
</style>
