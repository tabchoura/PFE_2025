<template>
  <div class="candidatures-container">
    <div class="header-actions">
      <h2><i class="fas fa-user-check"></i> Mes candidatures</h2>
    </div>

    <!-- Gestion des états de chargement -->
    <div v-if="loading" class="loading-state">
      <p>Chargement des candidatures...</p>
    </div>

    <!-- Gestion des erreurs -->
    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="getCandidatures" class="btn-retry">
        <i class="fas fa-redo-alt"></i> Réessayer
      </button>
    </div>

    <!-- Affichage quand il n'y a pas de candidatures -->
    <div v-else-if="candidatures.length === 0" class="empty-state">
      <p>Aucune candidature faite pour le moment</p>
    </div>

    <!-- Affichage des candidatures -->
    <ul v-else class="candidature-grid">
      <li
        class="candidature-item"
        v-for="candidature in candidatures"
        :key="candidature.id"
      >
        <div class="candidature-info">
          <!-- Vérifier que l'offre est définie avant d'afficher -->
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
          <button class="voir-button" @click="voirDetails(candidature.id)">
            <i class="fas fa-eye"></i> Voir détails
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const candidatures = ref([]);
const loading = ref(true);
const error = ref(null);

// Fonction pour tronquer le texte
const truncateText = (text, maxLength) => {
  if (!text) return "";
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
};

// Fonction pour voir les détails d'une candidature
const voirDetails = (id) => {
  router.push(`/detailscandidature/${id}`);
};

// Fonction pour récupérer les candidatures depuis l'API
const getCandidatures = async () => {
  loading.value = true;
  error.value = null;

  try {
    await axios.get("/sanctum/csrf-cookie"); // Générer le cookie CSRF

    const response = await axios.get("/api/mescandidatures", {
      withCredentials: true, // Important pour envoyer les cookies d'authentification
      headers: {
        "X-Requested-With": "XMLHttpRequest", // Important pour Sanctum
        Accept: "application/json",
      },
    });

    console.log("Réponse API:", response.data);
    candidatures.value = response.data;
  } catch (err) {
    console.error("Erreur lors de la récupération des candidatures:", err);

    if (err.response) {
      // L'API a répondu avec un statut d'erreur
      if (err.response.status === 401) {
        error.value = "Vous devez être connecté pour voir vos candidatures.";
      } else {
        error.value = `Erreur: ${
          err.response.data.message || "Problème lors de la récupération des candidatures"
        }`;
      }
    } else if (err.request) {
      // La requête a été faite mais pas de réponse
      error.value =
        "Impossible de contacter le serveur. Vérifiez votre connexion internet.";
    } else {
      // Erreur lors de la configuration de la requête
      error.value =
        "Erreur lors de la récupération des candidatures. Veuillez réessayer.";
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  getCandidatures();
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

.header-actions {
  margin-bottom: 2rem;
}

.header-actions h2 {
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.error-state {
  color: #e74c3c;
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
  transition: background 0.3s;
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
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.candidature-info h3 {
  margin: 0;
  color: #3498db;
  font-size: 1.3rem;
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
  transition: background 0.3s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.voir-button:hover {
  background: #2980b9;
}

@media (min-width: 768px) {
  .candidature-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
