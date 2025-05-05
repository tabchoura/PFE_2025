<template>
  <div class="entretiens">
    <h2>🗓️ Planifier l’entretien</h2>
    <p>Choisissez la date puis cliquez sur « Envoyer ».</p>

    <!-- Calendrier relié au state selectedDate -->
    <Calendrier v-model="selectedDate" />

    <button class="primary-btn" @click="envoyer" :disabled="loading">
      <i class="fas fa-paper-plane" />
      <span v-if="!loading">Envoyer</span>
      <span v-else>Envoi...</span>
    </button>

    <!-- Message de confirmation -->
    <div v-if="successMessage" class="success">
      <h3>Entretien enregistré ✅</h3>
      <p>🗓️ {{ formatDate(entretienDate) }}</p>
    </div>
    <div v-if="errorMessage" class="error">
      <p>{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import Calendrier from "./Calendrier.vue";

const route = useRoute();
const router = useRouter();
const candidatureId = Number(route.params.candidatureId);

const selectedDate = ref(null);
const entretienDate = ref(null);
const successMessage = ref(false);
const errorMessage = ref(null);
const loading = ref(false);

function formatDate(d) {
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

async function envoyer() {
  if (!selectedDate.value) {
    errorMessage.value = "Veuillez choisir une date d'entretien.";
    return;
  }

  loading.value = true;
  errorMessage.value = null;

  try {
    const { data } = await axios.post(`/api/candidatures/${candidatureId}/entretien`, {
      date_entretien: selectedDate.value,
    });

    entretienDate.value = data.date_entretien;
    successMessage.value = true;

    // Optionnel : revenir après 2s
    setTimeout(() => {
      router.push({ name: "Candidaturesrecruteur" });
    }, 2000);
  } catch (err) {
    errorMessage.value =
      err.response?.data?.message || "Erreur lors de l’enregistrement de l’entretien.";
  } finally {
    loading.value = false;
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
.primary-btn:disabled {
  background: #a0aec0;
  cursor: not-allowed;
}
.primary-btn:hover:not(:disabled) {
  background: #2980b9;
}

.success {
  margin-top: 2rem;
  padding: 1rem 1.5rem;
  background: #c6f6d5;
  border-left: 4px solid #38a169;
  border-radius: 8px;
}

.error {
  margin-top: 1rem;
  padding: 1rem 1.5rem;
  background: #fed7d7;
  border-left: 4px solid #e53e3e;
  border-radius: 8px;
  color: #c53030;
}
</style>
