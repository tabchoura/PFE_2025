<template>
  <div class="candidatures-container">
    <!-- Header avec titre et filtre -->
    <div class="header-actions">
      <h2><i class="fas fa-user-check"></i> Mes candidatures</h2>
      <select v-model="filtreStatut" class="statut-filter">
        <option value="">Tous les statuts</option>
        <option value="enattente">En attente</option>
        <option value="accepter">Acceptée</option>
        <option value="entretien">Entretien</option>
        <option value="embauche">Embauchée</option>
        <option value="refuser">Refusée</option>
      </select>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Chargement des candidatures...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-circle error-icon"></i>
      <p>{{ error }}</p>
      <button @click="getCandidatures" class="btn-retry">
        <i class="fas fa-redo-alt"></i> Réessayer
      </button>
    </div>

    <!-- Empty state -->
    <div v-else-if="candidaturesFiltrees.length === 0" class="empty-state">
      <i class="fas fa-inbox empty-icon"></i>
      <p>
        Aucune candidature
        {{ filtreStatut ? `avec le statut "${labels[filtreStatut]}"` : "" }} pour le
        moment
      </p>
    </div>

    <!-- Candidatures list -->
    <ul v-else class="candidature-grid">
      <li
        v-for="c in candidaturesFiltrees"
        :key="c.id"
        class="candidature-item"
      >
        <div class="candidature-header">
          <h3 class="title-offre">{{ c.offre?.titre || "Offre inconnue" }}</h3>
          
          <!-- Affichage du statut IA avec condition -->
          <div
            :class="[ 
              'status-badge', 
              `status-${(c.status_ia || '').toLowerCase().trim()}` 
            ]"
          >
            <span>
              {{ c.status_ia === 'accepted' ? "Accepté ✅" : 
                 c.status_ia === 'rejected' ? "Refusée ❌" :
                 c.status_ia || "Statut inconnu" }}
            </span>
          </div>
        </div>

        <div class="candidature-card-body">
          <div class="card-info-row">
            <i class="fas fa-info-circle"></i>
            <p>
              <strong>Description :</strong>
              {{ truncateText(c.offre?.description, 100) || "—" }}
            </p>
          </div>
          <div class="card-info-row">
            <i class="fas fa-euro-sign"></i>
            <p><strong>Salaire :</strong> {{ c.offre?.salaire || "Non spécifié" }}</p>
          </div>
          <div class="card-info-row">
            <i class="fas fa-comment"></i>
            <p>
              <strong>Message :</strong>
              {{ truncateText(c.message, 120) || "Aucun message" }}
            </p>
          </div>
          <div class="card-info-row">
            <i class="fas fa-calendar"></i>
            <p><strong>Date :</strong> {{ formatDate(c.created_at) }}</p>
          </div>
        </div>
        <div class="card-info-row">
          <i class="fas fa-comment"></i>
          <p>
            <strong>Entretien :</strong>
            {{ formatDateTime12h(c.date_entretien) || "Aucun entretien" }}
          </p>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";

const candidatures = ref([]);
const loading = ref(true);
const error = ref(null);
const filtreStatut = ref("");

// Status labels for display
const labels = {
  enattente: "En attente",
  accepter: "Acceptée",
  entretien: "Entretien",
  embauche: "Embauchée",
  refuser: "Refusée",
};

/**
 * Format date in a user-friendly format
 */
function formatDate(dateString) {
  if (!dateString) return "—";
  try {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("fr-FR", {
      day: "numeric",
      month: "long",
      year: "numeric",
    }).format(date);
  } catch (e) {
    return dateString;
  }
}

function formatDateTime12h(dateString) {
  if (!dateString) return "—";
  const date = new Date(dateString);

  // Options pour Intl.DateTimeFormat
  const options = {
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "numeric",
    minute: "numeric",
    hour12: true, // active le format 12 h avec AM/PM
  };

  // Formatter en fr-FR (mais avec hour12: true pour avoir AM/PM)
  return new Intl.DateTimeFormat("fr-FR", options).format(date);
}

/**
 * Truncate text with ellipsis if it exceeds max length
 */
const truncateText = (text, max) => {
  if (!text) return "";
  return text.length > max ? text.slice(0, max) + "…" : text;
};

/**
 * Filter candidatures based on selected status
 */
const candidaturesFiltrees = computed(() => {
  if (!filtreStatut.value) return candidatures.value;
  return candidatures.value.filter((c) => c.statut === filtreStatut.value);
});

/**
 * Fetch candidatures from API
 */
async function getCandidatures() {
  loading.value = true;
  error.value = null;

  try {
    // Init CSRF cookie pour Laravel Sanctum
    await axios.get("/sanctum/csrf-cookie");

    // Récupère les candidatures
    const { data } = await axios.get("/api/mescandidatures", {
      withCredentials: true,
    });

    // On s’assure que data est un tableau, sinon on remet un tableau vide
    candidatures.value = Array.isArray(data)
      ? data.map((c) => {
          // Si le statut IA est "rejected", on met "refuser" dans c.statut
          if (c.status_ia === "rejected") {
            c.statut = "refuser"; // On assigne "refuser" dans c.statut
          } else if (c.status_ia === "accepted") {
            c.statut = "accepter"; // On assigne "accepter" dans c.statut
          } else {
            c.statut = "enattente"; // Sinon, le statut est "enattente"
          }
          return c;
        })
      : [];
  } catch (e) {
    console.error("Erreur lors du chargement :", e);
    error.value =
      e.response?.status === 401
        ? "Vous devez être connecté pour voir vos candidatures."
        : e.response?.data?.message || "Erreur lors du chargement des candidatures.";
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  // Fetch data
  getCandidatures();
});
</script>

<style scoped>
/* Styles pour le conteneur des candidatures */
.candidatures-container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 2rem;
  background: #f8fafc;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  overflow: hidden;
}

/* Header */
.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.header-actions h2 {
  margin: 0;
  color: #333;
  font-size: 1.8rem;
  font-weight: 700;
  display: flex;
  align-items: center;
}

.header-actions h2 i {
  margin-right: 0.75rem;
  color: #3498db;
}

.statut-filter {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-weight: 500;
  color: #333;
  background-color: #fff;
  font-size: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.statut-filter:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

/* Candidatures grid */
.candidature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  padding: 0;
  margin: 0;
  list-style: none;
}

/* Candidature cards */
.candidature-item {
  display: flex;
  flex-direction: column;
  background-color: #fff;
  border-radius: 10px;
  border: 1px solid #eaeaea;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.candidature-item:hover {
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  transform: translateY(-4px);
}

/* Status badges */
.statut-badge {
  display: inline-block;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 600;
  text-align: center;
  white-space: nowrap;
  text-transform: capitalize;
  margin-left: 0.75rem;
}

.statut-enattente {
  background-color: #f5f3ff;
  color: #7c3aed;
}

.statut-accepter {
  background-color: #ecfdf5;
  color: #047857;
}

.statut-entretien {
  background-color: #eff6ff;
  color: #1d4ed8;
}

.statut-embauche {
  background-color: #fef3c7;
  color: #b45309;
}

.statut-refuser {
  background-color: #fef2f2;
  color: #b91c1c;
}

/* Card body */
.candidature-card-body {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Card info row */
.card-info-row {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
}

.card-info-row i {
  margin-right: 0.5rem;
  color: #6b7280;
}

.card-info-row p {
  margin: 0;
  color: #444;
  font-size: 1rem;
}

/* Workflow stepper */
.workflow-stepper {
  display: flex;
  align-items: center;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #eaeaea;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  position: relative;
}

.step:not(:last-child)::after {
  content: "";
  position: absolute;
  width: calc(100% - 30px);
  height: 2px;
  background-color: #ddd;
  top: 13px;
  left: calc(50% + 15px);
  z-index: 1;
}

.step-done:not(:last-child)::after {
  background-color: #3498db;
}

.circle {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background-color: #fff;
  border: 2px solid #ddd;
  color: #888;
  font-size: 0.9rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
  z-index: 2;
}

.step-done .circle {
  background-color: #3498db;
  border-color: #3498db;
  color: #fff;
}

.step-active .circle {
  border-color: #3498db;
  color: #3498db;
  font-weight: bold;
}

.label {
  font-size: 0.85rem;
  color: #666;
  text-align: center;
}

.step-done .label,
.step-active .label {
  color: #3498db;
  font-weight: 500;
}

/* Card footer */
.candidature-card-footer {
  padding: 1rem;
  border-top: 1px solid #eaeaea;
  text-align: center;
}

.voir-button {
  width: 100%;
  padding: 0.75rem 1rem;
  background-color: #4a6cf7;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.voir-button:hover {
  background-color: #3a57d5;
}

.voir-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

/* Action buttons */
.primary-btn {
  padding: 0.75rem 1.25rem;
  background-color: #3498db;
  color: white;
  border-radius: 6px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
  margin-top: 1rem;
}

.primary-btn:hover {
  background-color: #2980b9;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .candidatures-container {
    padding: 1rem;
  }

  .candidature-grid {
    grid-template-columns: 1fr;
  }

  .header-actions {
    flex-direction: column;
    align-items: flex-start;
  }

  .statut-filter {
    margin-top: 1rem;
    width: 100%;
  }

  .workflow-stepper {
    overflow-x: auto;
  }
}
</style>
