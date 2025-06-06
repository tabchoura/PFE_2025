<template>
  <div class="page-wrapper">
    <div class="candidature-details-container">
      <!-- Chargement -->
      <div v-if="loading" class="loading-state">
        <div class="loader"></div>
        <p>Chargement des détails…</p>
      </div>

      <!-- Erreur -->
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="loadData" class="btn-retry">🔄 Réessayer</button>
      </div>

      <!-- Contenu principal -->
      <div v-else class="candidature-content">
        <h2>Détails de la candidature</h2>

        <!-- WORKFLOW STEPPER -->
        <div class="workflow-stepper">
          <div
            v-for="(step, i) in getSteps(statusKey)"
            :key="step.key"
            class="step"
            :class="{
              'step-done': stepOrder(statusKey) > i,
              'step-active': stepOrder(statusKey) === i
            }"
          >
            <span class="circle">{{ i + 1 }}</span>
            <span class="label">{{ step.label }}</span>
          </div>
        </div>

        <!-- Offre -->
        <div class="section-card offer-details">
          <h2>Offre d'emploi</h2>
          <h3>{{ offer.titre }}</h3>
          <p><strong>Description :</strong> {{ offer.description }}</p>
          <p><strong>Salaire :</strong> {{ offer.salaire }}</p>
          <p v-if="offer.details"><strong>Détails :</strong> {{ offer.details }}</p>
          <p class="status-line">
            <strong>Statut :</strong>
            <div :class="['status-badge', statusClass]">
              {{ statutLabel }}
            </div>
          </p>
        </div>

        <!-- Aperçu CV -->
        <div ref="cvElement" class="section-card cv-preview">
          <!-- ... CV preview omitted for brevity ... -->
        </div>

        <!-- Infos candidature -->
        <div class="section-card candidature-info">
          <h2>Infos candidature</h2>
          <p><strong>Date de dépôt :</strong> {{ formatDate(candidature.created_at) }}</p>
   <div v-if="statusKey === 'entretien' && candidature.date_entretien" class="entretien-info">
            <p class="entretien-status">
              🗓️ Entretien planifié pour le {{ formatDateTime12h(candidature.date_entretien) }}
            </p>
            <p class="entretien-status"> 🔗 Lien :{{ candidature.lien_visio }}</p>
          </div> 
          <div class="message-section">
            <h3>Message du candidat</h3>
            <div class="message-content">
              <span class="value">{{ truncateText(candidature.message, 100) }}</span>
            </div>
          </div>

          <!-- Affichage de la date d'entretien si statut entretien -->
       
        </div>

        <!-- Navigation -->
        <div class="navigation-actions">
          <button @click="goBack" class="btn-back">← Retour</button>
          <button @click="downloadPdf" class="btn-download">⬇️ Télécharger CV</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import html2pdf from 'html2pdf.js';

const route = useRoute();
const router = useRouter();
const candidatureId = route.params.candidatureId;

const loading = ref(true);
const error = ref(null);
const candidature = ref({});
const offer = ref({});
const cv = ref({});
const cvElement = ref(null);

const truncateText = (t, len = 100) => t && t.length > len ? t.slice(0, len) + '...' : t || '';

// Statut normalisé et libellé
const statusKey = computed(() => candidature.value.date_entretien ? 'entretien' : candidature.value.statut || 'pending');
const statusClass = computed(() => `status-${statusKey.value}`);
const statutLabel = computed(() => ({
  pending: 'En attente',
  accepted: 'Acceptée',
  refused: 'Refusée',
  entretien: 'Entretien'
}[statusKey.value]));

// Workflow
const getSteps = key => key === 'refused'
  ? [{ key: 'refused', label: 'Refusée' }]
  : [{ key: 'accepted', label: 'Acceptée' },{ key: 'entretien', label: 'Entretien' },{ key: 'embauche', label: 'Embauchée' }];
const stepOrder = key => ({ pending: 0, accepted: 0, entretien: 1, embauche: 2, refused: 0 }[key] || 0);

// Formatting dates
const formatDate = d => d ? new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
const formatDateTime12h = d => d ? new Intl.DateTimeFormat('fr-FR',{ day:'numeric', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit', hour12:true }).format(new Date(d)) : '';

// Chargement des données
async function loadData() {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get(`/api/candidatures/${candidatureId}`);
    candidature.value = data;
    offer.value = data.offre;
    Object.assign(cv.value, data.cv);
  } catch (e) {
    error.value = 'Impossible de charger les détails.';
  } finally {
    loading.value = false;
  }
}

function downloadPdf() {
  if (!cvElement.value) return;
  html2pdf().set({ margin: 10, filename: `CV_${candidature.value.cv.prenom}_${candidature.value.cv.nom}.pdf` }).from(cvElement.value).save();
}

function goBack() { router.back(); }

onMounted(loadData);
</script>




<style scoped>
/* Styles existants retenus */
.entretien-info {
  margin-top: 1.5rem;
  padding: 1.2rem;
  background-color: #ebf8ff;
  border-radius: 10px;
  border-left: 4px solid #3182ce;
}
.entretien-status {
  color: #2c5282;
  font-weight: 600;
  margin: 0;
  font-size: 1.05rem;
}
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  padding: 1rem 2rem;
  padding: 2rem;
}
/* Container global */
.candidature-details-container {
  max-width: 1000px;
  margin: 3rem auto;
  padding: 1.5rem;
  font-family: "Poppins", "Segoe UI", system-ui, sans-serif;
  color: #2d3748;
  background-color: #f8fafc;
  border-radius: 16px;
}

/* Cartes */
.section-card {
  background: #fff;
  padding: 2.5rem;
  border-radius: 16px;
  margin-bottom: 2.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  border: 1px solid #f0f4f8;
}
.step:not(:last-child):after {
  content: "";
  position: absolute;
  top: 12px;
  right: -50%;
  width: 100%;
  height: 2px;
  background-color: #e2e8f0;
  z-index: 1;
}

.step-done:not(:last-child):after {
  background-color: #3498db;
}

.step-active .circle {
  background-color: #3498db;
  color: white;
  box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
}
.step .label {
  font-size: 0.7rem;
  color: #718096;
  text-align: center;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.step-done .label,
.step-active .label {
  color: #3498db;
  font-weight: 600;
}

.circle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: #e2e8f0;
  color: #718096;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  margin-bottom: 0.5rem;
  z-index: 2;
  position: relative;
}

.step-done .circle {
  background-color: #3498db;
  color: white;
}
.step-active .circle {
  background-color: #3498db;
  color: white;
  box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
}

.status-filter {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  font-size: 1rem;
  color: #2c3e50;
  cursor: pointer;
  transition: all 0.2s ease;
}

.status-filter:focus-visible {
  outline: 2px solid #4299e1;
  outline-offset: 2px;
}

/* Stats bar */
.stats-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  background-color: #edf2f7;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.stats-bar p {
  margin: 0;
  color: #4a5568;
  font-size: 0.95rem;
}

.stats-count {
  font-weight: 700;
  color: #2c3e50;
}

/* Special States */
.loading-state,
.error-state,
.empty-state,
.no-results-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(52, 152, 219, 0.2);
  border-top-color: #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.error-state i,
.empty-state i,
.no-results-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #cbd5e0;
}

.error-state i {
  color: #e53e3e;
}

.error-state h3 {
  color: #c53030;
}

.empty-state h3,
.no-results-state h3 {
  color: #4a5568;
}

.retry-button,
.reset-filters-btn {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.retry-button {
  background: white;
  color: #3498db;
  border: 2px solid #3498db;
}

.retry-button:hover {
  background: #3498db;
  color: white;
}

.reset-filters-btn {
  background: #e53e3e;
  color: white;
  border: none;
}

.reset-filters-btn:hover {
  background: #c53030;
}

.retry-button i,
.reset-filters-btn i {
  margin-right: 0.5rem;
}

/* Candidature List */
.candidatures-list {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.candidature-card {
  display: flex;
  flex-direction: column;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  transition: box-shadow 0.3s ease, transform 0.3s ease;
  border-left: 4px solid #cbd5e0;
}

.candidature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  border-left-width: 5px;
}

/* Status Colors */
.status-enattente {
  border-left-color: #805ad5;
}

.status-accepter {
  border-left-color: #38a169;
}

.status-entretien {
  border-left-color: #3182ce;
}

.status-embauche {
  border-left-color: #dd6b20;
}

.status-refuser {
  border-left-color: #e53e3e;
}

.status-badge {
  padding: 0.4rem 1rem;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 600;
  text-align: center;
  text-transform: capitalize;
}

.status-badge.status-accepted {
  background-color: #c6f6d5;
  color: #2f855a;
}

.status-badge.status-refused {
  background-color: #fed7d7;
  color: #c53030;
}

.status-badge.status-entretien {
  background-color: #e9d8fd;
  color: #6b46c1;
}

/* Main Content */
.candidature-main {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #edf2f7;
}

.candidature-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
}

.candidature-header h3 {
  margin: 0;
  font-size: 1.2rem;
  color: #2d3748;
  font-weight: 600;
}

/* Candidate Info */
.candidat-info {
  display: flex;
  align-items: center;
  margin-bottom: 1.25rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #edf2f7;
}

.avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background-color: #3498db;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1.1rem;
  margin-right: 1rem;
}

.identity h4 {
  margin: 0 0 0.25rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
}

.identity p {
  margin: 0.2rem 0;
  color: #718096;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
}

.identity p i {
  margin-right: 0.5rem;
  width: 16px;
  color: #4a5568;
}

/* Détails */
.candidature-details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.detail-item {
  display: flex;
  align-items: flex-start;
}

.detail-item i {
  margin-right: 0.75rem;
  color: #4a5568;
  margin-top: 0.25rem;
  width: 16px;
}

.detail-item .label {
  font-size: 0.8rem;
  color: #718096;
  margin-bottom: 0.1rem;
}

.detail-item .value {
  color: #2d3748;
  font-size: 0.95rem;
}

/* Workflow Stepper */
.workflow-stepper {
  display: flex;
  align-items: center;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #edf2f7;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  position: relative;
}

.step:not(:last-child):after {
  content: "";
  position: absolute;
  top: 12px;
  right: -50%;
  width: 100%;
  height: 2px;
  background-color: #e2e8f0;
  z-index: 1;
}

.step-done:not(:last-child):after {
  background-color: #3498db;
}

.circle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: #e2e8f0;
  color: #718096;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  margin-bottom: 0.5rem;
  z-index: 2;
  position: relative;
}

.step-done .circle {
  background-color: #3498db;
  color: white;
}

.step-active .circle {
  background-color: #3498db;
  color: white;
  box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
}

.step .label {
  font-size: 0.7rem;
  color: #718096;
  text-align: center;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.step-done .label,
.step-active .label {
  color: #3498db;
  font-weight: 600;
}

/* Footer actions */
.candidature-actions {
  padding: 1rem 1.5rem;
  background-color: #f7fafc;
  border-top: 1px solid #edf2f7;
}

.action-buttons {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.primary-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1.25rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.primary-btn i {
  margin-right: 0.5rem;
}

.primary-btn:hover {
  background: #2980b9;
}

.primary-btn:disabled {
  background: #a0aec0;
  cursor: not-allowed;
}

.quick-status-update {
  display: flex;
  gap: 0.5rem;
}

.status-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.accept-btn {
  background-color: #c6f6d5;
  color: #2f855a;
}

.accept-btn:hover {
  background-color: #38a169;
  color: white;
}

.interview-btn {
  background-color: #bee3f8;
  color: #2b6cb0;
}

.interview-btn:hover {
  background-color: #3182ce;
  color: white;
}

.reject-btn {
  background-color: #fed7d7;
  color: #c53030;
}

.reject-btn:hover {
  background-color: #e53e3e;
  color: white;
}

/* Responsive */
@media (max-width: 768px) {
  .candidatures-container {
    padding: 1.5rem 1rem;
    margin: 1rem;
  }

  .actions-zone {
    flex-direction: column;
  }

  .filter-controls {
    width: 100%;
  }

  .stats-bar {
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
  }

  .candidature-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .action-buttons {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .quick-status-update {
    justify-content: space-between;
  }

  .workflow-stepper {
    overflow-x: auto;
    padding-bottom: 1rem;
  }

  .step .label {
    font-size: 0.65rem;
    max-width: 70px;
  }
}

.section-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  border-color: #e2e8f0;
}

/* En-têtes */
h1 {
  font-size: 2.75rem;
  margin-bottom: 2rem;
  color: #1a202c;
  font-weight: 700;
  letter-spacing: -0.5px;
  position: relative;
  padding-bottom: 0.5rem;
}
.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.header-actions h2 {
  margin: 0;
  color: #1a237e;
  font-size: 1.8rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  letter-spacing: -0.5px;
}

h1::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, #3182ce, #63b3ed);
  border-radius: 2px;
}

h2 {
  font-size: 1.85rem;
  margin-bottom: 1.25rem;
  color: #2c5282;
  font-weight: 600;
  letter-spacing: -0.3px;
}

h3 {
  font-size: 1.35rem;
  margin-bottom: 1rem;
  color: #2d3748;
  font-weight: 600;
}

h4 {
  font-size: 1.15rem;
  margin-bottom: 0.75rem;
  color: #2d3748;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.95rem;
}

/* Ligne statut */
.status-line {
  margin-top: 1.5rem;
  font-size: 1.1rem;
  padding: 0.75rem 0;
  border-top: 1px dashed #e2e8f0;
}


.status-line strong {
  color: #1a365d;
}

/* Loader */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}

.loader {
  width: 70px;
  height: 70px;
  border: 4px solid #edf2f7;
  border-top: 4px solid #3182ce;
  border-radius: 50%;
  animation: spin 1.2s cubic-bezier(0.5, 0.1, 0.5, 0.9) infinite;
  margin: 0 auto 1.5rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.loading-state p {
  color: #4a5568;
  font-size: 1.1rem;
}

/* Erreur */
.error-state {
  background: #fff5f7;
  border: 1px solid #fed7e2;
  padding: 2rem;
  border-radius: 12px;
  color: #c53030;
  margin-bottom: 2rem;
  text-align: center;
}

.btn-retry {
  margin-top: 1.5rem;
  padding: 0.8rem 1.8rem;
  background: linear-gradient(135deg, #e53e3e, #c53030);
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
}

.btn-retry:hover {
  background: linear-gradient(135deg, #c53030, #9b2c2c);
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(229, 62, 62, 0.4);
}

/* Statut */
.status-pending {
  color: #c05621;
  font-weight: 600;
  background-color: #fffaf0;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  border: 1px solid #feebc8;
}

.status-accepted {
  color: #2f855a;
  font-weight: 600;
  background-color: #f0fff4;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  border: 1px solid #c6f6d5;
}

.status-refused {
  color: #c53030;
  font-weight: 600;
  background-color: #fff5f5;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  border: 1px solid #fed7d7;
}

/* Boutons Accepter/Refuser */
.action-buttons {
  display: flex;
  gap: 1.5rem;
  margin-top: 2rem;
}
.workflow-stepper {
  display: flex;
  align-items: center;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #edf2f7;
}
.btn-accept,
.btn-refuse {
  flex: 1;
  padding: 1rem 1.2rem;
  border: none;
  border-radius: 10px;
  color: #fff;
  cursor: pointer;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  font-size: 1.05rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-accept {
  background: linear-gradient(135deg, #48bb78, #38a169);
  box-shadow: 0 4px 12px rgba(56, 161, 105, 0.2);
}

.btn-accept:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(56, 161, 105, 0.3);
  background: linear-gradient(135deg, #38a169, #2f855a);
}

.btn-refuse {
  background: linear-gradient(135deg, #f56565, #e53e3e);
  box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2);
}

.btn-refuse:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(229, 62, 62, 0.3);
  background: linear-gradient(135deg, #e53e3e, #c53030);
}

/* Entretien info */
.entretien-info {
  margin-top: 1.5rem;
  padding: 1.2rem;
  background-color: #ebf8ff;
  border-radius: 10px;
  border-left: 4px solid #3182ce;
}

.entretien-status {
  color: #2c5282;
  font-weight: 600;
  margin: 0;
  font-size: 1.05rem;
}
/* Container général */
.cv-container {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  max-width: 900px;
  margin: 2rem auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
  overflow: hidden;
  background: white;
}
.section-title1 {
  font-size: 1.4rem;
  font-weight: 600;
  color: white;
  margin-bottom: 1rem;
  border-bottom: 2px solid #3498db;
  padding-bottom: 0.5rem;
}

/* Loader */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
}
.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.loading-text {
  color: #555;
  font-size: 1.1rem;
}

/* Erreur */
.error-message {
  padding: 2rem;
  background: #fdecea;
  color: #c0392b;
  border-radius: 8px;
  text-align: center;
  margin: 2rem;
}
.retry-btn {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background: #c0392b;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.retry-btn:hover {
  background: #a93226;
}

/* Aperçu */
.cv-form.preview-form {
  display: flex;
  flex-wrap: wrap;
  min-height: 100vh;
  animation: fadeIn 0.5s;
}
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.cv-left-column,
.cv-right-column {
  padding: 2.5rem 2rem;
}
.cv-left-column {
  flex: 1;
  min-width: 260px;
  background: #0f3164;
  color: white;
}
.cv-right-column {
  flex: 2;
  min-width: 400px;
  background: #fff;
}

/* Photo de profil */
.profile-picture-container {
  text-align: center;
  margin-bottom: 1.5rem;
}
.profile-picture {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-position: center;
  background-size: cover;
  border: 4px solid #3498db;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  margin: 0 auto;
  transition: transform 0.3s;
}
.profile-picture:hover {
  transform: scale(1.05);
}

/* Titres & infos */
.cv-title-preview {
  text-align: center;
  color:white;
  font-size: 2rem;
  margin-bottom: 0.5rem;
}
.info {
  display: flex;
  align-items: center;
  font-size: 0.95rem;
  margin-bottom: 0.8rem;
}
.info a {
  color: #3498db;
  text-decoration: none;
}
.info a:hover {
  text-decoration: underline;
}

/* Sections */
.section {
  margin-bottom: 2rem;
}
.section-title {
  font-size: 1.4rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 1rem;
  border-bottom: 2px solid #3498db;
  padding-bottom: 0.5rem;
}


/* Listes */
ul {
  list-style: none;
  padding: 0;
}
ul li {
  position: relative;
  padding-left: 1.2rem;
  margin-bottom: 0.7rem;
}
ul li::before {
  content: "•";
  position: absolute;
  left: 0;
  color: #3498db;
  font-weight: bold;
}

/* Deux colonnes */
.two-columns {
  display: flex;
  gap: 2rem;
}

.form-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-top: 1px solid #eee;
}
.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: background 0.3s ease, transform 0.2s ease;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.download-btn {
  background: #20c599;
}
.download-btn:hover {
  filter: brightness(0.8);

}

.edit-btn {
  background: var(--btn-edit-bg);
  color: var(--btn-edit-color);
}
.edit-btn:hover {
  background: var(--btn-edit-hover);
  transform: translateY(-2px);
}

.btn-delete {
  background: var(--btn-delete-bg);
  color: var(--btn-delete-color);
}
.btn-delete:hover {
  background: var(--btn-delete-hover);
  transform: translateY(-2px);
}

/* Impression PDF */
.printing .cv-left-column {
  filter: brightness(0.8);
}

/* Responsive */
@media (max-width: 768px) {
  .cv-form.preview-form {
    flex-direction: column;
  }
  .two-columns {
    flex-direction: column;
  }
  .form-actions {
    flex-wrap: wrap;
  }
  .action-btn {
    flex: 1;
    text-align: center;
  }
}

/* Message du candidat */
.message-section {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background-color: #f7fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.message-section h3 {
  margin-top: 0;
  font-size: 1.2rem;
  color: #2d3748;
  margin-bottom: 1rem;
}

.message-content {
  line-height: 1.7;
  color: #4a5568;
  white-space: pre-line;
  font-style: italic;
  padding: 1rem;
  background-color: white;
  border-radius: 8px;
  border-left: 3px solid #3182ce;
}

/* Navigation */
.navigation-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 2.5rem;
}

.btn-back,
.btn-download {
  padding: 1rem 2rem;
  border: none;
  border-radius: 10px;
  color: #fff;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;

  font-size: 1.05rem;
}

.btn-back {
  background: #f5f7fa;
  color: #34495e;
  border: 2px solid #dfe6e9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.btn-back:hover {
  background: #e0e7ee;
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
.btn-download {
background: linear-gradient(135deg, #20c599, #1fae8d, #178467);

}

.btn-download:hover {
  transform: translateY(-3px);
  filter: brightness(0.8);
  box-shadow: 0 8px 20px rgba(49, 130, 206, 0.3);
}

/* Offre détails */
.offer-details h3 {
  color: #2b6cb0;
  font-size: 1.5rem;
  margin-bottom: 1.25rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e2e8f0;
}

.offer-details p {
  margin-bottom: 1rem;
  line-height: 1.7;
}

.offer-details p strong {
  color: #2d3748;
  font-weight: 600;
}

/* Candidature info */
.candidature-info h2 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.candidature-info h2::before {
  content: "📋";
  font-size: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .candidature-details-container {
    padding: 1rem;
    margin: 2rem auto;
  }

  .section-card {
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .cv-content {
    flex-direction: column;
    gap: 1.5rem;
  }

  .two-columns {
    flex-direction: column;
    gap: 1.5rem;
  }

  h1 {
    font-size: 2rem;
  }

  h2 {
    font-size: 1.5rem;
  }

  .navigation-actions {
    flex-direction: column;
    gap: 1rem;
  }

  .btn-back,
  .btn-download {
    width: 100%;
    justify-content: center;
  }

  .action-buttons {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  h1 {
    font-size: 1.75rem;
  }

  .profile-picture {
    width: 120px;
    height: 120px;
  }
}

/* Animation pour le contenu principal */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.candidature-content {
  animation: fadeIn 0.6s ease-out;
}

.candidature-content h2{
   color: #1e3a8a; /* bleu foncé proche de la capture */
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  position: relative;
  padding-bottom: 0.5rem;
  letter-spacing: -0.5px;
}
.candidature-content h2::after{
   content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 3rem; /* largeur de la barre */
  height: 4px; /* épaisseur */
  background-color: #1e3a8a; /* même bleu foncé */
  border-radius: 4px; /* arrondi */
}

/* Effet de focus pour les boutons */
button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}

/* Police pour tout le document */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");
</style>
