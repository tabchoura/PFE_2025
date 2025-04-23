<template>
    <div class="add-details-section">
      <h2>Ajouter des détails à l'offre</h2>
  
      <!-- Chargement -->
      <div v-if="loading" class="loading-state">
        <p>Chargement...</p>
      </div>
  
      <!-- Erreur -->
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="submitDetails" class="btn-retry">Réessayer</button>
      </div>
  
      <!-- Formulaire d'ajout des détails -->
      <div v-else class="details-form">
        <div class="input-group">
          <label for="details"><strong>Détails supplémentaires :</strong></label>
          <textarea v-model="details" placeholder="Ajoutez des détails supplémentaires sur l'offre ici..."></textarea>
        </div>
  
        <!-- Affichage des erreurs de validation -->
        <div v-if="errorDetail" class="error-message">
          <p>Les détails ne peuvent pas être vides.</p>
        </div>
  
        <button @click="submitDetails" class="btn-submit">Enregistrer les détails</button>
        <button @click="goBack" class="btn-back">Retour à la liste des offres</button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axios from 'axios';
  
  const details = ref(''); // Détails supplémentaires
  const loading = ref(false);
  const error = ref(null);
  const errorDetail = ref(false); // Gestion des erreurs de champ vide
  const route = useRoute();
  const router = useRouter();
  
  const offerId = route.params.id; // Récupère l'ID de l'offre depuis l'URL
  
  // Fonction pour soumettre les détails supplémentaires
  const submitDetails = async () => {
    // Validation si le champ "details" est vide
    if (!details.value.trim()) {
      errorDetail.value = true;
      return;
    } else {
      errorDetail.value = false;
    }
  
    loading.value = true;
    error.value = null;
  
    try {
      // Requête PUT pour ajouter les détails
      await axios.put(`/api/offres/${offerId}/adddetails`, { details: details.value });
      alert('Détails ajoutés avec succès');
    } catch (err) {
      error.value = 'Erreur lors de l\'ajout des détails. Veuillez réessayer.';
      console.error('Erreur:', err);
    } finally {
      loading.value = false;
    }
  };
  
  // Fonction pour revenir à la liste des offres
  const goBack = () => {
    router.push('/listeoffres'); // Remplace '/listeoffres' par la route appropriée de votre liste d'offres
  };
  </script>
  
  <style scoped>
  .add-details-section {
    padding: 30px;
    background-color: #fff;
    border-radius: 10px;
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
  
  .details-form {
    font-size: 1.1rem;
  }
  
  .details-form .input-group {
    margin-bottom: 20px;
  }
  
  .details-form .input-group label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
  }
  
  .details-form .input-group textarea {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border-radius: 6px;
    border: 1px solid #ddd;
    resize: vertical;
    min-height: 100px;
  }
  
  .details-form .btn-submit, .btn-back {
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }
  
  .details-form .btn-submit:hover, .btn-back:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
  }
  
  .error-message {
    color: #e74c3c;
    font-size: 0.9rem;
    margin-top: 10px;
  }
  </style>
  