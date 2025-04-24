<template>
  <div>
    <form @submit.prevent="modifier">
      <h1>Modifier l'offre de travail :</h1>

      <label for="titre">Titre</label>
      <input type="text" name="titre" id="titre" v-model="formdata.titre" />

      <label for="description">Description</label>
      <input type="text" name="description" id="description" v-model="formdata.description" />

      <label for="salaire">Salaire</label>
      <input type="text" name="salaire" id="salaire" v-model="formdata.salaire" />
      <label for="details">details</label>
      <input type="text" name="details" id="details" v-model="formdata.details" />


      <button type="submit">Enregistrer les modifications</button>
    </form>
  </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const offerId = route.params.id; // Récupérer l'ID de l'offre de la route

// État réactif pour les données du formulaire
const formdata = reactive({
  titre: '',
  description: '',
  salaire: '',
  type: '',
  details:''
});

// Fonction pour récupérer les données de l'offre à modifier
const getOffer = async () => {
  try {
    const response = await axios.get(`/api/offres/${offerId}`);
    formdata.titre = response.data.titre;
    formdata.description = response.data.description;
    formdata.salaire = response.data.salaire;
    formdata.type = response.data.type;
    formdata.type=response.data.details;
  } catch (error) {
    console.error('Erreur lors de la récupération de l\'offre:', error);
  }
};

// Récupérer l'offre lors du montage du composant
onMounted(() => {
  getOffer();
});

// Fonction pour envoyer la demande de modification
const modifier = async () => {
  try {
    const response = await axios.put(`/api/offres/${offerId}`, formdata);
    router.push('/offresrecruteur'); // Rediriger vers la liste des offres
  } catch (error) {
    console.error("Erreur lors de la modification de l'offre:", error);
  }
};
</script>

<style scoped>
/* Styles basiques pour le formulaire */
form {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f7f7f7;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  color: #333;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
}

input {
  width: 100%;
  padding: 10px;
  margin-bottom: 20px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

button {
  width: 100%;
  padding: 12px;
  background-color: #2ecc71;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
}

button:hover {
  background-color: #27ae60;
}
</style>
