<template>
  <div class="mon-espace">
    <!-- Barre Latérale -->
    <aside class="sidebar">
      <h2>Mon Espace</h2>
      <ul>
        <li
          @click="navigateTo('profil')"
          :class="{ active: current === 'profil' }"
          role="button"
          tabindex="0"
        >
          Mon profil
        </li>
        <li
          @click="navigateTo('candidatures')"
          :class="{ active: current === 'candidatures' }"
          role="button"
          tabindex="0"
        >
          Mes candidatures
        </li>
        <li
          @click="navigateTo('entretiens')"
          :class="{ active: current === 'entretiens' }"
          role="button"
          tabindex="0"
        >
          Mes entretiens
        </li>
        <li
          @click="navigateTo('offres')"
          :class="{ active: current === 'offres' }"
          role="button"
          tabindex="0"
        >
          Mes offres Enregistrées
        </li>
        <li
          @click="navigateTo('documents')"
          :class="{ active: current === 'documents' }"
          role="button"
          tabindex="0"
        >
          Mes CV & Lettres
        </li>
        <li
          @click="navigateTo('tests')"
          :class="{ active: current === 'tests' }"
          role="button"
          tabindex="0"
        >
          Mes Tests
        </li>
        <li
          @click="navigateTo('notifications')"
          :class="{ active: current === 'notifications' }"
          role="button"
          tabindex="0"
        >
          Mes Notifications
        </li>
        <!-- Déconnexion -->
        <li @click="logout" class="logout-item" role="button" tabindex="0">
          Déconnexion
        </li>
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
import Monprofile from "../components/Monprofile.vue";
import Candidature from "../components/Candidature.vue";
import Entretiens from "../components/Entretiens.vue";
import Mesoffres from "../components/Mesoffres.vue";
import MesCvEtLettres from "../components/MesCvEtLettres.vue";
import MesTests from "../components/MesTests.vue";
import MesNotifications from "../components/MesNotifications.vue";

// Initialiser le router au niveau supérieur du script setup
const router = useRouter();

// Déclaration des variables réactives
const current = ref("profil");
const userData = ref(null);

// Définir le composant en fonction de la page sélectionnée
const currentComponent = computed(() => {
  return (
    {
      profil: Monprofile,
      candidatures: Candidature,
      entretiens: Entretiens,
      offres: Mesoffres,
      documents: MesCvEtLettres,
      tests: MesTests,
      notifications: MesNotifications,
    }[current.value] || Monprofile
  );
});

// Fonction de navigation vers une page
function navigateTo(page) {
  current.value = page;
}

// Vérifier la session utilisateur à l'initialisation
// Dans la partie <script setup> et la fonction onMounted()

onMounted(() => {
  // Lire d'abord dans sessionStorage, puis dans localStorage
  const session = JSON.parse(
    sessionStorage.getItem("userSession") || localStorage.getItem("userSession") || "null"
  );
  if (session) {
    userData.value = session;
  } else {
    // Si l'utilisateur n'est pas connecté, redirige vers l'authentification
    router.push("/authentification");
  }
});


// Déconnexion
function logout() {
  // Supprimer la session et rediriger vers la page de connexion
  localStorage.removeItem("userSession");
  sessionStorage.removeItem("userSession");
  router.push("/authentification");
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
  height: calc(100vh - 55px); /* Pour que la sidebar prenne toute la hauteur de la page */
  z-index: 10;
}

.sidebar h2 {
  margin-bottom: 20px;
  font-size: 1.6rem;
  color: #0055a5;
  font-weight: 700;
  text-align: center; /* Centrer le titre de la sidebar */
}

.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar li {
  padding: 15px;
  cursor: pointer;
  border-radius: 8px; /* Coins plus arrondis */
  margin-bottom: 12px;
  color: #333;
  font-size: 1rem;
  transition: background-color 0.3s, color 0.3s; /* Transitions douces pour hover */
  text-align: center; /* Aligner le texte au centre */
  display: flex;
  align-items: center; /* Centrer verticalement les éléments */
  justify-content: center; /* Centrer horizontalement */
}

.sidebar li.active,
.sidebar li:hover {
  background-color: #cce0ff; /* Couleur de fond au survol plus douce */
  color: #0055a5;
  transform: translateX(
    5px
  ); /* Ajouter un petit effet de translation pour l'interaction */
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
  margin-left: 250px; /* Décalage pour le contenu après la sidebar */
  transition: margin-left 0.3s ease;
  border-radius: 9px; /* Ajouter une légère bordure arrondie */
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* Ombre discrète autour du contenu */
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
  }

  .content {
    margin-left: 0;
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
