<template>
  <div class="page-wrapper">
    <!-- Grande carte blanche contenant tout le contenu -->
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
        <button @click="getUserSelectedOffers" class="retry-button">Réessayer</button>
      </div>
      <div v-else-if="!loading && !error && offers.length === 0" class="no-offers">
        <div class="empty-state-icon">📋</div>
        <p>Vous n'avez pas encore d'offres enregistrées.</p>
        <router-link to="/offres" class="browse-offers-btn">
          Parcourir les offres
        </router-link>
      </div>

      <!-- Liste animée des offres -->
      <transition-group name="offer-list" tag="div" class="offers-container">
        <div v-for="offer in offers" :key="offer.id" class="offer-item">
          <div class="offer-content">
            <h2>{{ offer.titre }}</h2>
            <p class="offer-description">{{ offer.description }}</p>
            <div class="offer-details">
              <p class="salary"><span>Salaire:</span> {{ offer.salaire }}</p>
              <p class="details"><span>Détails:</span> {{ offer.details }}</p>
            </div>
          </div>
          <div class="offer-actions">
            <button @click="supprimerEnregistrement(offer.id)" class="delete-btn">
              <span class="delete-icon">🗑️</span>
              <span>Supprimer</span>
            </button>
          </div>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const offers = ref([]);
const error = ref(null);
const loading = ref(true);
const router = useRouter();

async function getUserSelectedOffers() {
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

async function supprimerEnregistrement(offerId) {
  try {
    const response = await axios.delete(`/api/offreenregistrer/${offerId}`);
    if (response.status === 200) {
      offers.value = offers.value.filter((o) => o.id !== offerId);
      // notification manuelle
      const notif = document.createElement("div");
      notif.className = "success-notification";
      notif.textContent = "Offre supprimée avec succès !";
      document.body.appendChild(notif);
      setTimeout(() => {
        notif.classList.add("fade-out");
        setTimeout(() => document.body.removeChild(notif), 500);
      }, 3000);
    }
  } catch (err) {
    console.error("Erreur lors de la suppression de l'offre:", err);
    error.value = "Une erreur est survenue lors de la suppression de l'offre.";
  }
}

onMounted(getUserSelectedOffers);
</script>
<style scoped>
/* === BASE STYLES === */
.page-wrapper {
  background: linear-gradient(135deg, #f0f7ff, #e6f0ff);
  min-height: 100vh;
  padding: 2rem;
}

/* === MAIN CONTAINER === */
.profil-container {
  background: white;
  border-radius: 16px;
  padding: 2.5rem;
  max-width: 1200px;
  margin: 0 auto;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
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
  display: inline-block;
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
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
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
  background: #3b82f6;
  color: white;
  padding: 0.75rem 1.75rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.browse-offers-btn:hover {
  background: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

/* === OFFERS GRID === */
.offers-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

/* === OFFER CARD === */
.offer-item {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  border: 1px solid #e2e8f0;
}

.offer-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
  border-color: #cbd5e1;
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
  line-height: 1.4;
}

.offer-description {
  color: #475569;
  font-size: 0.95rem;
  line-height: 1.6;
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

/* === OFFER ACTIONS === */
.offer-actions {
  padding: 1rem 1.5rem;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.delete-btn {
  width: 100%;
  padding: 0.75rem;
  background: white;
  color: #ef4444;
  border: 1px solid #fecaca;
  border-radius: 8px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
  cursor: pointer;
}

.delete-btn:hover {
  background: #fef2f2;
  color: #dc2626;
  border-color: #fca5a5;
  transform: translateY(-1px);
}

/* === TRANSITIONS === */
.offer-list-enter-active,
.offer-list-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.offer-list-enter-from,
.offer-list-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* === NOTIFICATION === */
.success-notification {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: white;
  color: #065f46;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 10px 15px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  z-index: 1000;
  border-left: 4px solid #10b981;
  animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.fade-out {
  animation: fadeOut 0.5s ease-out forwards;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes fadeOut {
  to {
    opacity: 0;
  }
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

  .offers-container {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .profil-container {
    padding: 1.5rem;
  }

  .no-offers {
    padding: 2rem 1.5rem;
  }

  .success-notification {
    bottom: 1rem;
    right: 1rem;
    left: 1rem;
    width: calc(100% - 2rem);
  }
}
</style>
