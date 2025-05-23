<template>
  <div class="page-wrapper">
    <div class="form-container">
      <form @submit.prevent="modifier">
        <h1>Modifier l'offre de travail</h1>

        <div class="form-group">
          <label for="titre">Titre</label>
          <input
            type="text"
            id="titre"
            v-model="formdata.titre"
            placeholder="Ex: Développeur Full Stack"
          />
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <input
            type="text"
            id="description"
            v-model="formdata.description"
            placeholder="Décrivez brièvement le poste"
          />
        </div>

        <div class="form-group">
          <label for="salaire">Salaire</label>
          <input
            type="text"
            id="salaire"
            v-model="formdata.salaire"
            placeholder="Ex: 45 000 € – 55 000 €"
          />
        </div>

        <div class="form-group">
          <label for="details">Détails</label>
          <input
            type="text"
            id="details"
            v-model="formdata.details"
            placeholder="Informations complémentaires, avantages…"
          />
        </div>

        <button type="submit" class="submit-btn">Enregistrer les modifications</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";
const toast = useToast();

const route = useRoute();
const router = useRouter();
const offerId = route.params.id;

const formdata = reactive({
  titre: "",
  description: "",
  salaire: "",
  details: "",
});

const getOffer = async () => {
  try {
    const { data } = await axios.get(`/api/offres/${offerId}`);
    formdata.titre = data.titre;
    formdata.description = data.description;
    formdata.salaire = data.salaire;
    formdata.details = data.details;
  } catch (err) {
    console.error("Erreur récupération offre :", err);
  }
};

onMounted(getOffer);

async function modifier() {
  try {
    await axios.put(`/api/offres/${offerId}`, formdata);
    toast.success("Offre mis à avec succès !");

    router.push("/offresrecruteur");
  } catch (err) {
    console.error("Erreur modification offre :", err);
  }
}
</script>

<style scoped>
/* Wrapper global avec fond bleu */
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  min-height: 100vh;
  padding: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 3%;
}

/* Conteneur transparent */
.form-container {
  width: 100%;
  max-width: 500px;
}

/* Formulaire en carte blanche */
form {
  background-color: #ffffff;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

/* Titre */
h1 {
  text-align: center;
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: #2c3e50;
  position: relative;
}
h1:after {
  content: "";
  display: block;
  width: 80px;
  height: 3px;
  background-color: #3498db;
  margin: 10px auto 0;
  border-radius: 2px;
}

/* Champs */
.form-group {
  margin-bottom: 1.5rem;
}
label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #2c3e50;
}
input {
  width: 100%;
  padding: 0.8rem 1rem;
  border: 1px solid #e1e5ee;
  border-radius: 8px;
  font-size: 1rem;
  background-color: #f8fafc;
  transition: all 0.2s ease;
}
input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
  background-color: #fff;
}
input::placeholder {
  color: #a0aec0;
}

/* Bouton principal */
.submit-btn {
  width: 100%;
  padding: 0.9rem;
  background-color: #3498db;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: all 0.2s ease;
}
.submit-btn:hover {
  background-color: #2980b9;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.submit-btn:active {
  transform: translateY(0);
}

/* Responsive */
@media (max-width: 768px) {
  .page-wrapper {
    padding: 1rem;
  }
  form {
    padding: 1.5rem;
  }
}
</style>
