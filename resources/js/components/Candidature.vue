<template>
  <div class="page-wrapper">
    <div class="candidatures-container">
      <!-- Header avec titre et filtre -->
      <div class="header-actions mescandidatures">
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
        <li v-for="c in candidaturesFiltrees" :key="c.id" class="candidature-item">
          <div class="candidature-header">
            <h3 class="title-offre">{{ c.offre?.titre || "Offre inconnue" }}</h3>

            <!-- Affichage du statut IA avec correction -->
            <div :class="['status-badge', `statut-${c.status_ia}`]">
              <span>
                {{ labels[c.status_ia] || "Statut inconnu" }}
                {{
                  c.status_ia === "accepter"
                    ? "✅"
                    : c.status_ia === "refuser"
                    ? "❌"
                    : c.status_ia === "entretien"
                    ? "🗓️"
                    : c.status_ia === "embauche"
                    ? "🎉"
                    : ""
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
  enattente: "En attente",
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
    const response = await axios.get("/api/mescandidatures", {
      withCredentials: true,
    });
    const data = response.data;

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
.mescandidatures h2 {
  color: #1e3a8a; /* bleu foncé proche de la capture */
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  position: relative;
  padding-bottom: 0.5rem;
  letter-spacing: -0.5px;
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

/* === BASE STYLES === */
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

.candidatures-container:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 20px 40px rgba(0, 0, 0, 0.15);
}
/* === HEADER STYLES === */
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

.statut-filter {
  padding: 10px 18px;
  border-radius: 10px;
  border: 2px solid #e0e7ff;
  font-size: 1rem;
  font-weight: 500;
  color: #374151;
  background: white;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
  appearance: none;
  background-image: linear-gradient(45deg, transparent 50%, #3f51b5 50%),
    linear-gradient(135deg, #3f51b5 50%, transparent 50%);
  background-position: calc(100% - 20px) calc(1em + 2px),
    calc(100% - 15px) calc(1em + 2px);
  background-size: 5px 5px, 5px 5px;
  background-repeat: no-repeat;
}

.statut-filter:hover {
  border-color: #a5b4fc;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.statut-filter:focus {
  outline: none;
  border-color: #3f51b5;
  box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.15), 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

/* === CANDIDATURE GRID === */
.candidature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
  padding: 0;
  margin: 0;
  list-style: none;
}

/* === CANDIDATURE CARD === */
.candidature-item {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #e0e7ff;
  position: relative;
}

.candidature-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
  border-color: #cbd5e1;
}
.candidature-item::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #3f51b5, #7986cb);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.candidature-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  border-color: #c7d2fe;
}

.candidature-item:hover::before {
  opacity: 1;
}

.candidature-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 1rem;
  background: #f8fafc;
  border-bottom: 1px solid #e0e7ff;
  transition: background 0.3s ease;
}

.candidature-header::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 100%;
  background: linear-gradient(90deg, rgba(63, 81, 181, 0.03), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}
.candidature-item:hover .candidature-header {
  background: #f0f4ff;
}

.title-offre {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.4;
  flex: 1;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-box-orient: vertical;
}

/* === STATUS BADGES === */
.status-badge {
  padding: 6px 14px;
  border-radius: 24px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: capitalize;
  margin-left: 12px;
  white-space: nowrap;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.candidature-item:hover .status-badge {
  transform: scale(1.05);
}

.statut-enattente {
  background-color: #e0e7ff;
  color: #3730a3;
}

.statut-accepter {
  background-color: #d1fae5;
  color: #065f46;
}

.statut-entretien {
  background-color: #dbeafe;
  color: #1e40af;
}

.statut-embauche {
  background-color: #fef3c7;
  color: #92400e;
}

.statut-refuser {
  background-color: #fee2e2;
  color: #991b1b;
}

/* === CARD BODY === */
.candidature-card-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.card-info-row {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.card-info-row i {
  color: #4f46e5;
  margin-top: 3px;
  font-size: 1.1rem;
  min-width: 20px;
  transition: color 0.3s ease;
}

.candidature-item:hover .card-info-row i {
  color: #6366f1;
}

.card-info-row p {
  margin: 0;
  color: #4b5563;
  font-size: 0.95rem;
  line-height: 1.6;
  flex: 1;
}

.card-info-row strong {
  color: #1f2937;
  font-weight: 600;
}

/* === STATE STYLES === */
.loading-state,
.error-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
  background: #f8fafc;
  border-radius: 12px;
  margin: 2rem 0;
}

.loading-state {
  color: #4b5563;
}

.error-state {
  color: #dc2626;
}

.empty-state {
  color: #64748b;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 5px solid rgba(63, 81, 181, 0.1);
  border-top-color: #4f46e5;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1.5rem;
  position: relative;
}

.spinner::after {
  content: "";
  position: absolute;
  top: -5px;
  left: -5px;
  right: -5px;
  bottom: -5px;
  border: 5px solid transparent;
  border-top-color: #818cf8;
  border-radius: 50%;
  animation: spin 1.5s linear infinite;
}

.error-icon,
.empty-icon {
  font-size: 3rem;
  margin-bottom: 1.5rem;
  transition: transform 0.3s ease;
}

.error-icon {
  color: #ef4444;
}

.empty-icon {
  color: #94a3b8;
}

.error-state:hover .error-icon,
.empty-state:hover .empty-icon {
  transform: scale(1.1);
}

.btn-retry {
  margin-top: 1.5rem;
  padding: 0.85rem 1.75rem;
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1);
}

.btn-retry:hover {
  background: linear-gradient(135deg, #6366f1, #818cf8);
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2),
    0 4px 6px -2px rgba(79, 70, 229, 0.05);
}

.btn-retry:active {
  transform: translateY(0);
}

/* === ANIMATIONS === */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* === RESPONSIVE === */
@media (max-width: 1024px) {
  .candidature-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }
}

@media (max-width: 768px) {
  .page-wrapper {
    padding: 1.25rem;
  }

  .candidatures-container {
    padding: 2rem;
  }

  .header-actions {
    flex-direction: column;
    align-items: stretch;
    gap: 1.25rem;
  }

  .statut-filter {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .candidatures-container {
    padding: 1.5rem;
    border-radius: 12px;
  }

  .header-actions h2 {
    font-size: 1.5rem;
  }

  .candidature-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
  }

  .status-badge {
    margin-left: 0;
    align-self: flex-end;
  }

  .candidature-card-body {
    padding: 1.25rem;
    gap: 1rem;
  }

  .card-info-row {
    gap: 0.75rem;
  }
}
</style>
