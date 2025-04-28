<template>
  <div class="postuler-container">
    <h1 class="title">Postuler à l'offre</h1>

    <!-- Chargement -->
    <div v-if="loading" class="loading-container">
      <div class="loader"></div>
      <p class="loading-text">Chargement de vos CV...</p>
    </div>

    <!-- Si pas de CV -->
    <div v-else-if="cvs.length === 0" class="empty-state">
      <div class="empty-icon">📝</div>
      <h3>Vous n'avez pas encore de CV</h3>
      <p>Pour postuler à cette offre, vous devez d'abord créer votre CV.</p>
      <button @click="Creercv" class="primary-button create-cv-button">
        <span class="button-icon">➕</span> Créer votre CV
      </button>
    </div>

    <!-- Etat de confirmation -->
    <div v-else class="confirmation-state">
      <div class="offre-info" v-if="detailsoffre">
        <h3 class="offre-title">{{ detailsoffre.titre }}</h3>
        <p class="offre-company">{{ detailsoffre.description }}</p>
        <p class="offre-location">{{ detailsoffre.salaire }}</p>
        <p> {{ detailsoffre.details }}</p>
      </div>

      <div class="cv-selection" v-if="cvs.length > 1">
        <h3>Sélectionnez le CV à utiliser</h3>
        <div class="cv-list">
          <div 
            v-for="(cv, index) in cvs" 
            :key="index" 
            :class="['cv-item', { 'selected': selectedCvId === cv.id }]"
            @click="selectedCvId = cv.id"
          >
            <div class="cv-item-content">
              <span class="cv-name">{{ cv.nom }} {{ cv.prenom }}</span>
              <span class="cv-date">Mis à jour: {{ formatDate(cv.updated_at || cv.created_at) }}</span>
            </div>
            <div class="check-indicator" v-if="selectedCvId === cv.id">✓</div>
          </div>
        </div>
      </div>

      <!-- Message de confirmation -->
      <div class="confirmation-message">
        <p>Êtes-vous sûr de vouloir postuler à cette offre ?</p>

        <div class="motivation-section">
          <label for="motivation" class="motivation-label">Ajoutez un message (optionnel)</label>
          <textarea id="motivation" v-model="motivationText" class="motivation-textarea" placeholder="Expliquez brièvement pourquoi ce poste vous intéresse..."></textarea>
        </div>
      </div>

      <div class="action-buttons">
        <button @click="confirmPostuler" class="primary-button confirm-button" :disabled="submitting">
          <span class="button-icon" v-if="!submitting">✓</span>
          <span class="loader small-loader" v-else></span>
          {{ submitting ? 'Envoi en cours...' : 'Confirmer ma candidature' }}
        </button>
        <button @click="cancel" class="secondary-button">Annuler</button>
      </div>
    </div>

    <!-- Message d'erreur -->
    <div v-if="error" class="error-message">
      <span class="error-icon">⚠️</span>
      <p>{{ error }}</p>
      <button @click="error = null" class="close-error-button">×</button>
    </div>

    <!-- Message de succès -->
    <div v-if="success" class="success-message">
      <span class="success-icon">✅</span>
      <p>{{ success }}</p>
      <div class="success-actions">
        <button @click="goToCandidatures" class="primary-button small-button">Voir mes candidatures</button>
        <button @click="goToOffers" class="secondary-button small-button">Voir d'autres offres</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

// Déclaration des variables réactives
const route = useRoute()
const router = useRouter()

const cvs = ref([]) // Tableau des CVs
const loading = ref(true) // Variable de chargement
const error = ref(null) // Message d'erreur
const success = ref(null) // Message de succès
const submitting = ref(false) // Indicateur de soumission
const selectedCvId = ref(null) // ID du CV sélectionné
const motivationText = ref('') // Texte de la lettre de motivation
const detailsoffre = ref(null) // Détails de l'offre

// Fonction pour formater les dates
function formatDate(dateStr) {
  if (!dateStr) return 'Date inconnue'
  const date = new Date(dateStr)
  return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}

// Récupération des données au montage du composant
onMounted(async () => {
  try {
    const offerId = route.params.id
    // Charger les données des offres et des CVs en parallèle
    const [offreResponse, cvsResponse] = await Promise.all([
      axios.get(`/api/offres/${offerId}`),
      axios.get(`/api/cv`) // Récupérer tous les CVs
    ])
    detailsoffre.value = offreResponse.data // Détails de l'offre
    cvs.value = cvsResponse.data // Liste des CVs

    // Sélectionner le premier CV s'il existe
    if (cvs.value.length > 0) {
      selectedCvId.value = cvs.value[0].id
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Impossible de charger les données nécessaires'
  } finally {
    loading.value = false
  }
})

// Fonction pour confirmer la candidature
async function confirmPostuler() {
  const offerId = route.params.id
  const cvId = selectedCvId.value || (cvs.value.length > 0 ? cvs.value[0].id : null)

  if (!cvId) {
    error.value = 'Aucun CV disponible pour postuler'
    return
  }

  submitting.value = true

  try {
    await axios.post(`/api/offres/${offerId}/postuler`, {
      cv_id: cvId,
      message: motivationText.value || null,
      statut: "En attente" // Statut de la candidature
    })
    success.value = 'Votre candidature a été envoyée avec succès!'
    error.value = null
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur lors de la candidature'
  } finally {
    submitting.value = false
  }
}

// Fonction pour créer un CV
function Creercv() {
  router.push({ name: 'creerCv', query: { from: 'postuler', offreId: route.params.id } })
}

// Fonction pour annuler et revenir à la page précédente
function cancel() {
  router.back()
}

// Fonction pour aller aux candidatures
function goToCandidatures() {
  router.push({ name: 'Candidature' })
}

// Fonction pour voir d'autres offres
function goToOffers() {
  router.push({ name: 'Offres' })
}
</script>




<style scoped>
.postuler-container {
  max-width: 800px;
  margin: 6rem auto 2.5rem;   padding: 2.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.title {
  color: #2c3e50;
  font-size: 2.2rem;
  margin-bottom: 2.5rem;
  text-align: center;
  position: relative;
  font-weight: 700;
}

.title:after {
  content: "";
  position: absolute;
  bottom: -12px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 4px;
  background: linear-gradient(90deg, #3498db, #2980b9);
  border-radius: 4px;
}

/* États de chargement */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
}

.loader {
  border: 4px solid rgba(52, 152, 219, 0.1);
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1.2s cubic-bezier(0.5, 0.1, 0.5, 0.9) infinite;
  margin-bottom: 1.5rem;
}

.small-loader {
  width: 22px;
  height: 22px;
  border-width: 2px;
  margin-right: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  color: #7f8c8d;
  font-size: 1.2rem;
  font-weight: 500;
}

/* État vide - pas de CV */
.empty-state {
  text-align: center;
  padding: 4rem 0;
  animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
  color: #95a5a6;
  opacity: 0.8;
}

.empty-state h3 {
  font-size: 1.7rem;
  margin-bottom: 1.2rem;
  color: #2c3e50;
  font-weight: 600;
}

.empty-state p {
  color: #7f8c8d;
  margin-bottom: 2.5rem;
  font-size: 1.1rem;
  max-width: 80%;
  margin-left: auto;
  margin-right: auto;
}

/* État de confirmation */
.confirmation-state {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  animation: fadeIn 0.5s ease;
}

/* Information sur l'offre */
.offre-info {
  background: linear-gradient(to right, #f8f9fa, #f1f3f4);
  padding: 1.8rem;
  border-radius: 12px;
  margin-bottom: 1.2rem;
  border-left: 5px solid #3498db;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.offre-info:hover {
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
  transform: translateY(-2px);
}

.offre-title {
  margin: 0 0 0.7rem 0;
  font-size: 1.6rem;
  color: #2c3e50;
  font-weight: 700;
}

.offre-company {
  font-weight: 600;
  margin: 0 0 0.7rem 0;
  color: #34495e;
  font-size: 1.2rem;
}

.offre-location {
  color: #7f8c8d;
  margin: 0;
  display: flex;
  align-items: center;
  font-size: 1.05rem;
}

.offre-location:before {
  content: "📍";
  margin-right: 6px;
}

/* Sélection du CV */
.cv-selection {
  margin: 1.5rem 0;
}

.cv-selection h3 {
  margin-bottom: 1.2rem;
  font-size: 1.3rem;
  color: #2c3e50;
  font-weight: 600;
}

.cv-list {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.cv-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.2rem 1.5rem;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  transition: all 0.25s ease;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.cv-item:hover {
  border-color: #3498db;
  background: #f8f9fa;
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(52, 152, 219, 0.1);
}

.cv-item.selected {
  border-color: #3498db;
  background: #ebf7fd;
  box-shadow: 0 5px 15px rgba(52, 152, 219, 0.15);
}

.cv-item-content {
  display: flex;
  flex-direction: column;
}

.cv-name {
  font-weight: 600;
  color: #2c3e50;
  font-size: 1.1rem;
}

.cv-date {
  font-size: 0.95rem;
  color: #7f8c8d;
  margin-top: 0.6rem;
}

.check-indicator {
  color: #3498db;
  font-size: 1.8rem;
  font-weight: bold;
  transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.cv-item.selected .check-indicator {
  transform: scale(1.2);
}

/* Message de confirmation */
.confirmation-message {
  text-align: center;
  margin: 1.5rem 0;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 12px;
}

.confirmation-message p {
  font-size: 1.3rem;
  color: #2c3e50;
  margin-bottom: 1.5rem;
  font-weight: 500;
}

/* Lettre de motivation */
.motivation-section {
  margin: 2rem 0;
  text-align: left;
}

.motivation-label {
  display: block;
  margin-bottom: 0.8rem;
  color: #34495e;
  font-weight: 500;
  font-size: 1.1rem;
}

.motivation-textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  min-height: 140px;
  resize: vertical;
  font-family: inherit;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.motivation-textarea:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.motivation-textarea::placeholder {
  color: #b2bec3;
}

/* Boutons d'action */
.action-buttons {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 2rem;
}

button {
  padding: 0.9rem 1.8rem;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  font-size: 1.05rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.primary-button {
  background: linear-gradient(to right, #3498db, #2980b9);
  color: white;
  box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
}

.primary-button:hover {
  background: linear-gradient(to right, #2980b9, #2471a3);
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
}

.primary-button:active {
  transform: translateY(-1px);
}

.primary-button:disabled {
  background: linear-gradient(to right, #95a5a6, #7f8c8d);
  cursor: not-allowed;
  transform: none;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.secondary-button {
  background: #f5f7fa;
  color: #34495e;
  border: 2px solid #dfe6e9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.secondary-button:hover {
  background: #e0e7ee;
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.secondary-button:active {
  transform: translateY(-1px);
}

.create-cv-button {
  padding: 1.2rem 2.5rem;
  font-size: 1.15rem;
  border-radius: 12px;
}

.small-button {
  padding: 0.6rem 1.2rem;
  font-size: 0.95rem;
}

.button-icon {
  margin-right: 10px;
  font-size: 1.1em;
}

.confirm-button {
  padding: 1rem 2.5rem;
  min-width: 220px;
}

/* Message d'erreur */
.error-message {
  display: flex;
  align-items: center;
  background: #fdedec;
  border-left: 5px solid #e74c3c;
  padding: 1.2rem;
  margin-top: 2rem;
  border-radius: 10px;
  color: #c0392b;
  position: relative;
  animation: slideIn 0.3s ease;
  box-shadow: 0 4px 10px rgba(231, 76, 60, 0.1);
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.error-icon {
  margin-right: 12px;
  font-size: 1.4rem;
}

.error-message p {
  font-size: 1.05rem;
  margin-right: 20px;
}

.close-error-button {
  position: absolute;
  right: 12px;
  top: 12px;
  background: none;
  border: none;
  color: #e74c3c;
  font-size: 1.4rem;
  cursor: pointer;
  padding: 0;
  transition: transform 0.2s ease;
}

.close-error-button:hover {
  transform: scale(1.2);
}

/* Message de succès */
.success-message {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #e8f8f5;
  border-left: 5px solid #2ecc71;
  padding: 2rem;
  margin-top: 2rem;
  border-radius: 12px;
  color: #27ae60;
  text-align: center;
  animation: fadeIn 0.5s ease;
  box-shadow: 0 4px 15px rgba(46, 204, 113, 0.15);
}

.success-icon {
  font-size: 3rem;
  margin-bottom: 1.2rem;
  animation: successPulse 2s infinite;
}

@keyframes successPulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

.success-message p {
  color: #2c3e50;
  font-size: 1.4rem;
  margin-bottom: 1.8rem;
  font-weight: 500;
}

.success-actions {
  display: flex;
  gap: 1.2rem;
  margin-top: 0.8rem;
}

/* Responsive */
@media (max-width: 768px) {
  .postuler-container {
    padding: 1.8rem;
    margin: 1rem;
    border-radius: 12px;
  }
  
  .title {
    font-size: 1.8rem;
  }
  
  .title:after {
    width: 80px;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 1rem;
  }
  
  .action-buttons button {
    width: 100%;
  }
  
  .success-actions {
    flex-direction: column;
    width: 100%;
    gap: 0.8rem;
  }
  
  .offre-title {
    font-size: 1.4rem;
  }
  
  .confirmation-message p {
    font-size: 1.2rem;
  }
  
  .empty-state p {
    max-width: 100%;
  }
  
  .empty-icon {
    font-size: 4rem;
  }
  
  .cv-item {
    padding: 1rem;
  }
  
  .motivation-textarea {
    min-height: 120px;
  }
}

/* Accessibilité et détails supplémentaires */
@media (prefers-reduced-motion) {
  *, *::before, *::after {
    animation-duration: 0.001s !important;
    transition-duration: 0.001s !important;
  }
}

/* Style focus pour accessibilité */
button:focus, .motivation-textarea:focus, .cv-item:focus {
  outline: 3px solid rgba(52, 152, 219, 0.4);
  outline-offset: 2px;
}

/* Transition plus douce entre les états */
.postuler-container > div {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Mode sombre - support basique si le système est en mode sombre */
@media (prefers-color-scheme: dark) {
  .postuler-container {
    background: #1a1e25;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
  }
  
  .title, .offre-title, .cv-name, .confirmation-message p, .success-message p, .cv-selection h3, .empty-state h3 {
    color: #ecf0f1;
  }
  
  .offre-info {
    background: linear-gradient(to right, #2c3e50, #34495e);
    border-left-color: #3498db;
  }
  
  .offre-company {
    color: #bdc3c7;
  }
  
  .cv-item {
    background: #2c3e50;
    border-color: #34495e;
  }
  
  .cv-item:hover {
    background: #34495e;
  }
  
  .cv-item.selected {
    background: #2980b9;
  }
  
  .cv-date {
    color: #bdc3c7;
  }
  
  .confirmation-message {
    background: #2c3e50;
  }
  
  .motivation-textarea {
    background: #2c3e50;
    color: #ecf0f1;
    border-color: #34495e;
  }
  
  .motivation-textarea::placeholder {
    color: #95a5a6;
  }
  
  .secondary-button {
    background: #34495e;
    color: #ecf0f1;
    border-color: #4a6073;
  }
  
  .secondary-button:hover {
    background: #4a6073;
  }
  
  .error-message {
    background: #4e1a17;
    border-left-color: #e74c3c;
  }
  
  .success-message {
    background: #1e5931;
    border-left-color: #2ecc71;
  }
}
</style>