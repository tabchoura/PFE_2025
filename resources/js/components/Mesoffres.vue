<template>
  <div class="mes-offres">
    <h1>Mes offres enregistrées</h1>
    
    <!-- Affichage de l'état de chargement -->
    <div v-if="loading" class="loading">
      <div class="loading-spinner"></div>
      <p>Chargement de vos offres...</p>
    </div>
    
    <!-- Affichage d'une erreur si il y en a une -->
    <div v-if="error" class="error-message">
      <span class="error-icon">⚠️</span>
      <p>{{ error }}</p>
      <button @click="getUserSelectedOffers" class="retry-button">Réessayer</button>
    </div>
    
    <!-- Si aucune offre enregistrée, afficher ce message -->
    <div v-if="!loading && !error && offers.length === 0" class="no-offers">
      <div class="empty-state-icon">📋</div>
      <p>Vous n'avez pas encore d'offres enregistrées.</p>
      <router-link to="/offres" class="browse-offers-btn">Parcourir les offres</router-link>
    </div>
    
    <!-- Affichage des offres enregistrées -->
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
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const offers = ref([]);
const error = ref(null);
const loading = ref(true);

// Fonction pour récupérer les offres enregistrées par l'utilisateur
const getUserSelectedOffers = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get('/api/mesoffres');
    offers.value = response.data;
  } catch (err) {
    console.error("Erreur de chargement:", err);
    error.value = "Impossible de charger vos offres enregistrées. Veuillez réessayer.";
  } finally {
    loading.value = false;
  }
};

// Fonction pour supprimer une offre enregistrée
const supprimerEnregistrement = async (offerId) => {
  try {
    const response = await axios.delete(`/api/offreenregistrer/${offerId}`);
    if (response.status === 200) {
      // Filtrer les offres localement pour une mise à jour instantanée de l'UI
      offers.value = offers.value.filter(offer => offer.id !== offerId);
      
      // Notification de succès
      const notification = document.createElement('div');
      notification.className = 'success-notification';
      notification.textContent = "Offre supprimée avec succès !";
      document.body.appendChild(notification);
      
      // Supprimer la notification après 3 secondes
      setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => document.body.removeChild(notification), 500);
      }, 3000);
    }
  } catch (err) {
    console.error("Erreur lors de la suppression de l'offre:", err);
    error.value = "Une erreur est survenue lors de la suppression de l'offre.";
  }
};

onMounted(() => {
  getUserSelectedOffers();
});
</script>

<style scoped>
.mes-offres {
  max-width: 1000px;
  margin: 0 auto;
  padding: 30px 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.mes-offres h1 {
  color: #1e3a8a;
  font-size: 2rem;
  margin-bottom: 30px;
  font-weight: 700;
  text-align: center;
  position: relative;
}

.mes-offres h1::after {
  content: "";
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, #3b82f6, #1e3a8a);
  border-radius: 2px;
}

/* Loader */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 50px 0;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(59, 130, 246, 0.2);
  border-radius: 50%;
  border-left-color: #3b82f6;
  animation: spin 1s linear infinite;
  margin-bottom: 15px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading p {
  color: #3b82f6;
  font-size: 1.1rem;
}

/* Error message */
.error-message {
  background-color: #fef2f2;
  border-left: 4px solid #ef4444;
  padding: 20px;
  border-radius: 6px;
  margin: 20px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.error-icon {
  font-size: 2rem;
  margin-bottom: 10px;
}

.error-message p {
  color: #b91c1c;
  font-size: 1rem;
  margin-bottom: 15px;
}

.retry-button {
  background-color: #ef4444;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.retry-button:hover {
  background-color: #dc2626;
}

/* Empty state */
.no-offers {
  text-align: center;
  padding: 60px 20px;
  background-color: #f9fafb;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.empty-state-icon {
  font-size: 4rem;
  margin-bottom: 20px;
  color: #9ca3af;
}

.no-offers p {
  font-size: 1.2rem;
  color: #4b5563;
  margin-bottom: 20px;
}

.browse-offers-btn {
  display: inline-block;
  background-color: #3b82f6;
  color: white;
  font-weight: 600;
  padding: 10px 24px;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.2s;
}

.browse-offers-btn:hover {
  background-color: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
}

/* Offers container */
.offers-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

/* Offer card */
.offer-item {
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.offer-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.offer-content {
  padding: 20px;
  flex-grow: 1;
}

.offer-item h2 {
  color: #1e3a8a;
  font-size: 1.3rem;
  margin-bottom: 12px;
  font-weight: 600;
  line-height: 1.3;
}

.offer-description {
  color: #4b5563;
  font-size: 0.95rem;
  margin-bottom: 15px;
  line-height: 1.5;
}

.offer-details {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #e5e7eb;
}

.offer-details p {
  margin: 8px 0;
  font-size: 0.9rem;
  color: #6b7280;
}

.offer-details p span {
  font-weight: 600;
  color: #374151;
}

.salary {
  color: #059669 !important;
}

.salary span {
  color: #065f46 !important;
}

/* Action buttons */
.offer-actions {
  padding: 15px 20px;
  background-color: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.delete-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 10px;
  background-color: white;
  color: #ef4444;
  border: 1px solid #fca5a5;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.delete-btn:hover {
  background-color: #fef2f2;
  color: #b91c1c;
}

.delete-icon {
  margin-right: 8px;
}

/* Animations for transition-group */
.offer-list-enter-active,
.offer-list-leave-active {
  transition: all 0.5s ease;
}

.offer-list-enter-from,
.offer-list-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Success notification */
.success-notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #ecfdf5;
  color: #065f46;
  padding: 15px 25px;
  border-left: 4px solid #10b981;
  border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  animation: slide-in 0.3s ease-out forwards;
}

.fade-out {
  animation: fade-out 0.5s ease-out forwards;
}

@keyframes slide-in {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes fade-out {
  from { opacity: 1; }
  to { opacity: 0; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .offers-container {
    grid-template-columns: 1fr;
  }
  
  .mes-offres {
    padding: 20px 15px;
  }
  
  .mes-offres h1 {
    font-size: 1.7rem;
  }
}
</style>