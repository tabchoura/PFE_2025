<template>
    <div class="offer-details-section">
      <div v-if="loading" class="loading-state">
        <p>Chargement des détails...</p>
      </div>
  
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="fetchOfferDetails" class="btn-retry">Réessayer</button>
      </div>
  
      <div v-else class="offer-details-content">
        <h2>Modifier l'offre</h2>
  
        <!-- Champ titre modifiable -->
        <div class="input-group">
          <label for="titre"><strong>Titre :</strong></label>
          <input type="text" id="titre" v-model="offerDetails.titre" />
        </div>
  
        <!-- Champ description modifiable -->
        <div class="input-group">
          <label for="description"><strong>Description :</strong></label>
          <textarea id="description" v-model="offerDetails.description"></textarea>
        </div>
  
        <!-- Champ salaire modifiable -->
        <div class="input-group">
          <label for="salaire"><strong>Salaire :</strong></label>
          <input type="number" id="salaire" v-model="offerDetails.salaire" />
        </div>
  
        <button @click="saveChanges" class="btn-save">Enregistrer les modifications</button>
        <button @click="goBack" class="btn-back">Retour à la liste</button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axios from 'axios';
  
  const offerDetails = ref({});
  const loading = ref(true);
  const error = ref(null);
  const route = useRoute();
  const router = useRouter();
  
  const offerId = route.params.id; // Récupère l'ID de l'offre depuis l'URL
  
  // Fonction pour récupérer les détails de l'offre
  const fetchOfferDetails = async () => {
    loading.value = true;
    error.value = null;
  
    try {
      const response = await axios.get(`/api/offres/${offerId}`);
      offerDetails.value = response.data;
    } catch (err) {
      error.value = 'Erreur lors de la récupération des détails de l\'offre. Veuillez réessayer.';
      console.error('Erreur:', err);
    } finally {
      loading.value = false;
    }
  };
  
  // Fonction pour enregistrer les modifications
  const saveChanges = async () => {
    try {
      await axios.put(`/api/offres/${offerId}`, offerDetails.value);
      alert('Les modifications ont été enregistrées avec succès!');
    } catch (err) {
      alert('Erreur lors de l\'enregistrement des modifications.');
      console.error('Erreur:', err);
    }
  };
  
  // Fonction pour revenir à la liste des offres
  const goBack = () => {
    router.push('/listeoffres'); // Remplace '/listeoffres' par la route appropriée de votre liste d'offres
  };
  
  onMounted(() => {
    fetchOfferDetails(); // Appel de la fonction lors du montage du composant
  });
  </script>
  
  <style scoped>
  .offer-details-section {
    padding: 30px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 40px auto;
  }
  
  .loading-state, .error-state {
    text-align: center;
    padding: 40px;
    color: #555;
  }
  
  .error-state {
    color: #e74c3c;
  }
  
  .offer-details-content {
    font-size: 1.1rem;
  }
  
  .offer-details-content h2 {
    color: #2c3e50;
    font-size: 1.6rem;
    margin-bottom: 20px;
  }
  
  .input-group {
    margin-bottom: 20px;
  }
  
  .input-group label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
  }
  
  .input-group input, .input-group textarea {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border-radius: 6px;
    border: 1px solid #ddd;
  }
  
  .input-group textarea {
    resize: vertical;
    min-height: 100px;
  }
  
  .btn-save, .btn-back {
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }
  
  .btn-save:hover, .btn-back:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
  }
  
  .btn-back {
    margin-top: 10px;
  }
  </style>
  