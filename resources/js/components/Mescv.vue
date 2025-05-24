<template>
  <div class="page-wrapper">
    <div class="mes-cv-container">
      <div class="header">
        <h1>Mes CV</h1>
        <button @click="ajoutercv" class="ajouter-button">
          <span class="icon">+</span>
          <span class="text">Nouveau CV</span>
        </button>
      </div>

      <!-- État de chargement -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Chargement de vos CV...</p>
      </div>

      <!-- Liste de CV -->
      <div v-else-if="cvs.length" class="cv-list">
        <div v-for="cv in cvs" :key="cv.id" class="cv-card">
          <div class="cv-info">
            <span class="cv-name">{{ cv.prenom }} {{ cv.nom }}</span>
            <span class="cv-date"
              >Dernière modification: {{ formatDate(cv.updatedAt) }}</span
            >
          </div>
          <div class="cv-actions">
            <button
              @click="consulterCv(cv.id)"
              class="action-button view"
              title="Consulter"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
            <button
              @click="modifierCv(cv.id)"
              class="action-button edit"
              title="Modifier"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
              </svg>
            </button>
            <button
              @click="confirmerSuppression(cv)"
              class="action-button delete"
              title="Supprimer"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polyline points="3 6 5 6 21 6"></polyline>
                <path
                  d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                ></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- État vide -->
      <div v-else class="empty-state">
        <div class="empty-icon">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="32"
            height="32"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
          </svg>
        </div>
        <p>Vous n'avez encore créé aucun CV</p>
        <p class="empty-subtitle">Créez votre premier CV pour commencer</p>
        <button @click="ajoutercv" class="empty-cta">Créer mon premier CV</button>
      </div>

      <!-- Modal de confirmation de suppression -->
      <div
        v-if="showConfirmModal"
        class="modal-overlay"
        @click="showConfirmModal = false"
      >
        <div class="modal-content" @click.stop>
          <h3>Confirmation de suppression</h3>
          <p>
            Êtes-vous sûr de vouloir supprimer le CV de
            <strong>{{ cvToDelete?.prenom }} {{ cvToDelete?.nom }}</strong> ?
          </p>
          <p class="modal-warning">Cette action est irréversible.</p>
          <div class="modal-actions">
            <button @click="showConfirmModal = false" class="btn-cancel">Annuler</button>
            <button @click="confirmerEtSupprimer" class="btn-confirm">Supprimer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

const toast = useToast();
const router = useRouter();
const loading = ref(true);
const cvs = ref([]);
const showConfirmModal = ref(false);
const cvToDelete = ref(null);

// Format de date
function formatDate(dateString) {
  if (!dateString) return "Date inconnue";
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }).format(date);
}

// Navigations
function ajoutercv() {
  router.push({ name: "CreerCv", query: { from: "mescv" } });
}

function consulterCv(id) {
  router.push({ name: "VoirCv", params: { id } });
}

function modifierCv(id) {
  router.push({ name: "Modifiercv", params: { id } });
}

function confirmerSuppression(cv) {
  cvToDelete.value = cv;
  showConfirmModal.value = true;
}

async function confirmerEtSupprimer() {
  try {
    await axios.delete(`/api/cv/${cvToDelete.value.id}`);
    cvs.value = cvs.value.filter((c) => c.id !== cvToDelete.value.id);
    showConfirmModal.value = false;
    // Notification de succès
    toast.success("CV supprimé avec succès");
  } catch (err) {
    console.error("Erreur suppression CV :", err);
    alert("Impossible de supprimer le CV.");
  }
}

// // Notification temporaire (simule un système de toast)
// function showNotification(message) {
//   const notif = document.createElement("div");
//   notif.className = "notification";
//   notif.textContent = message;
//   document.body.appendChild(notif);

//   // Animation d'entrée
//   setTimeout(() => {
//     notif.classList.add("show");
//   }, 10);

//   // Auto-suppression après 3 secondes
//   setTimeout(() => {
//     notif.classList.remove("show");
//     setTimeout(() => {
//       document.body.removeChild(notif);
//     }, 300);
//   }, 3000);
// }

// Chargement
onMounted(async () => {
  try {
    loading.value = true;
    const response = await axios.get("/api/cv");
    cvs.value = response.data;
  } catch (err) {
    console.error("Erreur lors du chargement des CV :", err);
    showNotification("Impossible de charger vos CV", "error");
  } finally {
    loading.value = false;
  }
});
</script>
<style scoped>
/* === BASE STYLES === */
.page-wrapper {
  background: linear-gradient(135deg, #f0f7ff, #e6f0ff);
  min-height: 100vh;
  padding: 2rem;
}

.mes-cv-container {
  background: white;
  border-radius: 16px;
  padding: 2.5rem;
  max-width: 1200px;
  margin: 0 auto;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* === HEADER STYLES === */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

.header h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e3a8a;
  position: relative;
  padding-bottom: 0.5rem;
}

.header h1::after {
  content: "";
  position: absolute;
  bottom: -1.5rem;
  left: 0;
  width: 60px;
  height: 3px;
  background: #3b82f6;
  border-radius: 3px;
}

.ajouter-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(90deg, #20c599 0%, #2dd9b5 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
}

.ajouter-button:hover {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
}

.ajouter-button .icon {
  font-size: 1.1rem;
  font-weight: bold;
}

/* === CV LIST STYLES === */
.cv-list {
  display: grid;
  gap: 1rem;
}

.cv-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  background: white;
  border-radius: 12px;
  border-left: 4px solid #3b82f6;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
}

.cv-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
  border-color: #cbd5e1;
}

.cv-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.cv-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
}

.cv-date {
  font-size: 0.85rem;
  color: #64748b;
}

/* === ACTION BUTTONS === */
.cv-actions {
  display: flex;
  gap: 0.5rem;
}

.action-button {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: white;
}

.action-button.view {
  background: linear-gradient(90deg, #e2e8f0 0%, #cbd5e1 100%);
  color: #334155;
}

.action-button.edit {
  background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);
}

.action-button.delete {
  background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
}

.action-button:hover {
  transform: scale(1.1);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.action-button.view:hover {
  background: #2563eb;
}

.action-button.edit:hover {
  background: #ca8a04;
}

.action-button.delete:hover {
  background: #dc2626;
}

/* === EMPTY STATE === */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 2px dashed #e2e8f0;
  margin-top: 1rem;
}

.empty-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 1.5rem;
  color: #94a3b8;
}

.empty-state p {
  margin: 0.5rem 0;
  color: #475569;
  font-size: 1.1rem;
}

.empty-subtitle {
  color: #64748b;
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
}

.empty-cta {
  padding: 0.75rem 1.5rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.empty-cta:hover {
  background: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 3px 6px rgba(59, 130, 246, 0.3);
}

/* === LOADING STATE === */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(59, 130, 246, 0.1);
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1.5rem;
}

.loading-state p {
  color: #64748b;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* === MODAL STYLES === */
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
  animation: slideUp 0.3s ease-out;
}

.modal-content h3 {
  margin-top: 0;
  margin-bottom: 1rem;
  color: #1e293b;
}

.modal-warning {
  color: #ef4444;
  font-size: 0.9rem;
  margin-top: 1rem;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-cancel {
  padding: 0.6rem 1.2rem;
  background: #f1f5f9;
  color: #475569;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-cancel:hover {
  background: #e2e8f0;
}

.btn-confirm {
  padding: 0.6rem 1.2rem;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-confirm:hover {
  background: #dc2626;
}

/* === NOTIFICATION STYLES === */
.notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #1e293b;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
  transform: translateY(100px);
  opacity: 0;
  transition: all 0.3s ease;
  z-index: 1000;
}

.notification.show {
  transform: translateY(0);
  opacity: 1;
}

/* === ANIMATIONS === */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* === RESPONSIVE STYLES === */
@media (max-width: 768px) {
  .page-wrapper {
    padding: 1rem;
  }

  .mes-cv-container {
    padding: 1.5rem;
  }

  .header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .header h1::after {
    bottom: -1rem;
  }

  .ajouter-button {
    width: 100%;
    justify-content: center;
  }

  .cv-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .cv-actions {
    width: 100%;
    justify-content: flex-end;
  }
}

@media (max-width: 480px) {
  .modal-content {
    padding: 1.5rem;
  }

  .modal-actions {
    flex-direction: column;
  }

  .btn-cancel,
  .btn-confirm {
    width: 100%;
  }
}
</style>
