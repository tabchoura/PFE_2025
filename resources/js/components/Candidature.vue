<template>
  <div class="candidatures-container">
    <!-- Header avec titre et filtre -->
    <div class="header-actions">
      <h2><i class="fas fa-user-check"></i> Mes candidatures</h2>
      <select v-model="filtreStatut" class="statut-filter">
        <option value="">Tous les statuts</option>
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
          
          <!-- Affichage du statut IA avec correction -->
          <div
            :class="[
              'status-badge',
              `statut-${c.status_ia}`
            ]"
          >
            <span>
              {{ labels[c.status_ia] || "Statut inconnu" }}
              {{ c.status_ia === 'accepter' ? "✅" : 
                 c.status_ia === 'refuser' ? "❌" :
                 c.status_ia === 'entretien' ? "🗓️" :
                 c.status_ia === 'embauche' ? "🎉" :""
              }}
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
          
          <!-- Affichage amélioré de la date d'entretien -->
          <div v-if="c.date_entretien" class="card-info-row">
            <i class="fas fa-handshake"></i>
            <p>
              <strong>Entretien :</strong>
              {{ formatDateTime12h(c.date_entretien) }}
            </p>
          </div>
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
  accepter: "Acceptée",
  entretien: "Entretien",
  embauche: "Embauchée",
  refuser: "Refusée",
  enattente: "En attente"
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

/**
 * Format date and time with 12h format
 */
function formatDateTime12h(dateString) {
  if (!dateString) return "—";
  try {
    const date = new Date(dateString);

    // Options pour Intl.DateTimeFormat
    const options = {
      day: "numeric",
      month: "long",
      year: "numeric",
      hour: "numeric",
      minute: "numeric",
      hour12: true, // active le format 12h avec AM/PM
    };

    // Formatter en fr-FR (mais avec hour12: true pour avoir AM/PM)
    return new Intl.DateTimeFormat("fr-FR", options).format(date);
  } catch (e) {
    return dateString;
  }
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
  return candidatures.value.filter((c) => c.status_ia === filtreStatut.value);
});

async function getCandidatures() {
  loading.value = true;
  error.value = null;

  try {
    // Init CSRF cookie pour Laravel Sanctum
    await axios.get("/sanctum/csrf-cookie");

    // Récupère les candidatures
    const response  = await axios.get("/api/mescandidatures", {
      withCredentials: true,
    });
    const data=response.data;

    // On s'assure que data est un tableau, sinon on remet un tableau vide
    candidatures.value = Array.isArray(data)
      ? data.map((c) => {
        
          // Traitement des statuts
          if (c.status_ia === "rejected") {
            c.status_ia = "refuser"; 
          } else if (c.status_ia === "accepted") {
            c.status_ia = "accepter";
          } else {
            c.status_ia = "enattente"; 
          }
          
          // Pas de modification du status_ia basé sur la date d'entretien
          // Nous utiliserons le filtrage pour différencier les affichages
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

/* Ajout de style pour l'en-tête de la carte (manquant) */
.candidature-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  background-color: #f9fafb;
  border-bottom: 1px solid #eaeaea;
}

.title-offre {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 600;
  color: #222;
}

/* Status badges */
.status-badge {
  display: inline-block;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 600;
  text-align: center;
  white-space: nowrap;
  text-transform: capitalize;
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
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.card-info-row i {
  margin-right: 0.5rem;
  margin-top: 0.25rem;
  color: #6b7280;
  min-width: 1rem;
}

.card-info-row p {
  margin: 0;
  color: #444;
  font-size: 1rem;
  flex: 1;
}

/* Loading state */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 0;
  color: #666;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error state */
.error-state {
  text-align: center;
  padding: 3rem 0;
  color: #e74c3c;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.btn-retry {
  margin-top: 1rem;
  padding: 0.75rem 1.5rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-retry:hover {
  background-color: #2980b9;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 3rem 0;
  color: #7f8c8d;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #bdc3c7;
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
}
</style>