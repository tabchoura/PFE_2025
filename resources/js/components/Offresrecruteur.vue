<template>
  <div class="offers-section">
    <div class="header-actions">
      <h2>Liste des offres</h2>
      <button @click="ajouterOffre" class="btn-ajouter" aria-label="Ajouter une nouvelle offre">
        + Ajouter une offre
      </button>
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
          <div class="offer-details">
          <p class="salaire"><strong>Détails :</strong> {{ offer.details || 'Non précisé' }}</p>
        </div>
        </div>
        

        <div class="offer-actions">
          <button class="btn-modifier" @click="modifierOffre(offer.id)" aria-label="Modifier cette offre">
            <span class="icon">✏️</span> Modifier
          </button>
          <button class="btn-supprimer" @click="supprimerOffre(offer.id)" aria-label="Supprimer cette offre">
  <span class="icon">🗑️</span> Supprimer
</button>
<!-- <button class="btn-details" @click="ajouterdetails(offer.id)" aria-label="Ajouter les détails de l'offre">
            <span class="icon">🔍</span> Voir les détails
          </button> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const offres = ref([]);
const loading = ref(true);
const error = ref(null);
const router = useRouter();

const getOffres = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/api/offres');
    offres.value = response.data;
  } catch (err) {
    error.value = 'Erreur lors de la récupération des offres. Veuillez réessayer.';
    console.error('Erreur lors de la récupération des offres:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  getOffres();
});

const ajouterOffre = () => {
  router.push('/ajouteroffre');
};

const modifierOffre = (id) => {
  router.push(`/modifieroffre/${id}`);
};

const supprimerOffre = (id) => {
  router.push(`/supprimeroffre/${id}`);
};
const ajouterdetails = (id) => {
  router.push(`/ajouterdetails/${id}`);
};

// Fonctions utilitaires
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
.offers-section {
  max-width: 1600px;
  margin: 60px auto;
  padding: 40px 20px;
  background-color: #f8f9fa;
  border-radius: 15px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  position: relative;
  transition: box-shadow 0.3s ease;
}

.offers-section:hover {
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
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
  font-size: 1.8rem;
  font-weight: bold;
}

.btn-ajouter {
  padding: 8px 16px;
  background-color: #2980b9;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.85rem;  /* Réduit la taille de la police */
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-ajouter:hover {
  background-color: #3498db;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
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
  padding: 6px 15px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.85rem;
}

.btn-retry:hover {
  background-color: #2980b9;
  transform: translateY(-2px);
}

.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  justify-content: center;
}

.offer-card {
  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
}

.offer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.offer-card-header {
  margin-bottom: 15px;
}

.title-offre {
  font-size: 1.4rem;
  font-weight: bold;
  color: #2c3e50;
  margin: 0;
}

.offer-card-body {
  flex-grow: 1;
}

.description {
  font-size: 1rem;
  color: #555;
  margin-bottom: 15px;
  line-height: 1.6;
}

.offer-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.salaire {
  font-size: 1rem;
  color: #555;
  margin: 5px 0;
}

.offer-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 15px;
  gap: 10px;
}

.btn-modifier, .btn-supprimer, .btn-details {
  padding: 6px 12px;  /* Réduit les dimensions des boutons */
  font-size: 0.85rem;  /* Réduit la taille de la police */
  display: flex;
  align-items: center;
  gap: 5px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.btn-modifier {
  background-color: #f39c12;
  color: #fff;
}

.btn-modifier:hover {
  background-color: #e67e22;
  transform: translateY(-2px);
}

.btn-supprimer {
  background-color: #e74c3c;
  color: white;
}

.btn-supprimer:hover {
  background-color: #c0392b;
  transform: translateY(-2px);
}

.btn-details {
  background-color: #3498db;
  color: white;
}

.btn-details:hover {
  background-color: #2980b9;
  transform: translateY(-2px);
}

/* Modal de confirmation */
.confirmation-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 30px;
  border-radius: 10px;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.btn-confirm {
  background-color: #e74c3c;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn-cancel {
  background-color: #95a5a6;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

/* Responsive */
@media (max-width: 768px) {
  .header-actions {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .btn-ajouter {
    width: 100%;
  }
  
  .offers-grid {
    grid-template-columns: 1fr;
  }
  
  .offer-card {
    width: 100%;
  }
}
</style>
