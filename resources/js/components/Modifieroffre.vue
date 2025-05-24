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
        <div class="btn-group">
          <button type="submit" class="submit-btn">Enregistrer les modifications</button>
          <button type="button" @click="rollback" class="annuler-btn">Annuler</button>
        </div>
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
function rollback() {
  router.push("/mescv");
}
</script>
<style scoped>
/* ──────────────── LAYOUT PRINCIPAL ──────────────── */
.page-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 2rem;
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  border-radius: 3%;
}

.form-container {
  width: 100%;
  max-width: 500px;
}

/* ──────────────── FORMULAIRE ──────────────── */
form {
  background-color: #ffffff;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

/* ──────────────── TYPOGRAPHIE ──────────────── */
h1 {
  position: relative;
  text-align: center;
  color: #2c3e50;
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
}
h1::after {
  content: "";
  display: block;
  width: 80px;
  height: 3px;
  background-color: #3498db;
  margin: 0.5rem auto 0;
  border-radius: 2px;
}

/* ──────────────── CHAMPS DE SAISIE ──────────────── */
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
  background-color: #f8fafc;
  font-size: 1rem;
  transition: border 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}
input::placeholder {
  color: #a0aec0;
}
input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
  background-color: #ffffff;
}

/* ──────────────── GROUPE DE BOUTONS ──────────────── */
.btn-group {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
  /* Pour alignement à droite, décommentez ces lignes :
  justify-content: flex-end;
  */
}

.btn-group .submit-btn,
.btn-group .annuler-btn {
  flex: 1;
  padding: 0.9rem;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
}

/* Bouton principal */
.submit-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: #ffffff;
  padding: 20px;
  border: none;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.submit-btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

/* Bouton secondaire */
.annuler-btn {
  background: linear-gradient(135deg, #7d7d7d 0%, #a3a3a3 100%);
  color: #ffffff;
  border: 1px solid #666666;
}
.annuler-btn:hover {
  filter: brightness(1.1);
}
.annuler-btn:active {
  filter: brightness(0.9);
}

/* ──────────────── RESPONSIVE ──────────────── */
@media (max-width: 768px) {
  .page-wrapper {
    padding: 1rem;
  }
  form {
    padding: 1.5rem;
  }
}
</style>
