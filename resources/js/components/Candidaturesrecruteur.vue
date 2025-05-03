<template>
  <div class="candidatures-container">
    <h1>Candidatures Reçues</h1>

    <div v-if="loading" class="loading">Chargement...</div>

    <div v-else-if="candidatures.length === 0" class="empty">
      Aucune candidature reçue pour le moment.
    </div>

    <ul v-else>
      <li
        v-for="candidature in candidatures"
        :key="candidature.id"
        class="candidature-item"
      >
        <div class="candidature-info">
          <h3>{{ candidature.offre?.titre || "Offre inconnue" }}</h3>
          <p>Candidat : {{ candidature.cv?.prenom }} {{ candidature.cv?.nom }}</p>
          <p>Date de candidature : {{ formatDate(candidature.created_at) }}</p>
          <P>Statut : {{ candidature.statut }}</P>
        </div>

        <button
          @click="voirDetails(candidature.offre?.id, candidature.cv?.id)"
          class="voir-button"
        >
          Voir plus
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const candidatures = ref<any[]>([]);
const loading = ref(true);
const router = useRouter();

function formatDate(dateStr: string) {
  const date = new Date(dateStr);
  return date.toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

function voirDetails(offerId?: number, cvId?: number) {
  if (!offerId || !cvId) {
    console.error("ID manquant pour la redirection vers les détails de la candidature.");
    return;
  }
  router.push({
    name: "DetailsCandidature",
    params: {
      offerId: offerId.toString(),
      cvId: cvId.toString(),
    },
  });
}

onMounted(async () => {
  try {
    const { data } = await axios.get("/api/candidatures");
    candidatures.value = data;
  } catch (err) {
    console.error("Erreur lors du chargement des candidatures", err);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.candidatures-container {
  max-width: 900px;
  margin: 2rem auto;
  padding: 2rem;
  background: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

h1 {
  text-align: center;
  margin-bottom: 2rem;
  color: #2c3e50;
}

.loading,
.empty {
  text-align: center;
  color: #7f8c8d;
  font-style: italic;
}

ul {
  list-style: none;
  padding: 0;
}

.candidature-item {
  background: white;
  margin-bottom: 1.5rem;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.candidature-info h3 {
  margin: 0;
  color: #3498db;
  font-size: 1.3rem;
}

.candidature-info p {
  margin: 0.3rem 0;
  color: #34495e;
}

.voir-button {
  background: #3498db;
  color: white;
  border: none;
  padding: 0.7rem 1.2rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.voir-button:hover {
  background: #2980b9;
}
</style>
