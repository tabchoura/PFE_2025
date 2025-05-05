<template>
  <div class="candidatures-container">
    <!-- Header avec titre et filtre -->
    <div class="header-section">
      <h1><i class="fas fa-file-alt"></i> Candidatures Reçues</h1>
      <div class="actions-zone">
        <div class="filter-controls">
          <select
            v-model="statutFilter"
            @change="filterCandidatures"
            class="status-filter"
          >
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
    <div class="stats-bar" v-if="!loading && filteredCandidatures.length > 0">
      <p>
        <span class="stats-count">{{ filteredCandidatures.length }}</span>
        candidature{{ filteredCandidatures.length > 1 ? "s" : "" }}
        {{ statutFilter ? `avec statut "${getStatusLabel(statutFilter)}"` : "au total" }}
      </p>
    </div>

    <!-- États conditionnels -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Chargement des candidatures...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-triangle"></i>
      <h3>Erreur lors du chargement des candidatures</h3>
      <p>{{ error }}</p>
      <button @click="loadCandidatures" class="retry-button">
        <i class="fas fa-sync"></i> Réessayer
      </button>
    </div>

    <div v-else-if="candidatures.length === 0" class="empty-state">
      <i class="fas fa-inbox"></i>
      <h3>Aucune candidature reçue</h3>
      <p>Les candidatures apparaîtront ici dès que vous en recevrez.</p>
    </div>

    <div v-else-if="filteredCandidatures.length === 0" class="no-results-state">
      <i class="fas fa-search"></i>
      <h3>Aucun résultat trouvé</h3>
      <p>Aucune candidature ne correspond à vos critères de recherche.</p>
      <!-- <button @click="resetFilters" class="reset-filters-btn">
        <i class="fas fa-times"></i> Réinitialiser les filtres
      </button> -->
    </div>

    <!-- Liste des candidatures -->
    <div v-else class="candidatures-list">
      <div
        v-for="c in filteredCandidatures"
        :key="c.id"
        class="candidature-card"
        :class="`status-${c.statut || 'enattente'}`"
      >
        <div class="candidature-main">
          <div class="candidature-header">
            <h3>{{ c.offre?.titre || "Offre inconnue" }}</h3>
            <div :class="['status-badge', `status-${c.statut || 'enattente'}`]">
              {{ getStatusLabel(c.statut) }}
            </div>
          </div>

          <div class="candidat-info">
            <div class="avatar">
              {{ getInitials(c.cv?.prenom, c.cv?.nom) }}
            </div>
            <div class="identity">
              <h4>{{ c.cv?.prenom || "Prénom" }} {{ c.cv?.nom || "Nom" }}</h4>
              <p class="email" v-if="c.cv?.email">
                <i class="fas fa-envelope"></i> {{ c.cv?.email }}
              </p>
              <p class="phone" v-if="c.cv?.telephone">
                <i class="fas fa-phone"></i> {{ c.cv?.telephone }}
              </p>
            </div>
          </div>

          <div class="candidature-details">
            <div class="detail-item">
              <i class="fas fa-calendar"></i>
              <div>
                <span class="label">Date de candidature</span>
                <span class="value">{{ formatDate(c.created_at) }}</span>
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
                <span class="value">{{ c.cv?.experience }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="candidature-actions">
          <div class="action-buttons">
            <button @click="voirDetails(c.id)" class="primary-btn" :disabled="!c.id">
              <i class="fas fa-eye"></i> Voir détails
            </button>
            <div class="quick-status-update">
              <!-- <button
                v-if="c.statut !== 'accepter'"
                @click="updateStatus(c.id, 'accepter')"
                class="status-btn accept-btn"
                title="Accepter"
              >
                <i class="fas fa-check"></i>
              </button> -->
              <!-- <button
                v-if="c.statut !== 'entretien'"
                @click="updateStatus(c.id, 'entretien')"
                class="status-btn interview-btn"
                title="Programmer entretien"
              >
                <i class="fas fa-calendar-check"></i>
              </button>
              <button
                v-if="c.statut !== 'refuser'"
                @click="updateStatus(c.id, 'refuser')"
                class="status-btn reject-btn"
                title="Refuser"
              >
                <i class="fas fa-times"></i>
              </button> -->
              <button
                v-if="c.statut === 'accepter'"
                @click="planifierEntretien(c.id)"
                class="primary-btn schedule-btn"
              >
                <i class="fas fa-calendar-alt" /> Planifier entretien
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

// Types
interface Cv {
  id?: number;
  prenom?: string;
  nom?: string;
  email?: string;
  telephone?: string;
  experience?: string;
}

interface Offre {
  id?: number;
  titre?: string;
  description?: string;
}

interface Candidature {
  id?: number;
  cv?: Cv;
  offre?: Offre;
  created_at?: string;
  statut?: string;
  message?: string;
}

// État
const candidatures = ref<Candidature[]>([]);
const filteredCandidatures = ref<Candidature[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const statutFilter = ref("");
const sortOption = ref("recent");

const router = useRouter();

// Formatage et utilitaires
const formatDate = (d?: string) => {
  if (!d) return "Date non spécifiée";
  try {
    return new Date(d).toLocaleDateString("fr-FR", {
      day: "numeric",
      month: "long",
      year: "numeric",
    });
  } catch {
    return "Format de date invalide";
  }
};

const getStatusLabel = (statut?: string) => {
  const labels: Record<string, string> = {
    enattente: "En attente",
    accepter: "Acceptée",
    entretien: "Entretien",
    embauche: "Embauchée",
    refuser: "Refusée",
  };
  return labels[statut || "enattente"] || "En attente";
};

const truncateText = (text?: string, maxLength = 100) => {
  if (!text) return "";
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
};

const getInitials = (prenom?: string, nom?: string) => {
  const p = prenom?.charAt(0) || "";
  const n = nom?.charAt(0) || "";
  return p + n || "??";
};

// Actions
function voirDetails(candidatureId?: number) {
  if (!candidatureId) return;
  router.push({
    name: "DetailsCandidature",
    params: { candidatureId: candidatureId.toString() },
  });
}

function filterCandidatures() {
  // Si c'est "Tous les statuts" ou valeur vide, on affiche tout
  if (!statutFilter.value || statutFilter.value === "Tous les statuts") {
    filteredCandidatures.value = [...candidatures.value];
  } else {
    filteredCandidatures.value = candidatures.value.filter(
      (c) => c.statut === statutFilter.value
    );
  }
  // sortCandidatures();
}
function planifierEntretien(candidatureId: number) {
  router.push({
    name: "Entretiens",
    params: { candidatureId }, // correspond au segment dynamique
  });
}
// function sortCandidatures() {
//   switch (sortOption.value) {
//     case "recent":
//       filteredCandidatures.value.sort((a, b) => {
//         return (
//           new Date(b.created_at || "").getTime() - new Date(a.created_at || "").getTime()
//         );
//       });
//       break;
//     case "ancien":
//       filteredCandidatures.value.sort((a, b) => {
//         return (
//           new Date(a.created_at || "").getTime() - new Date(b.created_at || "").getTime()
//         );
//       });
//       break;
//     case "name":
//       filteredCandidatures.value.sort((a, b) => {
//         const nameA = `${a.cv?.nom || ""} ${a.cv?.prenom || ""}`.toLowerCase();
//         const nameB = `${b.cv?.nom || ""} ${b.cv?.prenom || ""}`.toLowerCase();
//         return nameA.localeCompare(nameB);
//       });
//       break;
//   }
// }

// function resetFilters() {
//   statutFilter.value = "";
//   filterCandidatures();
// }

async function updateStatus(candidatureId?: number, newStatus?: string) {
  if (!candidatureId || !newStatus) return;

  try {
    await axios.patch(`/api/candidatures/${candidatureId}`, {
      statut: newStatus,
    });

    // Mise à jour locale
    const index = candidatures.value.findIndex((c) => c.id === candidatureId);
    if (index !== -1) {
      candidatures.value[index].statut = newStatus;
      filterCandidatures();
    }
  } catch (err) {
    console.error("Erreur lors de la mise à jour du statut", err);
    alert("Erreur lors de la mise à jour du statut");
  }
}

async function loadCandidatures() {
  loading.value = true;
  error.value = null;

  try {
    const { data } = await axios.get("/api/candidatures");

    if (Array.isArray(data)) {
      candidatures.value = data;
      filterCandidatures();
    } else {
      throw new Error("Format de données invalide");
    }
  } catch (err: any) {
    console.error("Erreur chargement", err);
    error.value =
      err.response?.data?.message || "Erreur lors du chargement des candidatures";
  } finally {
    loading.value = false;
  }
}

// Initialisation
onMounted(() => {
  loadCandidatures();
});
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
  background-color: #e9d8fd;
  color: #6b46c1;
}

.status-accepter {
  border-left-color: #38a169;
  background-color: #c6f6d5;
  color: #2f855a;
}

.status-entretien {
  border-left-color: #3182ce;
  background-color: #bee3f8;
  color: #2b6cb0;
}

.status-embauche {
  border-left-color: #dd6b20;
  background-color: #feebc8;
  color: #c05621;
}

.status-refuser {
  border-left-color: #e53e3e;
  background-color: #fed7d7;
  color: #c53030;
}

.status-badge {
  padding: 0.4rem 1rem;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 600;
  text-align: center;
  text-transform: capitalize;
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
}
</style>
