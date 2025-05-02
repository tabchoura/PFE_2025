<template>
  <div class="candidatures-container">
    <div class="header-actions">
      <h2><i class="fas fa-user-check"></i> Mes candidatures</h2>
    </div>

    <div v-if="loading" class="loading-state">
      <p>Chargement des candidatures...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="getCandidatures" class="btn-retry">
        <i class="fas fa-redo-alt"></i> Réessayer
      </button>
    </div>

    <div v-else-if="candidatures.length === 0" class="empty-state">
      <p>Aucune candidature faite pour le moment</p>
    </div>

    <ul v-else class="candidature-grid">
      <li
        class="candidature-item"
        v-for="candidature in candidatures"
        :key="candidature.id"
      >
        <div class="candidature-info">
          <h3 class="title-offre" v-if="candidature.offre">
            {{ candidature.offre.titre }}
          </h3>
          <p v-else>Aucune offre associée</p>
        </div>
        <div class="candidature-card-body">
          <p v-if="candidature.offre">
            <strong>Description :</strong>
            {{ truncateText(candidature.offre.description, 100) }}
          </p>
          <p v-if="candidature.offre">
            <strong>Salaire :</strong> {{ candidature.offre.salaire }}
          </p>
          <p><strong>Statut :</strong> {{ candidature.statut || "En attente" }}</p>
          <p>
            <strong>Message envoyé :</strong> {{ truncateText(candidature.message, 120) }}
          </p>
        </div>
        <div class="candidature-card-footer">
          <button
            @click="voirDetails(candidature.offre?.id, candidature.cv?.id)"
            class="voir-button"
          ></button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const candidatures = ref([]);
const loading = ref(true);
const error = ref(null);

const truncateText = (text, maxLength) => {
  if (!text) return "";
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
};

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

const getCandidatures = async () => {
  loading.value = true;
  error.value = null;

  try {
    await axios.get("/sanctum/csrf-cookie");
    const response = await axios.get("/api/mescandidatures", {
      withCredentials: true,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
      },
    });
    candidatures.value = response.data;
  } catch (err) {
    console.error("Erreur lors de la récupération des candidatures:", err);
    if (err.response) {
      if (err.response.status === 401) {
        error.value = "Vous devez être connecté pour voir vos candidatures.";
      } else {
        error.value = `Erreur: ${
          err.response.data.message || "Problème lors de la récupération des candidatures"
        }`;
      }
    } else if (err.request) {
      error.value =
        "Impossible de contacter le serveur. Vérifiez votre connexion internet.";
    } else {
      error.value =
        "Erreur lors de la récupération des candidatures. Veuillez réessayer.";
    }
  } finally {
    loading.value = false;
  }
};

onMounted(getCandidatures);
</script>

<style scoped>
.candidatures-container {
  max-width: 900px;
  margin: 2rem auto;
  padding: 2rem;
  background: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  overflow: hidden;
}

.header-actions {
  margin-bottom: 2rem;
}

.header-actions h2 {
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.6rem;
}

.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.error-state {
  color: #e74c3c;
  font-weight: bold;
}

.btn-retry {
  margin-top: 1rem;
  background: #3498db;
  color: white;
  border: none;
  padding: 0.7rem 1.2rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.btn-retry:hover {
  background: #2980b9;
}

.candidature-grid {
  list-style: none;
  padding: 0;
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

.candidature-item {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  gap: 1rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.candidature-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.candidature-info h3 {
  margin: 0;
  color: #3498db;
  font-size: 1.3rem;
  font-weight: 600;
}

.candidature-card-body p {
  margin: 0.5rem 0;
  color: #34495e;
}

.candidature-card-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 1rem;
}

.voir-button {
  background: #3498db;
  color: white;
  border: none;
  padding: 0.7rem 1.2rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease, transform 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.voir-button:hover {
  background: #2980b9;
  transform: translateY(-2px);
}

.voir-button i {
  font-size: 1.1rem;
}

@media (min-width: 768px) {
  .candidature-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .header-actions h2 {
    font-size: 1.4rem;
  }
  .candidature-item {
    padding: 1rem;
  }
}
</style>
