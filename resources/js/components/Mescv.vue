<template>
  <div class="mes-cv-container">
    <div class="header">
      <h1>Mes CV</h1>
      <button @click="ajoutercv" class="ajouter-button">
        <span class="icon">+</span>
        <span class="text">Nouveau CV</span>
      </button>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Chargement de vos CV...</p>
    </div>
    
    <div v-else-if="cvs.length" class="cv-list">
      <div v-for="cv in cvs" :key="cv.id" class="cv-card">
        <div class="cv-info">
          <span class="cv-name">{{ cv.prenom }} {{ cv.nom }}</span>
        </div>
        <div class="cv-actions">
          <button @click="constulercv(cv.id)" class="view-button">
            Consulter
            </button>
         </div>
      </div>
    </div>
    
    <div v-else class="empty-state">
      <div class="empty-icon">📄</div>
      <p>Vous n'avez encore créé aucun CV</p>
      <p class="empty-subtitle">Créez votre premier CV pour commencer</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(true)

// Interface pour les CV
interface Cv {
  id: number
  prenom: string
  nom: string
}

const cvs = ref<Cv[]>([])

function ajoutercv() {
  router.push({ name: 'CreerCv', query: { from: 'mescv' } })
}

onMounted(async () => {
  try {
    loading.value = true
    const { data } = await axios.get('/api/cv')
    cvs.value = data
  } catch (err) {
    console.error('Erreur lors du chargement des CV :', err)
  } finally {
    loading.value = false
  }
})

const constulercv = (id) => {
  router.push(`/voircv/${id}`);
};

</script>
<style scoped>
/* Styles pour la section Mes CV */
.mes-cv-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2rem;
  font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, sans-serif;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f0f0f0;
}

.header h1 {
  margin: 0;
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.8rem;
}

.ajouter-button {
  display: flex;
  align-items: center;
  padding: 0.7rem 1.2rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
}

.ajouter-button:hover {
  background-color: #2980b9;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
}

.ajouter-button .icon {
  font-size: 1.2rem;
  margin-right: 0.5rem;
}

.cv-list {
  display: grid;
  gap: 1rem;
}

.cv-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #f9f9f9;
  border-radius: 10px;
  padding: 1.2rem 1.5rem;
  transition: all 0.2s ease;
  border-left: 4px solid #3498db;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.cv-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
  background-color: #f0f7ff;
}

.cv-name {
  font-weight: 600;
  font-size: 1.1rem;
  color: #2c3e50;
}

.view-button {
  padding: 0.5rem 1rem;
  background-color: #3498db;
  color: white;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

.view-button:hover {
  background-color: #2980b9;
}

.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  background-color: #f9f9f9;
  border-radius: 10px;
  border: 2px dashed #e0e0e0;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #bdc3c7;
}

.empty-state p {
  margin: 0.5rem 0;
  color: #7f8c8d;
  font-size: 1.1rem;
}

.empty-subtitle {
  font-size: 0.9rem !important;
  opacity: 0.8;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(52, 152, 219, 0.2);
  border-top-color: #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 600px) {
  .mes-cv-container {
    padding: 1.5rem;
    margin: 1rem;
  }
  
  .header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .cv-card {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .cv-info, .cv-actions {
    width: 100%;
  }
  
  .view-button {
    display: block;
    text-align: center;
  }
}
</style>
