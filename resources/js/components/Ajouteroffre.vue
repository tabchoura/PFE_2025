<template>
  <div class="page-wrapper">
    <div class="form-container">
      <form @submit.prevent="ajouter">
        <h1>Ajouter une offre de travail</h1>

        <div class="form-group">
          <label for="titre">Titre</label>
          <input
            type="text"
            name="titre"
            id="titre"
            v-model="formdata.titre"
            placeholder="Ex: Développeur Full Stack"
          />
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea
            name="description"
            id="description"
            v-model="formdata.description"
            placeholder="Décrivez brièvement le poste"
            rows="3"
          ></textarea>
        </div>

        <div class="form-group">
          <label for="salaire">Salaire</label>
          <input
            type="text"
            name="salaire"
            id="salaire"
            v-model="formdata.salaire"
            placeholder="Ex: 45 000 € – 55 000 €"
          />
        </div>

        <div class="form-group">
          <label for="details">Détails</label>
          <textarea
            name="details"
            id="details"
            v-model="formdata.details"
            placeholder="Informations complémentaires, avantages, etc."
            rows="5"
          ></textarea>
        </div>

        <button type="submit">
          <span>Enregistrer</span>
          <i class="icon">➔</i>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

import { useRouter } from "vue-router";
const toast = useToast();

const formdata = reactive({
  titre: "",
  description: "",
  salaire: "",
  details: "",
});

const router = useRouter();
async function ajouter() {
  if (
    !formdata.titre ||
    !formdata.description ||
    !formdata.salaire ||
    !formdata.details
  ) {
    alert("Tous les champs doivent être remplis !");
    return;
  }

  try {
    await axios.post("/api/offres", formdata);
    toast.success("Offre ajouter avec succès !");

    router.push("/offresrecruteur");
  } catch (error) {
    console.error("Erreur lors de l'ajout :", error);
  }
}
</script>

<style scoped>
/* Fond global englobant toute la page */
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3); /* Dégradé léger */
  min-height: 100vh;
  padding: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 3%;
}

/* Conteneur transparent pour laisser transparaître le bleu */
.form-container {
  width: 100%;
  max-width: 600px;
}

/* Formulaire en blanc avec ombre douce */
form {
  background-color: #ffffff;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

/* Titre avec trait souligné bleu */
h1 {
  text-align: center;
  font-size: 1.8rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 1.5rem;
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

/* Champs de saisie */
.form-group {
  margin-bottom: 1.5rem;
}
label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #2c3e50;
  font-size: 0.95rem;
}
input,
textarea {
  width: 100%;
  padding: 0.8rem 1rem;
  border: 1px solid #e1e5ee;
  border-radius: 8px;
  font-size: 1rem;
  background-color: #f8fafc;
  transition: all 0.2s ease;
}
input:focus,
textarea:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
  background-color: #fff;
}
input::placeholder,
textarea::placeholder {
  color: #a0aec0;
}
textarea {
  resize: vertical;
  min-height: 80px;
}

/* Bouton principal */
button {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding: 0.9rem;
  margin-top: 1rem;
  background-color: #3498db;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}
button:hover {
  background-color: #2980b9;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
button:active {
  transform: translateY(0);
}
.icon {
  margin-left: 8px;
  transition: transform 0.2s ease;
}
button:hover .icon {
  transform: translateX(4px);
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
