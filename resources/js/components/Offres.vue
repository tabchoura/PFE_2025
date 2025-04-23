<template>
  <div class="offers-section">
    <div class="header-actions">
      <h2>Liste des offres</h2>
    </div>

    <div v-if="loading" class="loading-state">
      <p>Chargement des offres...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="getOffres" class="btn-retry">Réessayer</button>
    </div>

    <div v-else-if="offres.length === 0" class="empty-state">
      <p>Aucune offre disponible pour le moment</p>
    </div>

    <div v-else class="offers-grid">
      <div class="offer-card" v-for="offer in offres" :key="offer.id">
        <div class="offer-card-header">
          <h3 class="title-offre">{{ offer.titre }}</h3>
        </div>

        <div class="offer-card-body">
          <p class="description"><strong>Description :</strong> {{ truncateText(offer.description, 100) }}</p>
          <div class="offer-details">
            <p class="salaire"><strong>Salaire :</strong> {{ formatSalaire(offer.salaire) }}</p>
          </div>
        </div>

        <div class="offer-actions">
          <button class="btn-see-more" @click="voirDetails(offer.id)">Voir les détails</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const offres = ref([]);  // Tableau pour stocker les offres récupérées
const loading = ref(true); // Indicateur de chargement
const error = ref(null);  // Message d'erreur en cas de problème
const router = useRouter();

// Fonction pour récupérer les offres depuis l'API
const getOffres = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/api/offres');  // Appel API pour récupérer les offres
    offres.value = response.data;
  } catch (err) {
    error.value = 'Erreur lors de la récupération des offres. Veuillez réessayer.';
    console.error('Erreur lors de la récupération des offres:', err);
  } finally {
    loading.value = false;
  }
};

const voirDetails = (offerId) => {
  // Rediriger l'utilisateur vers la page des détails de l'offre
  router.push(`/ajouterdetails/${offerId}`);
};

onMounted(() => {
  getOffres();
});

const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

const formatSalaire = (salaire) => {
  if (!salaire) return 'Non précisé';
  return salaire;
};
</script>

<style scoped>
/* Styles pour la section des offres */
.offers-section {
  max-width: 1200px;
  margin: 50px auto;
  padding: 40px 20px;
  background-color: #f8f9fa;
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  position: relative;
}

.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #eaeaea;
}

.header-actions h2 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.6rem;
}

.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 40px;
  color: #555;
}

.error-state {
  color: #e74c3c;
}

.btn-retry {
  margin-top: 15px;
  padding: 8px 20px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  justify-content: center;
}

.offer-card {
  background-color: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.offer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.offer-card-header {
  margin-bottom: 15px;
}

.title-offre {
  font-size: 1.3rem;
  font-weight: bold;
  color: #2c3e50;
  margin: 0;
}

.offer-card-body {
  flex-grow: 1;
}

.description {
  font-size: 0.95rem;
  color: #555;
  margin-bottom: 15px;
  line-height: 1.5;
}

.offer-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.salaire {
  font-size: 0.95rem;
  color: #555;
  margin: 5px 0;
}

.btn-see-more {
  padding: 12px 24px;
  background-color: #2980b9;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.5px;
  transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-see-more:hover {
  background-color: #3498db;
  box-shadow: 0 4px 12px rgba(41, 128, 185, 0.2);
  transform: translateY(-2px);
}
</style>
