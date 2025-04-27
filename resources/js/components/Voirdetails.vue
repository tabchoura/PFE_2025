<template>
  <div class="offer-details">
    <h1>{{ offer.titre }}</h1>
    <p><strong>Description:</strong> {{ offer.description }}</p>
    <p><strong>Salaire:</strong> {{ offer.salaire }}</p>
    <p><strong>Détails:</strong> {{ offer.details }}</p>
    <div class="button-container">
      <button class="btnapply" @click="Postuler">Postuler par ici</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';


const offer = ref({});
const route = useRoute();
const router=useRouter();
const offerId = route.params.id;

onMounted(() => {
  // Récupérer les détails de l'offre en fonction de l'ID
  axios.get(`/api/offres/${offerId}`)
    .then(response => {
      offer.value = response.data;
    })
    .catch(error => {
      console.error('Erreur lors de la récupération des détails de l\'offre:', error);
    });
});

const Postuler=()=>{
  const offreId = route.params.id 

  router.push(`/postuler/${offreId}`)}
</script>

<style scoped>
.offer-details {
  max-width: 900px;
  margin: 30px auto;
  margin-top:100px;
  padding: 30px;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease-in-out;
  min-height: 300px; /* Hauteur minimale pour éviter que la section soit trop petite */
  max-height: 400px; /* Hauteur maximale */
  overflow-y: auto; /* Permet le défilement vertical si le contenu dépasse */
}

.offer-details:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.button-container {
  display: flex;
  justify-content: flex-end; /* Aligne le contenu à droite */
  margin-top: 20px;
}

.btnapply {
  padding: 12px 24px;
  background-color: #09a1cb;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.btnapply:hover {
  background-color: #428beb;
}

h1 {
  font-size: 2rem;
  text-align: center;
  color: #2980b9;
  margin-bottom: 20px;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1px;
}

p {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #555;
  margin-bottom: 15px;
}

strong {
  color: #2c3e50;
  font-weight: 600;
}

.offer-details p:first-child {
  margin-top: 0;
}

.offer-details p:last-child {
  margin-bottom: 0;
}

.offer-details .offer-header {
  background-color: #2980b9;
  color: white;
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 25px;
  font-size: 1.2rem;
  font-weight: bold;
  text-align: center;
}

.offer-details .btn {
  background-color: #2ecc71;
  color: white;
  padding: 12px 30px;
  font-size: 1.1rem;
  border-radius: 8px;
  text-align: center;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 20px;
  display: block;
  width: 100%;
}

.offer-details .btn:hover {
  background-color: #27ae60;
}

@media (max-width: 768px) {
  .offer-details {
    padding: 20px;
  }
  
  h1 {
    font-size: 1.8rem;
  }
  
  p {
    font-size: 1rem;
  }
}
</style>