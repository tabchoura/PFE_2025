<template>
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
          placeholder="Ex: 45 000 € - 55 000 €"
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
</template>

<script setup>
import axios from "axios";
import { reactive } from "vue";
import { useRouter } from "vue-router";

const formdata = reactive({
  titre: "",
  description: "",
  salaire: "",
  details: "",
});

const router = useRouter();
async function ajouter() {
  // Validation basique
  if (
    !formdata.titre ||
    !formdata.description ||
    !formdata.salaire ||
    !formdata.details
  ) {
    alert("Tous les champs doivent être remplis!");
    return;
  }

  try {
    const response = await axios.post("/api/offres", formdata);
    console.log(response.data);
    router.push("/offresrecruteur");
  } catch (error) {
    console.error("Erreur lors de l'ajout :", error);
  }
}
</script>

<style scoped>
.form-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

form {
  width: 100%;
  max-width: 600px;
  padding: 2rem;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

h1 {
  margin-bottom: 1.5rem;
  font-size: 1.8rem;
  font-weight: 600;
  color: #2c3e50;
  text-align: center;
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
  transition: all 0.2s ease;
  background-color: #f8fafc;
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

button {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding: 0.9rem;
  margin-top: 1rem;
  background-color: #3498db;
  color: white;
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

@media (max-width: 768px) {
  .form-container {
    padding: 1rem;
  }

  form {
    padding: 1.5rem;
  }
}
</style>
