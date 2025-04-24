<template>
  <div class="details-offre">
    <h1>{{ offer.titre }}</h1>
    <p><strong>Description:</strong> {{ offer.description }}</p>
    <p><strong>Salaire:</strong> {{ offer.salaire }}</p>
    <p v-if="offer.details"><strong>Détails:</strong> {{ offer.details }}</p>
    <button @click="retourListe">Retour à la liste des offres</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

// Initialisation des variables
const offer = ref({});
const route = useRoute();
const router = useRouter();
const offerId = route.params.id;  // Récupération de l'ID de l'offre depuis l'URL

// Fonction pour récupérer les détails de l'offre
const getOfferDetails = async () => {
  try {
    const response = await axios.get(`/api/offres/${offerId}`);
    offer.value = response.data;  // Stockage des données de l'offre dans `offer`
  } catch (err) {
    console.error('Erreur lors de la récupération des détails de l\'offre:', err);
  }
};

// Appel à la fonction au montage de la page
onMounted(() => {
  getOfferDetails();
});

// Fonction pour revenir à la liste des offres
const retourListe = () => {
  router.push('/offresrecruteur');  // Redirige vers la liste des offres
};
</script>

<style scoped>
.details-offre {
  padding: 20px;
}

button {
  padding: 10px 20px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

button:hover {
  background-color: #2980b9;
}
</style>
