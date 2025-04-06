<template>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="welcome-box">
          <h2 class="welcome-title">Bienvenue 👋</h2>
          <p class="welcome-user">{{ profile.prenom }} {{ profile.nom }}</p>
        </div>
      </div>
      <nav class="nav-links">
        <button
          v-for="section in navigationSections"
          :key="section.id"
          class="nav-btn"
          :class="{
            active: currentSection === section.id,
            'logout-btn': section.id === 'logout',
          }"
          @click="handleNavigation(section.id)"
          :aria-label="`Naviguer vers ${section.name}`"
        >
          <i :class="section.icon" />
          <span>{{ section.name }}</span>
        </button>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="main-content">
      <div class="content-card">
        <component :is="currentComponent" />
      </div>
    </main>
  </div>
</template>

<script>
import Monprofile from "../components/Monprofile.vue";
import Candidature from "../components/Candidature.vue";
import Entretiens from "../components/Entretiens.vue";

import Test from "../components/Test.vue";

export default {
  name: "CandidateProfile",
  components: {
    Monprofile,
    Candidature,
    Test,
    Entretiens,
  },

  data() {
    return {
      profile: {
        nom: "",
        prenom: "",
      },
      currentSection: "profile",
      navigationSections: [
        { id: "profile", name: "Mon profil", icon: "fas fa-user" },
        { id: "applications", name: "Mes candidatures", icon: "fas fa-briefcase" },
        { id: "tests", name: "Mes tests", icon: "fas fa-vial" },
        { id: "interviews", name: "Mes entretiens", icon: "fas fa-calendar-alt" },
        { id: "cv", name: "Creer un cv ", icon: "fas fa-graduation-cap" },
        { id: "lettre", name: "Creer lettre de motivation", icon: "fas fa-cog" },
        { id: "logout", name: "Déconnexion", icon: "fas fa-sign-out-alt" },
      ],
    };
  },

  computed: {
    currentComponent() {
      const map = {
        profile: this.$options.components.Monprofile,
        applications: this.$options.components.Candidature,
        tests: this.$options.components.Test,
        interviews: this.$options.components.Entretiens,
      };

      return (
        map[this.currentSection] || {
          template: "<div><p>📌 Composant introuvable.</p></div>",
        }
      );
    },
  },

  mounted() {
    const data = JSON.parse(localStorage.getItem("candidat") || "{}");
    this.profile.nom = data.nom || "";
    this.profile.prenom = data.prenom || "";
  },

  methods: {
    handleNavigation(sectionId) {
      if (sectionId === "logout") {
        this.logout();
      } else {
        this.currentSection = sectionId;
      }
    },
    logout() {
      localStorage.removeItem("candidat");
      alert("Vous avez été déconnecté(e).");
      window.location.href = "/login";
    },
  },
};
</script>

<style scoped>
.dashboard-container {
  display: flex;
  min-height: 100vh;
  background-color: #f3f4f6;
}

.sidebar {
  width: 16rem;
  background-color: #fff;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
  padding: 1.25rem;
  display: none;
}

@media (min-width: 768px) {
  .sidebar {
    display: block;
    flex-shrink: 0;
  }
}

.sidebar-header {
  margin-bottom: 2rem;
}

.welcome-box {
  text-align: center;
  margin-bottom: 2rem;
}

.welcome-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
}

.welcome-user {
  font-size: 1rem;
  font-weight: bold;
  color: #2563eb;
}

.nav-links {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.nav-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
  background: transparent;
  border: none;
  padding: 0.75rem;
  width: 100%;
  text-align: left;
  color: #374151;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background 0.2s ease;
}

.nav-btn:hover {
  background-color: #f0f4ff;
  color: #2563eb;
}

.nav-btn.active {
  background-color: #e0e7ff;
  color: #2563eb;
  font-weight: 600;
}

.logout-btn {
  background-color: #fee2e2;
  color: #b91c1c;
  font-weight: 600;
}

.logout-btn:hover {
  background-color: #fecaca;
  color: #991b1b;
}

.main-content {
  flex: 1;
  padding: 1rem;
  background-color: #f9fafb;
}

@media (min-width: 768px) {
  .main-content {
    padding: 2.5rem;
    margin-left: 16rem;
  }
}

.content-card {
  background-color: #ffffff;
  border-radius: 0.5rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
</style>
