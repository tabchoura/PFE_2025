<template>
  <div class="page-wrapper">
    <div class="entretiens-container">
      <h1>🗓️ Planifier l'entretien</h1>
      <p class="instructions">Choisissez la date puis cliquez sur « Envoyer ».</p>

      <button @click="formVisible = !formVisible" class="toggle-btn">
        {{ formVisible ? "✖️ Annuler l'ajout" : "➕ Ajouter un événement" }}
      </button>

      <form v-if="formVisible" @submit.prevent="ajouterEvenement" class="form">
        <input v-model="date" type="date" required />
        <input v-model="heure" type="time" required />
        <input
          v-model="lienVisio"
          type="url"
          placeholder="https://meet.jobgo.app/abcd"
          required
          autocomplete="off"
        />

        <div class="form-buttons">
          <button type="submit">Ajouter</button>
          <button type="button" class="cancel-btn" @click="annuler">Annuler</button>
        </div>
      </form>

      <pro-calendar :events="events" :config="calendarConfig" />

      <div v-if="events.length" class="event-list">
        <h3>📅 Événements enregistrés :</h3>
        <ul>
          <li v-for="(event, index) in events" :key="event.id" class="event-item">
            {{ formatDate(event.date) }} –
            <a :href="event.lienVisio" target="_blank" rel="noopener noreferrer">
              {{ event.lienVisio }}
            </a>
            <button
              class="btn-supprimer"
              @click="supprimerEvenement(index)"
              aria-label="Supprimer l'événement"
            >
              Supprimer
            </button>
          </li>
        </ul>
      </div>

      <div class="btn-group">
        <button @click="retour" class="back-btn" aria-label="Retour">← Retour</button>
        <button
          @click="envoyer"
          class="send-btn"
          :disabled="!events.length || isSubmitting"
        >
          {{ isSubmitting ? "Envoi…" : "Envoyer" }}
        </button>
      </div>

      <div v-if="entretien" class="success-message" role="alert">
        <h3>Entretien enregistré ✅</h3>
        <p>🗓️ {{ formatDate(entretien.date_entretien) || "Date indisponible" }}</p>
        <p v-if="entretien.lien_visio">
          🔗
          <a
            :href="nettoyerLien(entretien.lien_visio)"
            target="_blank"
            rel="noopener noreferrer"
          >
            {{ nettoyerLien(entretien.lien_visio) }}
          </a>
        </p>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import "@lbgm/pro-calendar-vue/style";
import { useToast } from "vue-toastification";

const route = useRoute();
const router = useRouter();
const toast = useToast();

const candidatureId = Number(route.params.candidatureId);

const events = ref([]);
const formVisible = ref(false);
const date = ref("");
const heure = ref("");
const lienVisio = ref("");

const entretien = ref(null);
const isSubmitting = ref(false);

const calendarConfig = {
  actions: {
    view: { enabled: true, icon: true, text: "Voir" },
    report: { enabled: false },
  },
  searchPlaceHolder: "Rechercher...",
  eventName: "Nouvel Événement",
  closeText: "Fermer",
};

function formatDate(d) {
  if (!d) return "";
  try {
    const dateObj = new Date(d);
    return dateObj.toLocaleString("fr-FR", {
      day: "numeric",
      month: "long",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch {
    return "Date invalide";
  }
}

function nettoyerLien(lien) {
  if (!lien) return "";
  if (lien.length % 2 === 0) {
    const moitié = lien.length / 2;
    if (lien.slice(0, moitié) === lien.slice(moitié)) {
      return lien.slice(0, moitié);
    }
  }
  return lien;
}

function ajouterEvenement() {
  if (!date.value || !heure.value) {
    toast.warning("Merci de saisir la date et l'heure");
    return;
  }
  if (!lienVisio.value.trim()) {
    toast.warning("Merci de saisir un lien visio valide");
    return;
  }
  try {
    new URL(lienVisio.value.trim());
  } catch {
    toast.warning(
      "Le lien visio doit être une URL valide (ex: https://meet.jobgo.app/abcd)"
    );
    return;
  }

  const Date_entretien = `${date.value} ${heure.value}:00`;

  events.value.push({
    id: Date.now().toString(),
    date: Date_entretien,
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
  date.value = "";
  heure.value = "";
  lienVisio.value = "";
}

function supprimerEvenement(index) {
  events.value.splice(index, 1);
}

function retour() {
  router.push("/candidaturesrecruteur");
}

function formatDateDMY_HM(dateStr, timeStr) {
  if (!dateStr) return "";
  const parts = dateStr.split("-");
  if (parts.length !== 3) return dateStr;
  const [year, month, day] = parts;
  return `${day}/${month}/${year} ${timeStr}`;
}

async function envoyer() {
  if (isSubmitting.value) return;
  if (!events.value.length) {
    toast.warning("Veuillez ajouter au moins un événement avant d'envoyer.");
    return;
  }

  isSubmitting.value = true;

  const premierEvenement = events.value[0];
  const [datePart, timePartWithSeconds] = premierEvenement.date.split(" ");
  const timePart = timePartWithSeconds.slice(0, 5);

  const dateHeureFormatee = formatDateDMY_HM(datePart, timePart);
  const lienNettoye = nettoyerLien(premierEvenement.lienVisio);

  try {
    const { data } = await axios.post(`/api/candidatures/${candidatureId}/entretien`, {
      date_entretien: dateHeureFormatee,
      lien_visio: lienNettoye,
    });
    entretien.value = data.candidature;
    toast.success("Entretien planifié avec succès !");
    router.push("/Candidaturesrecruteur");
  } catch (err) {
    const message = toast.error("Erreur lors de l'envoi");
    toast.error(message);
  } finally {
    isSubmitting.value = false;
  }
}
</script>

<style scoped>
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  padding: 2rem;
  min-height: 100vh;
}
.entretiens-container h1 {
  color: #1e3a8a;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  position: relative;
  padding-bottom: 0.5rem;
}
.entretiens-container h1::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 4px;
  background: #3b82f6;
  border-radius: 2px;
}

.entretiens-container {
  max-width: 900px;
  margin: auto;
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.toggle-btn {
  background: linear-gradient(90deg, #20c599 0%, #2dd9b5 100%);
  color: white;
  font-weight: 700;
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-bottom: 1rem;
}

.form {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.form input {
  flex: 1;
  padding: 8px;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.form-buttons {
  display: flex;
  gap: 10px;
}

.form button[type="submit"],
.cancel-btn {
  background: linear-gradient(90deg, #20c599 0%, #2dd9b5 100%);
  border: none;
  color: white;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.cancel-btn {
  background: #a0aec0;
  color: #1a202c;
}

.form button[type="submit"]:hover {
  background: #1ca085;
}

.cancel-btn:hover {
  background: #718096;
}

.event-list {
  margin-top: 20px;
}

.event-list ul {
  list-style: none;
  padding: 0;
}

.event-list li {
  background: #f8fafc;
  padding: 10px;
  margin-bottom: 8px;
  border-radius: 6px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.btn-supprimer {
  background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
  border: none;
  color: white;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.btn-supprimer:hover {
  background: #dc2626;
}

.btn-group {
  margin-top: 2rem;
  display: flex;
  justify-content: space-between;
}

.back-btn,
.send-btn {
  padding: 10px 20px;
  border-radius: 6px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  min-width: 120px;
}

.back-btn {
  background: #f5f7fa;
  color: #34495e;
  border: 2px solid #dfe6e9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.back-btn:hover {
  background: #e0e7ee;
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.send-btn {
  background: linear-gradient(135deg, #20c599, #1fae8d);
  color: white;
}

.send-btn:disabled {
  background: #a0aec0;
  cursor: not-allowed;
  transform: none;
}

.send-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #1ca085, #178467);
  transform: translateY(-3px);
}

.success-message {
  margin-top: 2rem;
  padding: 1.5rem;
  background: #c6f6d5;
  border-left: 4px solid #38a169;
  border-radius: 8px;
  color: #2f855a;
}
</style>
