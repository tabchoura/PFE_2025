
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
const router = useRouter();
const offerId = route.params.id;

// Fonction pour récupérer les détails de l'offre uniquement si l'utilisateur est authentifié
const getOfferDetails = async () => {
  try {
    // Vérifier si l'utilisateur est authentifié en obtenant le cookie CSRF
    await axios.get('/sanctum/csrf-cookie');
    
    // Vérifier si l'utilisateur est connecté (session existante)
    const userSession = localStorage.getItem('userSession') || sessionStorage.getItem('userSession');
    
    if (!userSession) {
      // Rediriger l'utilisateur vers la page de connexion s'il n'est pas authentifié
      alert("Veuillez vous connecter pour voir les détails de l'offre.");
      router.push('/login');  // Redirection vers la page de connexion
      return;
    }
    
    // L'utilisateur est authentifié, on récupère les détails de l'offre
    const response = await axios.get(`/api/offres/${offerId}`);
    offer.value = response.data;
    
  } catch (error) {
    console.error('Erreur lors de la récupération des détails de l\'offre:', error);
    alert('Une erreur est survenue lors de la récupération des détails de l\'offre.');
  }
};

// Fonction pour postuler à l'offre
const Postuler = () => {
  const offreId = route.params.id;
  router.push(`/postuler/${offreId}`);
};

onMounted(() => {
  getOfferDetails();  // Appel de la fonction pour récupérer les détails de l'offre lors du montage du composant
});
</script>

<style scoped>
/* Styles pour la section des offres */
.offer-details {
  max-width: 900px;
  margin: 30px auto;
  margin-top: 100px;
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
