<template>
  <div class="offers-section">
    <!-- Titre de la section des offres -->
    <div class="header-actions">
      <h2>Liste des offres</h2>
    </div>

    <!-- Gestion des états de chargement -->
    <div v-if="loading" class="loading-state">
      <p>Chargement des offres...</p>
    </div>

    <!-- Gestion des erreurs -->
    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="getOffres" class="btn-retry">Réessayer</button>
    </div>

    <!-- Affichage quand il n'y a pas d'offres -->
    <div v-else-if="offres.length === 0" class="empty-state">
      <p>Aucune offre disponible pour le moment</p>
    </div>

    <!-- Affichage des offres -->
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

        <!-- Bouton Voir plus pour afficher le modal d'authentification -->
        <button class="btn-see-more" @click="openAuthModal">Voir plus</button>
      </div>
    </div>
  </div>

  <!-- Modal pour l'authentification -->
  <Teleport to="body">
    <div v-if="authModalVisible" class="modal-overlay" @click.self="closeAuthModal">
      <div class="modal-content">
        <button class="close-button" @click="closeAuthModal">×</button>
        <Authentification />
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Authentification from './Authentification.vue';  // Composant d'authentification

const offres = ref([]);  // Tableau pour stocker les offres récupérées
const loading = ref(true); // Indicateur de chargement
const error = ref(null);  // Message d'erreur en cas de problème
const authModalVisible = ref(false);  // Contrôle de l'affichage du modal d'authentification

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

// Charger les offres au montage du composant
onMounted(() => {
  getOffres();
});

// Fonction pour tronquer le texte (pour la description)
const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

// Fonction pour formater le salaire (si nécessaire)
const formatSalaire = (salaire) => {
  if (!salaire) return 'Non précisé';
  return salaire;
};

// Fonction pour ouvrir le modal d'authentification
const openAuthModal = () => {
  authModalVisible.value = true;
};

// Fonction pour fermer le modal d'authentification
const closeAuthModal = () => {
  authModalVisible.value = false;
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

/* Bouton Voir plus */
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

/* Modal d'authentification */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.7);
  z-index: 1099;
}

.modal-content {
  background-color: #fff;
  border-radius: 12px;
  max-height: 95vh;
  width: 60%;
  max-width: 800px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  border: 1px solid #ddd;
  text-align: left;
  overflow-y: auto;
  position: relative;
}

.close-button {
  position: absolute;
  top: 10px;
  right: 10px;
  background: transparent;
  border: none;
  font-size: 1.5rem;
  color: #e74c3c;
  cursor: pointer;
  z-index: 2;
}

.close-button:hover {
  color: #c0392b;
}

.modal-body {
  padding: 20px;
}

.modal-body h3 {
  font-size: 1.5rem;
  color: #2c3e50;
}

.modal-body p {
  font-size: 1rem;
  color: #555;
}
</style>
