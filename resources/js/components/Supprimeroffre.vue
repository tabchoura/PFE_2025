<template>
  <div class="page-wrapper">
    <div class="supprimer-offre-container">
      <div class="confirmation-card">
        <h2>Confirmer la suppression</h2>

        <div v-if="loading" class="loading-state">
          <p>Chargement des informations...</p>
        </div>

        <div v-else-if="error" class="error-state">
          <p>{{ error }}</p>
          <button @click="chargerOffre" class="btn-retry">Réessayer</button>
          <button @click="retourListe" class="btn-retour">Retour à la liste</button>
        </div>

        <div v-else class="confirmation-content">
          <p class="confirmation-message">
            Êtes-vous sûr de vouloir supprimer l'offre
            <strong>{{ offre.titre }}</strong> ?
          </p>

          <div class="warning-message">
            <p>
              ⚠️ Cette action est irréversible et supprimera définitivement cette offre.
            </p>
          </div>

          <div class="action-buttons">
            <button
              @click="confirmerSuppression"
              class="btn-supprimer"
              :disabled="suppressionEnCours"
            >
              <span v-if="suppressionEnCours">Suppression...</span>
              <span v-else>Confirmer la suppression</span>
            </button>
            <button @click="retourListe" class="btn-annuler">Annuler</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";

const router = useRouter();
const route = useRoute();
const offreId = route.params.id;

const offre = ref({});
const loading = ref(true);
const error = ref(null);
const suppressionEnCours = ref(false);

const chargerOffre = async () => {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get(`/api/offres/${offreId}`);
    offre.value = data;
  } catch (err) {
    error.value = "Erreur lors du chargement de l'offre. Veuillez réessayer.";
  } finally {
    loading.value = false;
  }
};

const confirmerSuppression = async () => {
  suppressionEnCours.value = true;
  try {
    await axios.delete(`/api/offres/${offreId}`);
    router.push("/offresrecruteur");
  } catch (err) {
    error.value = "Erreur lors de la suppression de l'offre. Veuillez réessayer.";
    suppressionEnCours.value = false;
  }
};

const retourListe = () => {
  router.push("/offresrecruteur");
};

onMounted(chargerOffre);
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

/* Conteneur transparent pour laisser transparaître le bleu */
.supprimer-offre-container {
  width: 100%;
  max-width: 600px;
  background: transparent;
}

/* Carte blanche de confirmation */
.confirmation-card {
  background-color: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 30px;
}

/* Titre */
.confirmation-card h2 {
  color: #2c3e50;
  margin: 0 0 20px;
  text-align: center;
  position: relative;
}
.confirmation-card h2:after {
  content: "";
  display: block;
  width: 60px;
  height: 3px;
  background-color: #3498db;
  margin: 10px auto 0;
  border-radius: 2px;
}

/* États loading & error */
.loading-state,
.error-state {
  text-align: center;
  padding: 20px 0;
  color: #555;
}
.error-state {
  color: #e74c3c;
}

/* Boutons retry & retour */
.btn-retry,
.btn-retour {
  padding: 8px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  margin: 0 5px;
}
.btn-retry {
  background-color: #3498db;
  color: #fff;
}
.btn-retour {
  background-color: #95a5a6;
  color: #fff;
}

/* Message de confirmation */
.confirmation-message {
  font-size: 1.1rem;
  margin-bottom: 20px;
  color: #2c3e50;
}

/* Alerte warning */
.warning-message {
  background-color: #fff3cd;
  border-left: 4px solid #ffc107;
  padding: 12px 15px;
  margin-bottom: 20px;
  color: #856404;
}

/* Boutons d'action */
.action-buttons {
  display: flex;
  gap: 10px;
}
.btn-supprimer,
.btn-annuler {
  flex: 1;
  padding: 10px 0;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
}
.btn-supprimer {
  background-color: #e74c3c;
  color: #fff;
}
.btn-supprimer:hover:not(:disabled) {
  background-color: #c0392b;
  transform: translateY(-2px);
}
.btn-supprimer:disabled {
  background-color: #e74c3c99;
  cursor: not-allowed;
}
.btn-annuler {
  background-color: #95a5a6;
  color: #fff;
}
.btn-annuler:hover {
  background-color: #7f8c8d;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .page-wrapper {
    padding: 1rem;
  }
  .supprimer-offre-container {
    margin: 0 auto;
  }
  .action-buttons {
    flex-direction: column;
  }
  .btn-supprimer,
  .btn-annuler {
    margin: 5px 0;
  }
}
</style>
