<template>
  <div class="mon-espace">
    <!-- Barre Latérale -->
    <aside class="sidebar">
      <h2>Mon Espace Candidat</h2>
      <ul>
        <li
          @click="navigateTo('/monprofile')"
          :class="{ active: isActive('/monprofile') }"
          role="button"
          tabindex="0"
        >
          Mon profil
        </li>
        <li
          @click="navigateTo('/candidature')"
          :class="{ active: isActive('/candidature') }"
          role="button"
          tabindex="0"
        >
          Mes candidatures
        </li>
        <li
          @click="navigateTo('/entretiens')"
          :class="{ active: isActive('/entretiens') }"
          role="button"
          tabindex="0"
        >
          Mes entretiens
        </li>
        <li
          @click="navigateTo('/mesoffres')"
          :class="{ active: isActive('/mesoffres') }"
          role="button"
          tabindex="0"
        >
          Mes offres enregistrées
        </li>

        <li
          @click="navigateTo(`/mescv`)"
          :class="{ active: isActive(`/mescv`) }"
          role="button"
          tabindex="0"
        >
          Mes CV
        </li>

        <li @click="logout" class="logout-item" role="button" tabindex="0">
          Déconnexion
        </li>
      </ul>
    </aside>

    <!-- Contenu principal -->
    <main class="content">
      <router-view />
    </main>
  </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios"; // ton axios préconfiguré

const router = useRouter();
const route = useRoute();
const userData = ref(null); // Pas de type ici
const cvId = ref(null);

function isActive(path) {
  return route.path === path;
}

function navigateTo(path) {
  if (route.path !== path) {
    router.push(path);
  }
}

onMounted(async () => {
  // 1️⃣ Chargement de la session
  const raw =
    sessionStorage.getItem("userSession") || localStorage.getItem("userSession");

  if (!raw) {
    return router.push("/authentification");
  }
  userData.value = JSON.parse(raw);

  // 2️⃣ Récupération de l'ID du premier CV de l'utilisateur
  // try {
  //   const { data: cvs } = await axios.get("/api/cv", {
  //     params: { user_id: userData.value.id },
  //   });
  //   if (Array.isArray(cvs) && cvs.length) {
  //     cvId.value = String(cvs[0].id);
  //   }
  // } catch (e) {
  //   console.error("Erreur lors du chargement des CV :", e);
  // }

  // 3️⃣ Redirection par défaut si on est sur /CompteCandidat
  if (route.path === "/CompteCandidat") {
    router.push("/monprofile");
  }
});
async function logout() {
  try {
    // Nettoyage côté API
    await axios.post("/api/logout"); // Déconnexion côté serveur

    // Supprimer le cookie CSRF pour Sanctum (si utilisé)
    await axios.get("/sanctum/csrf-cookie"); // Cette requête rafraîchit le cookie CSRF si nécessaire
  } catch (error) {
    console.error("Erreur lors de la déconnexion:", error);
  } finally {
    // Supprimer les informations de session locales
    sessionStorage.removeItem("userSession");
    localStorage.removeItem("userSession");

    // Rediriger vers la page de connexion
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
