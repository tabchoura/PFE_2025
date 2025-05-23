<template>
  <div class="page-wrapper">
    <div class="profil-container offer-details">
      <h1>{{ offer.titre }}</h1>

      <!-- Loading / Error -->
      <div v-if="loading" class="loading">
        <div class="loading-spinner"></div>
        <p>Chargement des détails…</p>
      </div>
      <div v-else-if="error" class="error-message">
        <span class="error-icon">⚠️</span>
        <p>{{ error }}</p>
        <button @click="getOfferDetails" class="retry-button">Réessayer</button>
      </div>

      <!-- Contenu de l'offre -->
      <div v-else class="offer-content-grid">
        <p><span>Description:</span> {{ offer.description }}</p>
        <p><span>Salaire:</span> {{ offer.salaire }}</p>
        <p><span>Détails:</span> {{ offer.details }}</p>
        <p><span>Date de publication:</span> {{ formatDate(offer.created_at) }}</p>
        <p v-if="offer.date_entretien">
          <span>Entretien:</span> {{ formatDateTime12h(offer.date_entretien) }}
        </p>
      </div>

      <!-- Boutons d'action -->
      <div class="button-container">
        <button @click="enregistrer">
          <i class="fas fa-heart"></i>
          Ajouter à mes offres
        </button>
        <button class="btnapply" @click="Postuler">
          <i class="fas fa-paper-plane"></i>
          Postuler
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";
const toast = useToast();

const offer = ref({});
const loading = ref(true);
const error = ref(null);

const route = useRoute();
const router = useRouter();
const offerId = route.params.id;

function formatDate(d) {
  if (!d) return "—";
  return new Intl.DateTimeFormat("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(new Date(d));
}

function formatDateTime12h(d) {
  if (!d) return "—";
  return new Intl.DateTimeFormat("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "numeric",
    minute: "numeric",
    hour12: true,
  }).format(new Date(d));
}

async function getOfferDetails() {
  loading.value = true;
  error.value = null;
  try {
    await axios.get("/sanctum/csrf-cookie");
    const session =
      localStorage.getItem("userSession") || sessionStorage.getItem("userSession");
    if (!session) {
      alert("Veuillez vous connecter pour voir cette offre.");
      router.push("/logincandidat");
      return;
    }
    const { data } = await axios.get(`/api/offres/${offerId}`);
    offer.value = data;
  } catch (err) {
    console.error(err);
    error.value = "Impossible de charger les détails.";
  } finally {
    loading.value = false;
  }
}

async function enregistrer() {
  try {
    const res = await axios.post(`/api/offreenregistrer/${offerId}`);
    if (res.status === 200) {
      toast.success("Offre enregistrées!");
    }
  } catch (err) {
    console.error(err);
    alert("Échec de l'enregistrement.");
  }
}

function Postuler() {
  router.push(`/postuler/${offerId}`);
}

onMounted(getOfferDetails);
</script>

<style scoped>
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  min-height: 100vh;
  padding: 2rem;
  border-radius: 3%;
}

.profil-container {
  background: #fff;
  padding: 2rem;
  border-radius: 1rem;
  max-width: 900px;
  margin: 6rem auto 2rem;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.profil-container:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.offer-details h1 {
  font-size: 2.4rem;
  text-align: center;
  color: #2980b9;
  margin-bottom: 1rem;
}
.offer-details h1::after {
  content: "";
  display: block;
  width: 100px;
  height: 4px;
  background: #09a1cb;
  margin: 0.5rem auto 0;
  border-radius: 2px;
}

.loading,
.error-message {
  text-align: center;
  padding: 2rem 0;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(59, 130, 246, 0.2);
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.retry-button {
  background: #ef4444;
  color: #fff;
  border: none;
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
}

.error-icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.offer-content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem 3rem;
  margin-top: 1.5rem;
  max-height: 60vh;
  overflow-y: auto;
  padding-right: 0.5rem;
}
.offer-content-grid::-webkit-scrollbar {
  width: 6px;
}
.offer-content-grid::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 3px;
}
.offer-content-grid p {
  margin: 0;
  font-size: 1rem;
  line-height: 1.6;
  color: #555;
}
.offer-content-grid p span {
  font-weight: 600;
  color: #2c3e50;
}

.button-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 2rem;
}
.button-container button {
  flex: 1 1 48%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.2s, transform 0.1s;
}

.button-container button:first-child {
  background: #e74c3c;
  color: #fff;
}
.button-container button:first-child:hover {
  background: #c0392b;
  transform: translateY(-2px);
}

.btnapply {
  background: #09a1cb;
  color: #fff;
}
.btnapply:hover {
  background: #07a0b5;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .offer-details h1 {
    font-size: 1.8rem;
  }
  .offer-content-grid {
    grid-template-columns: 1fr;
    max-height: none;
  }
  .button-container {
    flex-direction: column;
  }
  .button-container button {
    width: 100%;
  }
}
</style>
