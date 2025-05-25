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
        <li v-for="(event, index) in events" :key="event.id">
          {{ event.name }} – {{ formatDate(event.date) }}
          <button
            class="btn-supprimer"
            @click="supprimerEvenement(index)"
            aria-label="Supprimer l'événement"
          >
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
              <path d="M3 6h18M9 6v12m6-12v12M10 11v6m4-6v6M5 6l1-2h12l1 2" />
            </svg>
            Supprimer
          </button>
        </li>
      </ul>
    </div>

    <!-- BOUTON RETOUR EN BAS -->
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import "@lbgm/pro-calendar-vue/style";

const router = useRouter();

const props = defineProps<{ modelValue: string | null }>();
const emit = defineEmits<{ "update:modelValue": [string | null] }>();

const events = ref<any[]>([]);
const formVisible = ref(false);
const titre = ref("");
const date = ref(""); // YYYY-MM-DD
const heure = ref(""); // HH:mm

const calendarConfig = {
  actions: {
    view: { enabled: true, icon: true, text: "Voir" },
    report: { enabled: false },
  },
  searchPlaceHolder: "Rechercher...",
  eventName: "Nouvel Événement",
  closeText: "Fermer",
};

function ajouterEvenement() {
  if (!date.value || !heure.value) {
    alert("Merci de saisir la date et l’heure");
    return;
  }

  const mysql = `${date.value} ${heure.value}:00`;

  events.value.push({
    id: Date.now().toString(),
    name: titre.value,
    comment: "Ajout manuel",
    date: mysql,
    keywords: titre.value,
  });

  emit("update:modelValue", mysql);

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
}

function supprimerEvenement(index: number) {
  events.value.splice(index, 1);
  if (!events.value.length) emit("update:modelValue", null);
}

function formatDate(str: string) {
  return new Date(str).toLocaleString("fr-FR", {
    dateStyle: "full",
    timeStyle: "short",
  });
}
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: auto;
  padding: 20px;
}
.btn-supprimer {
  background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
  color: white;
  border: none; /* Pas de bordure pour un style plus moderne */
  padding: 8px 14px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.95rem;
  display: inline-flex;
  align-items: center;
  gap: 6px; /* espace entre texte et icône si icône ajoutée */
  transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
  user-select: none;
  box-shadow: 0 2px 5px rgba(239, 68, 68, 0.5);
}

.btn-supprimer:hover {
  background: #dc2626;
  box-shadow: 0 6px 12px rgba(220, 38, 38, 0.6);
  transform: translateY(-2px);
}

.btn-supprimer:focus-visible {
  outline: 3px solid #f87171; /* contour clair pour accessibilité */
  outline-offset: 2px;
  box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.5);
}

.btn-supprimer:active {
  transform: translateY(0);
  box-shadow: 0 3px 6px rgba(220, 38, 38, 0.7);
}

/* Si tu veux ajouter une icône SVG à gauche (optionnel) */
.btn-supprimer svg {
  width: 16px;
  height: 16px;
  fill: white;
}

.back-btn {
  background: #f5f7fa;
  color: #34495e;
  border: 2px solid #dfe6e9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  margin-top: 2rem; /* espace au-dessus */
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s ease, transform 0.2s ease;
  display: inline-block;
}

.back-btn:hover {
  background: #e0e7ee;
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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

/* Regroupe les boutons du formulaire */
.form-buttons {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-shrink: 0;
}

/* Bouton Ajouter */
.form button[type="submit"] {
  background: linear-gradient(90deg, #20c599 0%, #2dd9b5 100%);
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.form button[type="submit"]:hover {
  background: #1976d2;
}

/* Bouton Annuler */
.cancel-btn {
  background: #a0aec0;
  color: #1a202c;
  border: none;
  padding: 8px 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.cancel-btn:hover {
  background: #718096;
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
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  color: #e74c3c;
  background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
  transition: transform 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
}
</style>
