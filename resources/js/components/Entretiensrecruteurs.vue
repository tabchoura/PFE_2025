<template>
  <div class="entretiens-container">
    <h1>🗓️ Planifier l'entretien</h1>
    <p class="instructions">Choisissez la date puis cliquez sur « Envoyer ».</p>

    <!-- Calendrier relié au state selectedDate -->
    <Calendrier v-model="selectedDate" />

    <button class="primary-btn" @click="envoyer" :disabled="!selectedDate">
      <i class="fas fa-paper-plane"></i> Envoyer
    </button>

    <!-- Message de confirmation -->
    <div v-if="entretien" class="success-message">
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

// Récupération du paramètre de route
const route = useRoute();
const router = useRouter();
const candidatureId = Number(route.params.candidatureId);

// État local
const selectedDate = ref<string | null>(null);
const entretien = ref<any | null>(null);
const isSubmitting = ref(false);

// Formatage de date
function formatDate(d?: string) {
  if (!d) return "";

  try {
    return new Date(d).toLocaleString("fr-FR", {
      day: "numeric",
      month: "long",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch (error) {
    console.error("Erreur de formatage de date:", error);
    return "Date invalide";
  }
}

// Action d'envoi
async function envoyer() {
  if (!selectedDate.value) {
    alert("Merci de sélectionner une date.");
    return;
  }

  isSubmitting.value = true;

  try {
    // Requête POST vers l'API
    const { data } = await axios.post("/api/entretiens", {
      candidature_id: candidatureId,
      date_entretien: selectedDate.value,
    });

    // Stockage de la réponse
    entretien.value = data;

    // Redirection après affichage du message de succès
    setTimeout(() => router.push({ name: "Candidaturesrecruteur" }), 2000);
  } catch (err: any) {
    console.error("Erreur lors de l'enregistrement:", err);
    alert(
      err.response?.data?.message || "Erreur lors de l'enregistrement de l'entretien"
    );
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<style scoped>
.entretiens-container {
  max-width: 900px;
  margin: 2rem auto;
  padding: 2rem;
  background: #f8fafc;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  font-family: "Segoe UI", sans-serif;
}

h1 {
  font-size: 2rem;
  color: #1a202c;
  margin-bottom: 1rem;
}

.instructions {
  color: #4a5568;
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

/* Bouton principal */
.primary-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 2rem;
  padding: 0.8rem 1.5rem;
  background: #3498db;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.primary-btn:hover {
  background: #2980b9;
  transform: translateY(-2px);
}

.primary-btn:disabled {
  background: #a0aec0;
  cursor: not-allowed;
  transform: none;
}

/* Message de confirmation */
.success-message {
  margin-top: 2rem;
  padding: 1.5rem;
  background: #c6f6d5;
  border-left: 4px solid #38a169;
  border-radius: 8px;
  color: #2f855a;
  transition: all 0.3s ease;
}

.success-message h3 {
  margin-top: 0;
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .entretiens-container {
    margin: 1rem;
    padding: 1.5rem;
  }

  h1 {
    font-size: 1.5rem;
  }
}
</style>
