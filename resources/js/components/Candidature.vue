<template>
  <div class="candidature-section">
    <!-- Titre de la section des candidatures -->
    <div class="header-actions">
      <h2>Mes candidatures</h2>
    </div>

    <!-- Gestion des états de chargement -->
    <div v-if="loading" class="loading-state">
      <p>Chargement des candidatures...</p>
    </div>

    <!-- Gestion des erreurs -->
    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="getCandidatures" class="btn-retry">Réessayer</button>
    </div>

    <!-- Affichage quand il n'y a pas de candidatures -->
    <div v-else-if="candidatures.length === 0" class="empty-state">
      <p>Aucune candidature faite pour le moment</p>
    </div>

    <!-- Affichage des candidatures -->
    <div v-else class="candidature-grid">
      <div class="candidature-card" v-for="candidature in candidatures" :key="candidature.id">
        <div class="candidature-card-header">
          <h3 class="title-offre">{{ candidature.offre.titre }}</h3>
        </div>

        <div class="candidature-card-body">
          <p><strong>Description :</strong> {{ truncateText(candidature.offre.description, 100) }}</p>
          <p><strong>Salaire :</strong> {{ candidature.offre.salaire }}</p>
          <p><strong>Message envoyé :</strong> {{ truncateText(candidature.message, 120) }}</p>
        </div>
        
        <div class="candidature-card-footer">
          <button class="btn-see-more" @click="viewCandidatureDetails(candidature.id)">
            Voir détails
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const candidatures = ref([]);
const loading = ref(true);
const error = ref(null);

// Fonction pour tronquer le texte
const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength 
    ? text.substring(0, maxLength) + '...' 
    : text;
};

// Fonction pour voir les détails d'une candidature
const viewCandidatureDetails = (id) => {
  router.push(`/candidatures/${id}`);
};

// Fonction pour récupérer les candidatures depuis l'API
const getCandidatures = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/api/candidatures');
    candidatures.value = response.data;
  } catch (err) {
    error.value = 'Erreur lors de la récupération des candidatures. Veuillez réessayer.';
    console.error('Erreur lors de la récupération des candidatures:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  getCandidatures();
});
</script>

<style scoped>
/* Styles pour la section des candidatures */
.candidature-section {
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

/* Grille de candidatures */
.candidature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

/* Cartes de candidatures */
.candidature-card {
  background-color: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
  min-height: 220px;
}

.candidature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.candidature-card-header {
  margin-bottom: 15px;
}

.candidature-card-header h3 {
  font-size: 1.3rem;
  font-weight: bold;
  color: #2c3e50;
  margin-top: 0;
  margin-bottom: 5px;
}

.candidature-card-body {
  flex-grow: 1;
}

.candidature-card-body p {
  font-size: 0.95rem;
  color: #555;
  margin-bottom: 10px;
  line-height: 1.4;
}

.candidature-card-footer {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #eee;
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
  transition: background-color 0.3s ease;
  width: 100%;
}

.btn-see-more:hover {
  background-color: #3498db;
}

/* États de chargement et d'erreur */
.loading-state, .error-state, .empty-state {
  padding: 40px;
  text-align: center;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.loading-state {
  position: relative;
}

.loading-state:after {
  content: "";
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #2980b9;
  border-radius: 50%;
  display: block;
  margin: 15px auto;
  animation: spin 1s linear infinite;
}

.btn-retry {
  margin-top: 15px;
  padding: 8px 20px;
  background-color: #2980b9;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-retry:hover {
  background-color: #3498db;
}

/* Animation */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Media Queries pour le responsive */
@media (max-width: 768px) {
  .candidature-grid {
    grid-template-columns: 1fr;
  }
  
  .header-actions {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .candidature-card {
    padding: 15px;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .candidature-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>