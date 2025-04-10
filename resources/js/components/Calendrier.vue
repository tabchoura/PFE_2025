<template>
  <div class="container">
    <!-- Bouton pour afficher/masquer le formulaire -->
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

    <!-- Liste des événements avec bouton Supprimer -->
    <div v-if="events.length" class="event-list">
      <h3>📅 Événements enregistrés :</h3>
      <ul>
        <li v-for="(event, index) in events" :key="event.id">
          {{ event.name }} - {{ formatDate(event.date) }}
          <button @click="supprimerEvenement(index)">🗑 Supprimer</button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import '@lbgm/pro-calendar-vue/style';

const events = ref([]);
const formVisible = ref(false);
const titre = ref('');
const date = ref('');
const heure = ref('');

const calendarConfig = ref({
  actions: {
    view: { enabled: true, icon: true, text: 'Voir' },
    report: { enabled: false },
  },
  searchPlaceHolder: 'Rechercher...',
  eventName: 'Nouvel Événement',
  closeText: 'Fermer',
});

function ajouterEvenement() {
  const dateTime = new Date(`${date.value}T${heure.value}`);
  events.value.push({
    id: Date.now().toString(),
    name: titre.value,
    comment: 'Ajout manuel',
    date: dateTime.toISOString(),
    keywords: titre.value,
  });

  titre.value = '';
  date.value = '';
  heure.value = '';
  formVisible.value = false;
}

function supprimerEvenement(index) {
  events.value.splice(index, 1);
}

function formatDate(dateString) {
  const options = { dateStyle: 'full', timeStyle: 'short' };
  return new Date(dateString).toLocaleString('fr-FR', options);
}
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: auto;
  padding: 20px;
}
.toggle-btn {
  background-color: #4caf50;
  color: white;
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
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  flex: 1;
}
.form button {
  background-color: #2196f3;
  color: white;
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
  background-color: #e53935;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
}
</style>
