<template>
  <div class="mon-espace">
    <!-- Barre Latérale -->
    <aside class="sidebar">
      <h2>Mon Espace Recruteur</h2>
      <ul>
        <li @click="navigateTo('/monprofilerecruteur')" :class="{ active: isActive('/monprofilerecruteur') }" role="button" tabindex="0">
          Mon profil
        </li>
        <li @click="navigateTo('/Candidaturesrecruteur')">
          Candidatures reçues
        </li>
        <li @click="navigateTo('/Entretiensrecruteurs')" :class="{ active: isActive('/Entretiensrecruteurs') }">
  Mes entretiens
</li>

        <li @click="navigateTo('/offres')" :class="{ active: isActive('/offres') }" role="button" tabindex="0">
          Gérer les offres
        </li>
        <li @click="logout" class="logout-item" role="button" tabindex="0">
          Déconnexion
        </li>
      </ul>
    </aside>

    <!-- Contenu principal -->
    <main class="content">
      <!-- Affichage dynamique du composant -->
      <router-view></router-view> <!-- Ce composant affichera dynamiquement le composant associé à la route -->
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/axios";

// Initialiser le router et la route
const router = useRouter();
const route = useRoute();
const userData = ref(null);

// Vérifier si une route est active
function isActive(path) {
  return route.path === path;
}

// Fonction de navigation vers une page
function navigateTo(path) {
  if (route.path !== path) {
    router.push(path);
  }
}

// Vérifier la session utilisateur à l'initialisation
onMounted(() => {
  const session = JSON.parse(
    sessionStorage.getItem("userSession") || localStorage.getItem("userSession") || "null"
  );
  if (session) {
    userData.value = session;

    // Si l'utilisateur accède directement à /CompteCandidat sans section spécifique
    if (route.path === '/CompteRecruteur') {
      router.push('/monprofilerecruteur');  // Redirige automatiquement vers la page par défaut (par exemple 'Monprofile')
    }
  } else {
    router.push("/authentification");  // Redirige vers la page d'authentification si non connecté
  }
});
// Déconnexion
async function logout() {
  try {
    const session = JSON.parse(
      sessionStorage.getItem('userSession') || localStorage.getItem('userSession') || '{}'
    );

    const token = session.token;
    if (token) {
      // Ajoute le token dans l'en-tête Authorization pour que Laravel reconnaisse l'utilisateur
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    await api.post("/api/logout"); // Laravel va supprimer les tokens ici
  } catch (error) {
    console.error("Erreur lors de la déconnexion :", error.response?.data || error.message);
  } finally {
    // Supprimer la session même s'il y a une erreur
    localStorage.removeItem("userSession");
    sessionStorage.removeItem("userSession");
    // Redirige vers la page de connexion
    router.push("/authentification");
  }
}

</script>

<style scoped>
/* Styles pour l'ensemble du composant */
.mon-espace {
  min-height: 100vh;
  font-family: "Roboto", sans-serif;
  background-color: #f4f6f9;
  padding: 20px;
  margin: 30px;
  display: flex;
  justify-content: flex-start;
  align-items: flex-start;
}

/* Barre Latérale */
.sidebar {
  width: 250px;
  background-color: #fff;
  padding: 30px;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  border-right: 1px solid #e1e1e1;
  position: fixed;
  top: 55px;
  left: 0;
  height: calc(100vh - 55px); /* Fixer la barre latérale */
  z-index: 10;
}

.sidebar h2 {
  margin-bottom: 20px;
  font-size: 1.6rem;
  color: #0055a5;
  font-weight: 700;
  text-align: center;
}

.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar li {
  padding: 15px;
  cursor: pointer;
  border-radius: 8px;
  margin-bottom: 12px;
  color: #333;
  font-size: 1rem;
  transition: background-color 0.3s, color 0.3s;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sidebar li.active,
.sidebar li:hover {
  background-color: #cce0ff;
  color: #0055a5;
  transform: translateX(5px);
}

.logout-item {
  margin-top: 30px;
  border-top: 1px solid #e1e1e1;
  padding-top: 18px;
  color: #e74c3c;
  font-weight: 600;
  text-align: center;
  transition: background-color 0.3s, color 0.3s;
}

.logout-item:hover {
  background-color: #f9e6e6;
  color: #c0392b;
}

.content {
  flex: 1;
  padding: 60px;
  background: #ffffff;
  overflow-y: auto;
  margin-left: 250px;
  transition: margin-left 0.3s ease;
  border-radius: 9px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

@media (max-width: 768px) {
  .mon-espace {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: absolute;
    top: 0;
    left: 0;
    z-index: 10;
    padding: 15px;
    background-color: #fff;
    height: auto;
  }

  .content {
    margin-left: 0;
    margin-top: 300px;
    padding: 20px;
  }

  .sidebar h2 {
    font-size: 1.4rem;
    text-align: left;
  }

  .sidebar ul {
    padding-left: 10px;
  }

  .sidebar li {
    padding: 12px;
  }
}
</style>
