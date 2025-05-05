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
      <button type="submit">Ajouter</button>
    </form>

    <!-- Calendrier -->
    <pro-calendar :events="events" :config="calendarConfig" />

    <!-- Liste des événements -->
    <div v-if="events.length" class="event-list">
      <h3>📅 Événements enregistrés :</h3>
      <ul>
        <li v-for="(event, index) in events" :key="event.id">
          {{ event.name }} – {{ formatDate(event.date) }}
          <button @click="supprimerEvenement(index)">🗑 Supprimer</button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import "@lbgm/pro-calendar-vue/style";

/* ---- v-model support ---- */
const props = defineProps<{ modelValue: string | null }>();
const emit = defineEmits<{ "update:modelValue": [string | null] }>();

/* ---- état ---- */
const events = ref<any[]>([]);
const formVisible = ref(false);
const titre = ref("");
const date = ref(""); // YYYY-MM-DD
const heure = ref(""); // HH:mm

/* ---- config calendrier (laisse tel quel) ---- */
const calendarConfig = {
  actions: {
    view: { enabled: true, icon: true, text: "Voir" },
    report: { enabled: false },
  },
  searchPlaceHolder: "Rechercher...",
  eventName: "Nouvel Événement",
  closeText: "Fermer",
};

/* ---- ajouter un évènement + remonter la date ---- */
function ajouterEvenement() {
  if (!date.value || !heure.value) {
    alert("Merci de saisir la date et l’heure");
    return;
  }

  // construit directement la chaîne MySQL
  const mysql = `${date.value} ${heure.value}:00`; // ex. 2025-05-10 14:00:00

  events.value.push({
    id: Date.now().toString(),
    name: titre.value,
    comment: "Ajout manuel",
    date: mysql,
    keywords: titre.value,
  });

  emit("update:modelValue", mysql); // ← envoie au parent

  // reset formulaire
  titre.value = "";
  date.value = "";
  heure.value = "";
  formVisible.value = false;
}

/* ---- supprimer ---- */
function supprimerEvenement(index: number) {
  events.value.splice(index, 1);
  if (!events.value.length) emit("update:modelValue", null);
}

/* ---- format pour l’affichage ---- */
function formatDate(str: string) {
  return new Date(str).toLocaleString("fr-FR", {
    dateStyle: "full",
    timeStyle: "short",
  });
}
</script>

<style scoped>
/* styles inchangés */
.container {
  max-width: 800px;
  margin: auto;
  padding: 20px;
}
.toggle-btn {
  background: #4caf50;
  color: #fff;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  margin-bottom: 10px;
  cursor: pointer;
  font-weight: bold;
}
.form {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}
.form input {
  flex: 1;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.form button {
  background: #2196f3;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
}
.event-list {
  margin-top: 30px;
}
.event-list ul {
  list-style: none;
  padding: 0;
}
.event-list li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f0f0f0;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 8px;
}
.event-list button {
  background: #e53935;
  color: #fff;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
}
</style>
