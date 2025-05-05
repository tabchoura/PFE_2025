<template>
  <div class="entretiens">
    <h2>🗓️ Planifier l’entretien</h2>
    <p>Choisissez la date puis cliquez sur « Envoyer ».</p>

    <!-- Calendrier relié au state selectedDate -->
    <Calendrier v-model="selectedDate" />

    <button class="primary-btn" @click="envoyer">
      <i class="fas fa-paper-plane" /> Envoyer
    </button>

    <!-- Message de confirmation -->
    <div v-if="entretien" class="success">
      <h3>Entretien enregistré ✅</h3>
      <p>🗓️ {{ formatDate(entretien.date_entretien) }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import Calendrier from "./Calendrier.vue";

/* --- paramètres de route --- */
const route = useRoute();
const candidatureId = Number(route.params.candidatureId); // ex. 42

/* --- router pour une éventuelle redirection --- */
const router = useRouter();

/* --- état local --- */
const selectedDate = ref<string | null>(null); // date choisie dans le calendrier
const entretien = ref<any | null>(null); // réponse du backend

/* --- helpers --- */
function formatDate(d?: string) {
  return d
    ? new Date(d).toLocaleString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      })
    : "";
}

/* --- action Envoyer --- */
async function envoyer() {
  if (!selectedDate.value) {
    alert("Merci de sélectionner une date.");
    return;
  }

  try {
    // requête POST vers l’API
    const { data } = await axios.post("/api/entretiens", {
      candidature_id: candidatureId,
      date_entretien: selectedDate.value,
    });

    // on stocke l’objet entretien pour l’afficher
    entretien.value = data;

    // 👉 si tu veux revenir automatiquement à la liste :
    setTimeout(() => router.push("/candidaturesrecruteur"), 1500);
  } catch (err: any) {
    console.error(err);
    alert(
      err.response?.data?.message || "Erreur lors de l’enregistrement de l’entretien"
    );
  }
}
</script>

<style scoped>
.entretiens {
  max-width: 900px;
  margin: 2rem auto;
  padding: 2rem;
  background: #f8fafc;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

/* bouton primaire */
.primary-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
  padding: 0.75rem 1.5rem;
  background: #3498db;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}
.primary-btn:hover {
  background: #2980b9;
}
/* bloc succès */
.success {
  margin-top: 2rem;
  padding: 1rem 1.5rem;
  background: #c6f6d5;
  border-left: 4px solid #38a169;
  border-radius: 8px;
}
</style>
