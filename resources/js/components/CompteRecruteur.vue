<template>
  <div class="mon-espace">
    <!-- Barre Latérale -->
    <aside class="sidebar">
      <h2>Mon Espace Recruteur</h2>
      <ul>
        <li
          @click="navigateTo('/monprofilerecruteur')"
          :class="{ active: isActive('/monprofilerecruteur') }"
          role="button"
          tabindex="0"
        >
          Mon profil
        </li>
        <li
          @click="navigateTo('/Candidaturesrecruteur')"
          :class="{ active: isActive('/Candidaturesrecruteur') }"
          role="button"
          tabindex="0"
        >
          Candidatures reçues
        </li>
        <li
          @click="navigateTo('/entretiensrecrutuer')"
          :class="{ active: isActive('/entretiensrecrutuer') }"
          role="button"
          tabindex="0"
        >
          Entretiens
        </li>

        <li
          @click="navigateTo('/Offresrecruteur')"
          :class="{ active: isActive('/Offresrecruteur') }"
          role="button"
          tabindex="0"
        >
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
      <router-view></router-view>
      <!-- Ce composant affichera dynamiquement le composant associé à la route -->
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
    if (route.path === "/CompteRecruteur") {
      router.push("/monprofilerecruteur"); // Redirige automatiquement vers la page par défaut (par exemple 'Monprofile')
    }
  } else {
    router.push("/authentification"); // Redirige vers la page d'authentification si non connecté
  }
});
// Déconnexion
async function logout() {
  try {
    const session = JSON.parse(
      sessionStorage.getItem("userSession") || localStorage.getItem("userSession") || "{}"
    );

    const token = session.token;
    if (token) {
      // Ajoute le token dans l'en-tête Authorization pour que Laravel reconnaisse l'utilisateur
      api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    }

    await api.post("/api/logout"); // Laravel va supprimer les tokens ici
  } catch (error) {
    console.error(
      "Erreur lors de la déconnexion :",
      error.response?.data || error.message
    );
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
  background-color: #f5f7fa;
  display: flex;
  justify-content: flex-start;
  align-items: flex-start;
  position: relative;
  padding: 0;
}

/* Barre Latérale */
.sidebar {
  width: 280px;
  background: linear-gradient(135deg, #1a365d 0%, #0055a5 100%);
  padding: 30px 20px;
  box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  z-index: 100;
  color: #fff;
  overflow-y: auto;
  margin-top: 60px;

  transition: all 0.3s ease;
}

.sidebar h2 {
  margin-bottom: 30px;
  font-size: 1.5rem;
  color: #ffffff;
  font-weight: 600;
  text-align: center;
  padding-bottom: 15px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  letter-spacing: 0.5px;
}

.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 10px 0;
}

.sidebar li {
  padding: 14px 16px;
  cursor: pointer;
  border-radius: 8px;
  margin-bottom: 8px;
  color: rgba(255, 255, 255, 0.85);
  font-size: 0.95rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  position: relative;
  font-weight: 500;
  letter-spacing: 0.3px;
}

.sidebar li::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 4px;
  background-color: transparent;
  border-radius: 0 4px 4px 0;
  transition: all 0.2s ease;
}

.sidebar li.active {
  background-color: rgba(255, 255, 255, 0.15);
  color: #ffffff;
}

.sidebar li.active::before {
  background-color: #4ac5ff;
}

.sidebar li:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: #ffffff;
  transform: translateX(3px);
}

.logout-item {
  margin-top: 40px !important;
  color: rgba(255, 255, 255, 0.7) !important;
  background-color: rgba(231, 76, 60, 0.15);
  transition: all 0.2s ease;
}

.logout-item:hover {
  background-color: rgba(231, 76, 60, 0.25) !important;
  color: #ffffff !important;
}

/* Contenu principal */
.content {
  flex: 1;
  min-height: 100vh;
  background: #ffffff;
  overflow-y: auto;
  margin-left: 280px;
  padding: 40px;
  transition: all 0.3s ease;
  box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.03);
}

@media (max-width: 1024px) {
  .sidebar {
    width: 240px;
  }

  .content {
    margin-left: 240px;
    padding: 30px;
  }
}

@media (max-width: 768px) {
  .mon-espace {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
    padding: 15px;
    border-radius: 0 0 15px 15px;
  }

  .sidebar h2 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    text-align: center;
  }

  .sidebar ul {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
  }

  .sidebar li {
    padding: 10px 15px;
    flex: 0 0 auto;
    min-width: auto;
    width: calc(50% - 10px);
    margin-bottom: 6px;
    font-size: 0.85rem;
    justify-content: center;
  }

  .sidebar li::before {
    display: none;
  }

  .logout-item {
    width: 100%;
    margin-top: 10px !important;
  }

  .content {
    margin-left: 0;
    padding: 25px 15px;
    border-radius: 15px 15px 0 0;
    margin-top: -15px;
  }
}

@media (max-width: 480px) {
  .sidebar li {
    width: 100%;
  }

  .content {
    padding: 20px 12px;
  }
}
</style>
