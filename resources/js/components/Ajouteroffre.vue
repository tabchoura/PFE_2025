<template>
  <div>
    <form @submit.prevent="ajouter">
      <h1>Ajouter une offre de travail :</h1>

      <label for="titre">Titre</label>
      <input type="text" name="titre" id="titre" v-model="formdata.titre" />

      <label for="description">Description</label>
      <input type="text" name="description" id="description" v-model="formdata.description" />

      <label for="salaire">Salaire</label>
      <input type="text" name="salaire" id="salaire" v-model="formdata.salaire" />
      <label for="details">details</label>
      <input type="text" name="details" id="details" v-model="formdata.details" />

      

      <button type="submit">Enregistrer</button>
    </form>
  </div>
</template>

<script setup>
import axios from 'axios';
import { reactive } from 'vue';
import { useRouter } from 'vue-router';

const formdata = reactive({
  titre: '',
  description: '',
  salaire: '',
  details:''
});

const router = useRouter();
const ajouter = () => {
  // Validation basique
  if (!formdata.titre || !formdata.description || !formdata.salaire || !formdata.details ) {
    alert("Tous les champs doivent être remplis!");
    return;
  }

  // Ajouter l'offre
  axios.post('/api/offres', formdata)
    .then((response) => {
      // Rediriger vers la liste des offres après l'ajout
      router.push('/offresrecruteur');  // Redirection vers la page des offres
    })
    .catch((error) => {
      console.error("Erreur lors de l'ajout de l'offre:", error);
    });
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
/* Temporarily force the visibility of the input */


</style>