<template>
  <div class="page-wrapper">
    <div class="candidatures-container">
      <!-- Header with title and filter -->
      <div class="header-actions mescandidatures">
        <h2><i class="fas fa-user-check"></i> Candidatures reçues</h2>
        <div class="actions-zone">
          <div class="filter-controls">
            <select
              v-model="filtreStatut"
              class="status-filter"
              aria-label="Filtrer par statut"
            >
              <option value="">Tous les statuts</option>
              <option value="accepter">Acceptée</option>
              <option value="entretien">Entretien</option>
              <option value="embauche">Embauchée</option>
              <option value="refuser">Refusée</option>
            </select>
          </div>
        </div>
      </div>

      <div v-if="loading" class="loading-state" role="status" aria-live="polite">
        <div class="spinner"></div>
        <p>Chargement des candidatures…</p>
      </div>

      <div v-else-if="error" class="error-state" role="alert">
        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
        <p>{{ error }}</p>
        <button
          @click="getCandidatures"
          class="retry-button"
          aria-label="Réessayer le chargement des candidatures"
        >
          <i class="fas fa-redo-alt" aria-hidden="true"></i> Réessayer
        </button>
      </div>

      <div
        v-else-if="!candidatures.length"
        class="empty-state"
        role="region"
        aria-live="polite"
      >
        <i class="fas fa-inbox" aria-hidden="true"></i>
        <h3>Aucune candidature reçue</h3>
        <p>Les candidatures apparaîtront ici dès que vous en recevrez.</p>
      </div>

      <div v-else>
        <div class="stats-bar" v-if="candidaturesFiltrees.length">
          <p>
            <span class="stats-count">{{ candidaturesFiltrees.length }}</span>
            candidature{{ candidaturesFiltrees.length > 1 ? "s" : "" }}
            {{ filtreStatut ? `avec statut "${labels(filtreStatut)}"` : "au total" }}
          </p>
        </div>

        <div class="candidatures-list">
          <div
            v-for="c in candidaturesFiltrees"
            :key="c.id"
            class="candidature-card"
            :class="`status-${c.statut}`"
          >
            <div class="candidature-main">
              <div class="candidature-header">
                <h3>{{ c.offre?.titre || "Offre inconnue" }}</h3>
                <div :class="['status-badge', `status-badge-${c.statut}`]">
                  <span>
                    {{ labels(c.statut) }}
                    {{
                      c.statut === "accepter"
                        ? "✅"
                        : c.statut === "refuser"
                        ? "❌"
                        : c.statut === "entretien"
                        ? "🗓️"
                        : c.statut === "embauche"
                        ? "🎉"
                        : ""
                    }}
                  </span>
                </div>
              </div>

              <div class="candidat-info">
                <div
                  class="avatar"
                  :aria-label="`Initiales du candidat ${c.cv?.prenom} ${c.cv?.nom}`"
                >
                  {{ getInitials(c.cv?.prenom, c.cv?.nom) }}
                </div>
                <div class="identity">
                  <h4>{{ c.cv?.prenom }} {{ c.cv?.nom }}</h4>
                  <p v-if="c.cv?.email">
                    <i class="fas fa-envelope" aria-hidden="true"></i> {{ c.cv.email }}
                  </p>
                  <p v-if="c.cv?.telephone">
                    <i class="fas fa-phone" aria-hidden="true"></i> {{ c.cv.telephone }}
                  </p>
                </div>
              </div>

              <div class="candidature-details">
                <div class="detail-item">
                  <i class="fas fa-calendar" aria-hidden="true"></i>
                  <div>
                    <span class="label">Date de candidature</span>
                    <span class="value">{{ formatDate(c.created_at) }}</span>
                  </div>
                </div>
                <div class="detail-item" v-if="c.date_entretien">
                  <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                  <div class="card-info-row">
                    <i class="fas fa-handshake" aria-hidden="true"></i>
                    <p>
                      <strong>Entretien :</strong>
                      {{ formatDateTime12h(c.date_entretien) }}
                    </p>
                  </div>
                </div>
                <div class="detail-item" v-if="c.message">
                  <i class="fas fa-comment" aria-hidden="true"></i>
                  <div>
                    <span class="label">Message</span>
                    <span class="value">{{ truncateText(c.message, 100) }}</span>
                  </div>
                </div>
              </div>

              <div
                class="workflow-stepper"
                role="list"
                aria-label="Étapes du processus de candidature"
              >
                <div
                  v-for="(step, i) in getSteps(c.statut)"
                  :key="step.key"
                  class="step"
                  :class="{
                    'step-done': stepOrder(c.statut) > i,
                    'step-active': stepOrder(c.statut) === i,
                  }"
                  role="listitem"
                >
                  <span class="circle">{{ i + 1 }}</span>
                  <span class="label">{{ step.label }}</span>
                </div>
              </div>

              <div v-if="c.statut === 'accepter'" class="actions">
                <button
                  @click="planifierEntretien(c)"
                  class="primary-btn"
                  aria-label="Planifier un entretien"
                >
                  <i class="fas fa-calendar-alt" aria-hidden="true"></i> Planifier
                  entretien
                </button>
              </div>
            </div>

            <div class="candidature-actions">
              <button
                @click="voirDetails(c)"
                class="primary-btn"
                aria-label="Voir les détails de la candidature"
              >
                <i class="fas fa-eye" aria-hidden="true"></i> Voir détails
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const candidatures = ref([]);
const loading = ref(true);
const error = ref(null);
const filtreStatut = ref("");
const router = useRouter();

const formatDate = (d) =>
  d
    ? new Date(d).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
      })
    : "—";

const formatDateTime12h = (d) => {
  if (!d) return "—";
  const dt = new Date(d);
  return new Intl.DateTimeFormat("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  }).format(dt);
};

const truncateText = (t, len = 100) =>
  t && t.length > len ? `${t.slice(0, len)}...` : t || "";

const getInitials = (first, last) =>
  `${(first || "").charAt(0)}${(last || "").charAt(0)}`.toUpperCase();

const labels = (statut) =>
  ({
    enattente: "En attente",
    accepter: "Acceptée",
    entretien: "Entretien",
    embauche: "Embauchée",
    refuser: "Refusée",
  }[statut] || "—");

const getSteps = (statut) => {
  if (statut === "refuser") {
    return [{ key: "refuser", label: "Refusée" }];
  }
  return [
    { key: "accepter", label: "Acceptée" },
    { key: "entretien", label: "Entretien" },
    { key: "embauche", label: "Embauchée" },
  ];
};

const stepOrder = (statut) =>
  ({
    accepter: 0,
    entretien: 1,
    embauche: 2,
    refuser: 0,
  }[statut] || 0);

const voirDetails = (c) => {
  router.push({ name: "DetailsCandidature", params: { candidatureId: c.id } });
};

const planifierEntretien = (c) => {
  router.push({ name: "PlanifierEntretien", params: { candidatureId: c.id } });
};

const candidaturesFiltrees = computed(() => {
  if (!filtreStatut.value) return candidatures.value;
  return candidatures.value.filter((c) => c.statut === filtreStatut.value);
});

const getCandidatures = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get("/api/candidatures");
    const data = response.data;
    candidatures.value = Array.isArray(data)
      ? data.map((c) => {
          if (c.status_ia === "rejected") {
            c.statut = "refuser";
          } else if (c.status_ia === "accepted") {
            c.statut = "accepter";
          } else {
            c.statut = "enattente";
          }
          return c;
        })
      : [];
  } catch (e) {
    console.error(e);
    error.value = e.response?.data?.message || "Erreur lors du chargement";
  } finally {
    loading.value = false;
  }
};

onMounted(getCandidatures);
</script>

<style scoped>
.page-wrapper {
  background: linear-gradient(135deg, #f5f9ff, #ebf3ff);
  min-height: 100vh;
  padding: 2rem;
  font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
}
.candidatures-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 10px 15px -3px rgba(0, 0, 0, 0.08);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mescandidatures h2 {
  color: #1e3a8a; /* bleu foncé proche de la capture */
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  position: relative;
  padding-bottom: 0.5rem;
  letter-spacing: -0.5px;
}

.candidatures-container:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 20px 40px rgba(0, 0, 0, 0.15);
}

.mescandidatures h2::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 3rem; /* largeur de la barre */
  height: 4px; /* épaisseur */
  background-color: #1e3a8a; /* même bleu foncé */
  border-radius: 4px; /* arrondi */
}
/* Header Section */
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
.header-actions h2 i {
  margin-right: 14px;
  color: #3f51b5;
  font-size: 1.6rem;
  transition: transform 0.3s ease;
}

.header-actions:hover h2 i {
  transform: scale(1.1);
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

.primary-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #5090f6, #2563eb, #1d4ed8);
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

  .candidature-actions {
    display: flex;
    justify-content: flex-start;
    padding: 1rem 1rem;
  }

  .primary-btn {
    width: 100%;
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
