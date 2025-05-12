<template>
  <div class="candidatures-container">
    <!-- Header avec titre et filtre -->
    <div class="header-section">
      <h1><i class="fas fa-file-alt"></i> Candidatures Reçues</h1>
      <div class="actions-zone">
        <div class="filter-controls">
          <select v-model="filtreStatut" class="status-filter">
            <option value="">Tous les statuts</option>
            <option value="enattente">En attente</option>
            <option value="accepter">Acceptée</option>
            <option value="entretien">Entretien</option>
            <option value="embauche">Embauchée</option>
            <option value="refuser">Refusée</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Résumé du nombre de candidatures -->
    <div class="stats-bar" v-if="!loading && candidaturesFiltrees.length > 0">
      <p>
        <span class="stats-count">{{ candidaturesFiltrees.length }}</span>
        candidature{{ candidaturesFiltrees.length > 1 ? "s" : "" }}
        {{ filtreStatut ? `avec statut "${labels(filtreStatut)}"` : "au total" }}
      </p>
    </div>

    <!-- États conditionnels -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Chargement des candidatures...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-circle error-icon"></i>
      <p>{{ error }}</p>
      <button @click="getCandidatures" class="btn-retry">
        <i class="fas fa-redo-alt"></i> Réessayer
      </button>
    </div>

    <div v-else-if="candidatures.length === 0" class="empty-state">
      <i class="fas fa-inbox"></i>
      <h3>Aucune candidature reçue</h3>
      <p>Les candidatures apparaîtront ici dès que vous en recevrez.</p>
    </div>
    <div v-else-if="candidaturesFiltrees.length === 0" class="empty-state">
      <i class="fas fa-inbox empty-icon"></i>
      <p>
        Aucune candidature
        {{ filtreStatut ? `avec le statut "${labels(filtreStatut)}"` : "" }} pour le
        moment
      </p>
    </div>

    <!-- Liste des candidatures -->
    <div v-else class="candidatures-list">
      <div
        v-for="c in candidaturesFiltrees"
        :key="c.id"
        class="candidature-card"
        :class="`status-${c.statut || 'enattente'}`"
      >
        <div class="candidature-main">
          <div class="candidature-header">
            <h3>{{ c.offre?.titre || "Offre inconnue" }}</h3>
            
            <!-- Affichage du statut IA avec condition -->
            <div
              :class="[ 
                'status-badge', 
                `status-${(c.status_ia || '').toLowerCase().trim()}` 
              ]"
            >
              <span>
                {{ c.status_ia === 'accepted' ? "CV pertinent ✅" : 
                   c.status_ia === 'rejected' ? "CV non pertinent ❌" :
                   c.status_ia || "Statut inconnu" }}
              </span>
            </div>
          </div>

          <div class="candidat-info">
            <div class="avatar">
              {{ getInitials(c.cv?.prenom, c.cv?.nom) }}
            </div>
            <div class="identity">
              <h4>{{ c.cv?.prenom || "Prénom" }} {{ c.cv?.nom || "Nom" }}</h4>
              <p class="email" v-if="c.cv?.email">
                <i class="fas fa-envelope"></i> {{ c.cv.email }}
              </p>
              <p class="phone" v-if="c.cv?.telephone">
                <i class="fas fa-phone"></i> {{ c.cv.telephone }}
              </p>
            </div>
          </div>

          <div class="candidature-details">
            <div class="detail-item">
              <i class="fas fa-calendar"></i>
              <div>
                <span class="label">Date de candidature</span>
                <span class="value">{{ formatDate(c.created_at) }}</span>
                <br />
              </div>
              <div>
                <span class="label">Date de l'entretien</span>
                <span class="value">{{ formatDateTime12h(c.date_entretien) }}</span>
              </div>
            </div>
            <div class="detail-item" v-if="c.message">
              <i class="fas fa-comment"></i>
              <div>
                <span class="label">Message</span>
                <span class="value">{{ truncateText(c.message, 100) }}</span>
              </div>
            </div>
            <div class="detail-item" v-if="c.cv?.experience">
              <i class="fas fa-briefcase"></i>
              <div>
                <span class="label">Expérience</span>
                <span class="value">{{ c.cv.experience }}</span>
              </div>
            </div>
          </div>

          <!-- Workflow stepper dynamique basé sur le statut IA -->
          <div class="workflow-stepper">
            <div
              v-for="(step, i) in getSteps(c.status_ia)"
              :key="step.key"
              class="step"
              :class="{
                'step-done': stepOrder(c.statut) > i,
                'step-active': stepOrder(c.statut) === i,
              }"
            >
              <span class="circle">{{ i + 1 }}</span>
              <span class="label">{{ step.label }}</span>
            </div>
          </div>

          <!-- Bouton "Planifier entretien" uniquement si accepté -->
          <div v-if="c.status_ia === 'accepted'">
            <button @click="planifierEntretien(c)" class="primary-btn">
              <i class="fas fa-calendar-alt"></i> Planifier entretien
            </button>
          </div>
        </div>

        <div class="candidature-actions">
          <div class="action-buttons">
            <button @click="voirDetails(c)" class="primary-btn" :disabled="!c.id">
              <i class="fas fa-eye"></i> Voir détails
            </button>
            <div class="quick-status-update">
              <button
                v-if="c.statut === 'entretien'"
                class="primary-btn schedule-btn"
                @click="planifierEntretien(c.id, selectedDate)"
              >
                <i class="fas fa-calendar-alt"></i> Planifier entretien
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const candidatures = ref([]);
const loading = ref(true);
const error = ref(null);
const filtreStatut = ref("");
const selectedDate = ref("");
const router = useRouter();


const formatDate = (d) =>
  d
    ? new Date(d).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
      })
    : "Date non spécifiée";

const formatDateTime12h = (dateString) => {
  if (!dateString) return "—";
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "numeric",
    minute: "numeric",
    hour12: true,
  }).format(date);
};

const truncateText = (text, length = 100) =>
  text && text.length > length ? text.slice(0, length) + "..." : text;

const getInitials = (prenom, nom) =>
  (prenom || "").charAt(0) + (nom || "").charAt(0) || "??";

const labels = (statut) => {
  const map = {
    enattente: "En attente",
    accepter: "Acceptée",
    entretien: "Entretien",
    embauche: "Embauchée",
    refuser: "Refusée",
  };
  return map[statut] || "—";
};

const getSteps = (status_ia) => {
  if (status_ia === "accepted") {
    return [
      { key: "accepter", label: "Acceptée" },
      { key: "entretien", label: "Entretien" },
      { key: "embauche", label: "Embauchée" },
    ];
  } else if (status_ia === "rejected") {
    return [
      { key: "refuser", label: "Refusée" }
    ];
  }
  return [
    { key: "enattente", label: "En attente" },
    { key: "accepter", label: "Acceptée" },
    { key: "entretien", label: "Entretien" },
    { key: "embauche", label: "Embauchée" },
  ];
};

const stepOrder = (statut) => {
  const order = {
    enattente: 0,
    accepter: 1,
    entretien: 2,
    embauche: 3,
    refuser: 1,
  };
  return order[statut] ?? 0;
};

function voirDetails(candidature) {
  if (!candidature.id) return;

  localStorage.setItem("current_candidature", JSON.stringify(candidature));

  router.push({ name: "DetailsCandidature", params: { candidatureId: candidature.id } });
}

const candidaturesFiltrees = computed(() => {
  if (!filtreStatut.value) return candidatures.value;
  return candidatures.value.filter((c) => c.statut === filtreStatut.value);
});

// Fonction pour planifier l'entretien
async function planifierEntretien(candidature) {
  if (!candidature.id) return;  // Vérifie si l'ID de la candidature existe

  // Sauvegarde de la candidature actuelle dans le localStorage (facultatif)
  localStorage.setItem("current_candidature", JSON.stringify(candidature));

  // Passe à la route 'planifier-entretien' avec l'ID de la candidature
  router.push({ name: "PlanifierEntretien", params: { candidatureId: candidature.id } });
}



const getCandidatures = async () => {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get("/api/candidatures");

    candidatures.value = Array.isArray(data)
      ? data.map((c) => {
          const iaStatus = c.status_ia || "";
          let displayStatus = c.statut || "enattente";

          if (iaStatus.toLowerCase().trim() === "accepted") {
            displayStatus = "accepter";
          } else if (iaStatus.toLowerCase().trim() === "rejected") {
            displayStatus = "refuser";
          }

          return {
            ...c,
            status_ia_original: iaStatus,
            statut: displayStatus, 
          };
        })
      : [];
  } catch (e) {
    console.error("Erreur de chargement des candidatures:", e);
    error.value = e.response?.data?.message || "Erreur lors du chargement";
  } finally {
    loading.value = false;
  }
};

onMounted(getCandidatures);
</script>



<style scoped>
.candidatures-container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 2rem;
  background: #f8fafc;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

/* Header Section */
.header-section {
  margin-bottom: 1.5rem;
}

h1 {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
  color: #1a202c;
  font-size: 2rem;
  font-weight: 700;
}

h1 i {
  margin-right: 0.75rem;
  color: #3498db;
}

.actions-zone {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
}

.filter-controls {
  display: flex;
  gap: 0.75rem;
  width: 100%;
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

.status-badge.status-rejected {
  background-color: #fed7d7;
  color: #c53030;
}

.status-badge.status- {
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
</style>
