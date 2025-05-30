<template>
  <div class="container">
    <!-- Bouton pour afficher / masquer le formulaire -->
    <button @click="formVisible = !formVisible" class="toggle-btn">
      ➕ Ajouter un événement
    </button>

    <!-- Formulaire -->
    <form v-if="formVisible" @submit.prevent="ajouterEvenement" class="form">
      <input v-model="titre" placeholder="Titre de l'événement" required />
      <input v-model="date" type="date" required />
      <input v-model="heure" type="time" required />

      <!-- Champ lien visio (obligatoire) -->
      <input
        type="url"
        v-model="lienVisioInput"
        placeholder="https://meet.jobgo.app/abcd"
        required
      />

      <div class="form-buttons">
        <button type="submit">Ajouter</button>
        <button type="button" class="cancel-btn" @click="annuler">Annuler</button>
      </div>
    </form>

    <!-- Calendrier -->
    <pro-calendar :events="events" :config="calendarConfig" />

    <!-- Liste des événements -->
    <div v-if="events.length" class="event-list">
      <h3>📅 Événements enregistrés :</h3>
      <ul>
        <li v-for="(event, index) in events" :key="event.id" class="event-item">
          <div class="event-details">
            <div class="event-info">
              <strong>{{ event.name }}</strong> – {{ formatDate(event.date) }}
            </div>

            <!-- Affichage du lien Visio si présent -->
            <div v-if="event.lien_visio" class="event-link">
              🔗
              <a :href="event.lien_visio" target="_blank" rel="noopener noreferrer">{{
                event.lien_visio
              }}</a>
            </div>
          </div>

          <button
            class="btn-supprimer"
            @click="supprimerEvenement(index)"
            aria-label="Supprimer l'événement"
          >
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
              <path
                d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
              />
            </svg>
            Supprimer
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";

const route = useRoute();
const router = useRouter();
const toast = useToast();

const candidatureId = Number(route.params.candidatureId);

const events = ref<any[]>([]);
const formVisible = ref(false);
const titre = ref("");
const date = ref("");
const heure = ref("");
const lienVisio = ref("");

const entretien = ref<any | null>(null);
const isSubmitting = ref(false);

function formatDateDMY(dateStr: string) {
  if (!dateStr) return "";
  const parts = dateStr.split("-");
  if (parts.length !== 3) return dateStr;
  const [year, month, day] = parts;
  return `${day}/${month}/${year}`; // Format "d/m/Y"
}

function validerUrl(url: string) {
  try {
    new URL(url);
    return true;
  } catch {
    return false;
  }
}

function ajouterEvenement() {
  if (!titre.value.trim()) {
    toast.warning("Merci de saisir un titre pour l'événement");
    return;
  }
  if (!date.value || !heure.value) {
    toast.warning("Merci de saisir la date et l'heure");
    return;
  }
  if (!lienVisio.value.trim() || !validerUrl(lienVisio.value.trim())) {
    toast.warning(
      "Merci de saisir un lien visio valide (ex: https://meet.jobgo.app/abcd)"
    );
    return;
  }

  const mysql = `${date.value} ${heure.value}:00`;

  events.value.push({
    id: Date.now().toString(),
    name: titre.value.trim(),
    date: mysql,
    lienVisio: lienVisio.value.trim(),
  });

  resetForm();
  formVisible.value = false;
}

function annuler() {
  resetForm();
  formVisible.value = false;
}

function resetForm() {
  titre.value = "";
  date.value = "";
  heure.value = "";
  lienVisio.value = "";
}

function supprimerEvenement(index: number) {
  events.value.splice(index, 1);
}

function retour() {
  router.push("/candidaturesrecruteur");
}

async function envoyer() {
  if (isSubmitting.value) return;
  if (!events.value.length) {
    toast.warning("Veuillez ajouter au moins un événement avant d'envoyer.");
    return;
  }

  isSubmitting.value = true;

  const premierEvenement = events.value[0];
  const dateOnly = formatDateDMY(premierEvenement.date.split(" ")[0]); // "YYYY-MM-DD" → "d/m/Y"

  if (!validerUrl(premierEvenement.lienVisio)) {
    toast.error("Le lien visio du premier événement n'est pas valide.");
    isSubmitting.value = false;
    return;
  }

  try {
    const { data } = await axios.post(`/api/candidatures/${candidatureId}/entretien`, {
      date_entretien: dateOnly,
      lien_visio: premierEvenement.lienVisio,
    });
    entretien.value = data;
    toast.success("Entretien planifié avec succès !");
  } catch (err: any) {
    const message =
      err.response?.data?.message || err.message || "Erreur lors de l'envoi";
    toast.error(message);
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: auto;
  padding: 20px;
}

.toggle-btn {
  background: linear-gradient(90deg, #20c599 0%, #2dd9b5 100%);
  color: #fff;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  margin-bottom: 10px;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s ease;
}

.toggle-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(32, 197, 153, 0.3);
}

.form {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.form input {
  flex: 1;
  min-width: 200px;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 14px;
}

.form input:focus {
  outline: none;
  border-color: #20c599;
  box-shadow: 0 0 0 3px rgba(32, 197, 153, 0.1);
}

.form-buttons {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-shrink: 0;
  width: 100%;
  justify-content: flex-end;
}

.form button[type="submit"] {
  background: linear-gradient(90deg, #20c599 0%, #2dd9b5 100%);
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.form button[type="submit"]:hover {
  background: linear-gradient(90deg, #1ca085 0%, #26c2a0 100%);
  transform: translateY(-2px);
}

.cancel-btn {
  background: #6c757d;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.cancel-btn:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

.event-list {
  margin-top: 30px;
}

.event-list h3 {
  color: #343a40;
  margin-bottom: 15px;
}

.event-list ul {
  list-style: none;
  padding: 0;
}

.event-item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  background: #ffffff;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 10px;
  border: 1px solid #e9ecef;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  gap: 15px;
}

.event-details {
  flex: 1;
}

.event-info {
  margin-bottom: 8px;
  color: #343a40;
}

.event-link {
  font-size: 0.9em;
  color: #6c757d;
}

.event-link a {
  color: #20c599;
  text-decoration: none;
  word-break: break-all;
}

.event-link a:hover {
  text-decoration: underline;
}

.btn-supprimer {
  background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.9rem;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.3s ease;
  user-select: none;
  box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
  flex-shrink: 0;
}

.btn-supprimer:hover {
  background: linear-gradient(180deg, #dc2626 0%, #991b1b 100%);
  box-shadow: 0 4px 10px rgba(220, 38, 38, 0.4);
  transform: translateY(-2px);
}

.btn-supprimer:focus-visible {
  outline: 3px solid #f87171;
  outline-offset: 2px;
  box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.3);
}

.btn-supprimer svg {
  width: 16px;
  height: 16px;
}

@media (max-width: 768px) {
  .event-item {
    flex-direction: column;
    align-items: stretch;
  }

  .form input {
    min-width: 100%;
  }

  .form-buttons {
    justify-content: center;
  }
}
</style>
