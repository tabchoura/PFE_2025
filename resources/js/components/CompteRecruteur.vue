<template>
  <div class="mon-espace">
    <!-- Barre Latérale -->
    <aside class="sidebar">
      <h2>Mon Espace Recruteur</h2>
      <ul>
        <li @click="navigateTo('profil')" :class="{ active: current === 'profil' }">
          Mon profil
        </li>
        <li @click="navigateTo('offres')" :class="{ active: current === 'offres' }">
          Gérer les offres
        </li>
        <li
          @click="navigateTo('candidatures')"
          :class="{ active: current === 'candidatures' }"
        >
          Candidatures reçues
        </li>
        <li
          @click="navigateTo('entretiens')"
          :class="{ active: current === 'entretiens' }"
        >
          Mes entretiens
        </li>
        <li @click="logout" class="logout-item">Déconnexion</li>
      </ul>
    </aside>

    <!-- Contenu principal -->
    <main class="content">
      <!-- Composant dynamique -->
      <component :is="currentComponent" />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";

// Initialiser le router au niveau supérieur du script setup
const router = useRouter();

// Importation des composants nécessaires
import Monprofilerecruteur from "@/components/Monprofilerecruteur.vue"; // Assurez-vous d'utiliser un chemin valide
import Offres from "@/components/Offres.vue";
import Candidature from "@/components/Candidature.vue";
import Entretiens from "@/components/Entretiens.vue";

// Déclaration des variables réactives
const current = ref("profil");
const userData = ref(null);

// Définir le composant dynamique en fonction de la page sélectionnée
const currentComponent = computed(() => {
  return (
    {
      profil: Monprofilerecruteur,
      offres: Offres,
      candidatures: Candidature,
      entretiens: Entretiens,
    }[current.value] || Monprofilerecruteur
  );
});

// Fonction de navigation vers une page
function navigateTo(page) {
  current.value = page;
}

// Vérifier la session utilisateur à l'initialisation
onMounted(() => {
  const session = JSON.parse(
    localStorage.getItem("userSession") || sessionStorage.getItem("userSession") || "null"
  );
  if (session && session.type === "recruteur") {
    userData.value = session;
  } else {
    router.push("/authentification");
  }
});

import api from "@/axios";

async function logout() {
  try {
    await api.post("/api/logout"); // Laravel va supprimer la session côté serveur
  } catch (error) {
    console.error("Erreur lors de la déconnexion :", error);
  } finally {
    localStorage.removeItem("userSession");
    sessionStorage.removeItem("userSession");
    router.push("/authentification");
  }
}

</script>

<style scoped>
/* Ajoutez ici vos styles CSS pour le composant */
.mon-espace {
  min-height: 100vh;
  font-family: "Roboto", sans-serif;
  background-color: #f4f6f9;
  padding: 20px;
  margin: 30px;
}
.sidebar {
  width: 250px;
  background-color: #fff;
  padding: 30px;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
  border-right: 1px solid #e1e1e1;
  position: fixed;
  top: 55px;
  left: 0;
  height: calc(100vh - 55px);
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
}

.sidebar li.active,
.sidebar li:hover {
  background-color: #cce0ff;
  color: #0055a5;
}

.logout-item {
  margin-top: 30px;
  color: #e74c3c;
  font-weight: 600;
  text-align: center;
}

.content {
  flex: 1;
  padding: 60px;
  background: #ffffff;
  overflow-y: auto;
  margin-left: 250px;
  transition: margin-left 0.3s ease;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

@media (max-width: 768px) {
  .mon-espace {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    padding: 15px;
  }

  .content {
    margin-left: 0;
    padding: 20px;
  }
}
</style>
