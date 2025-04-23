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

const offer = ref({});
const route = useRoute();
const router = useRouter();
const offerId = route.params.id;

const getOfferDetails = async () => {
  try {
    const response = await axios.get(`/api/offres/${offerId}`);
    offer.value = response.data;
  } catch (err) {
    console.error('Erreur lors de la récupération des détails de l\'offre:', err);
  }
};

onMounted(() => {
  getOfferDetails();
});

const retourListe = () => {
  router.push('/offresrecruteur');
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
