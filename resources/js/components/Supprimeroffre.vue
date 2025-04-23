<template>
  <div class="supprimer-offre-container">
    <div class="confirmation-card">
      <h2>Confirmer la suppression</h2>
      
      <div v-if="loading" class="loading-state">
        <p>Chargement des informations...</p>
      </div>
      
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="chargerOffre" class="btn-retry">Réessayer</button>
        <button @click="retourListe" class="btn-retour">Retour à la liste</button>
      </div>
      
      <div v-else class="confirmation-content">
        <p class="confirmation-message">
          Êtes-vous sûr de vouloir supprimer l'offre <strong>{{ offre.titre }}</strong> ?
        </p>
        
        <div class="warning-message">
          <p>⚠️ Cette action est irréversible et supprimera définitivement cette offre.</p>
        </div>
        
        <div class="action-buttons">
          <button 
            @click="confirmerSuppression" 
            class="btn-supprimer" 
            :disabled="suppressionEnCours"
          >
            <span v-if="suppressionEnCours">Suppression...</span>
            <span v-else>Confirmer la suppression</span>
          </button>
          
          <button @click="retourListe" class="btn-annuler">
            Annuler
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const offreId = route.params.id;

const offre = ref({});
const loading = ref(true);
const error = ref(null);
const suppressionEnCours = ref(false);

const chargerOffre = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response = await axios.get(`/api/offres/${offreId}`);
    offre.value = response.data;
  } catch (err) {
    error.value = "Erreur lors du chargement de l'offre. Veuillez réessayer.";
    console.error("Erreur lors du chargement de l'offre:", err);
  } finally {
    loading.value = false;
  }
};

const confirmerSuppression = async () => {
  suppressionEnCours.value = true;
  
  try {
    await axios.delete(`/api/offres/${offreId}`);
    router.push({ path: '/Offresrecruteur', query: { 
      message: 'Offre supprimée avec succès',
      type: 'success'
    }});
  } catch (err) {
    error.value = "Erreur lors de la suppression de l'offre. Veuillez réessayer.";
    console.error("Erreur lors de la suppression:", err);
    suppressionEnCours.value = false;
  }
};

const retourListe = () => {
  router.push('/Offresrecruteur');
};

onMounted(() => {
  chargerOffre();
});
</script>

<style scoped>
.supprimer-offre-container {
  max-width: 600px;
  margin: 50px auto;
  padding: 20px;
}

.confirmation-card {
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 30px;
}

.confirmation-card h2 {
  color: #2c3e50;
  margin-top: 0;
  border-bottom: 1px solid #eee;
  padding-bottom: 15px;
  margin-bottom: 20px;
}

.loading-state, .error-state {
  text-align: center;
  padding: 20px 0;
  color: #555;
}

.error-state {
  color: #e74c3c;
}

.btn-retry, .btn-retour {
  margin-top: 15px;
  padding: 8px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn-retry {
  background-color: #3498db;
  color: white;
  margin-right: 10px;
}

.btn-retour {
  background-color: #95a5a6;
  color: white;
}

.confirmation-message {
  font-size: 1.1rem;
  margin-bottom: 20px;
  color: #2c3e50;
}

.offer-details {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.offer-details p {
  margin: 8px 0;
}

.warning-message {
  background-color: #fff3cd;
  border-left: 4px solid #ffc107;
  padding: 12px 15px;
  margin-bottom: 20px;
  color: #856404;
}

.action-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 30px;
}

.btn-supprimer, .btn-annuler {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
}

.btn-supprimer {
  background-color: #e74c3c;
  color: white;
  flex-grow: 1;
  margin-right: 10px;
}

.btn-supprimer:hover:not(:disabled) {
  background-color: #c0392b;
  transform: translateY(-2px);
}

.btn-supprimer:disabled {
  background-color: #e74c3c99;
  cursor: not-allowed;
}

.btn-annuler {
  background-color: #95a5a6;
  color: white;
  flex-grow: 1;
  margin-left: 10px;
}

.btn-annuler:hover {
  background-color: #7f8c8d;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .supprimer-offre-container {
    padding: 10px;
    margin: 20px auto;
  }
  
  .confirmation-card {
    padding: 20px;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .btn-supprimer, .btn-annuler {
    width: 100%;
    margin: 5px 0;
  }
  
  .btn-supprimer {
    margin-right: 0;
  }
  
  .btn-annuler {
    margin-left: 0;
  }
}
</style>
