<template>
  <div class="mon-espace">
    <aside class="sidebar">
      <h2>Mon Espace</h2>
      <ul>
        <li @click="current = 'profil'" :class="{ active: current === 'profil' }">
          Mon profil
        </li>
        <li
          @click="current = 'candidatures'"
          :class="{ active: current === 'candidatures' }"
        >
          Mes candidatures
        </li>
        <li @click="current = 'entretiens'" :class="{ active: current === 'entretiens' }">
          Mes entretiens
        </li>
        <li @click="current = 'offres'" :class="{ active: current === 'offres' }">
          Mes offres
        </li>
        <li @click="current = 'documents'" :class="{ active: current === 'documents' }">
          Mes CV & Lettres
        </li>
        <li @click="logout">Déconnexion</li>
      </ul>
    </aside>

    <main class="content">
      <component :is="currentComponent" />
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import Monprofile from "../components/Monprofile.vue";
import Candidature from "../components/Candidature.vue";
import Entretiens from "../components/Entretiens.vue";
import Mesoffres from "../components/Mesoffres.vue";
import MesCvEtLettres from "../components/MesCvEtLettres.vue";

const current = ref("profil");

const currentComponent = computed(() => {
  return (
    {
      profil: Monprofile,
      candidatures: Candidature,
      entretiens: Entretiens,
      offres: Mesoffres,
      documents: MesCvEtLettres,
    }[current.value] || Monprofile
  );
});

function logout() {
  localStorage.clear();
  window.location.href = "/authentification";
}
</script>

<style scoped>
.mon-espace {
  display: flex;
  min-height: 100vh;
  font-family: Arial, sans-serif;
}

.sidebar {
  width: 250px;
  background-color: #f4f6f8;
  padding: 20px;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
}

.sidebar h2 {
  margin-bottom: 20px;
  font-size: 1.4rem;
  color: #0055a5;
}

.sidebar ul {
  list-style: none;
  padding: 0;
}

.sidebar li {
  padding: 12px;
  cursor: pointer;
  border-radius: 5px;
  margin-bottom: 8px;
  color: #333;
  transition: 0.2s;
}

.sidebar li.active,
.sidebar li:hover {
  background-color: #e1ecf8;
  color: #0055a5;
}

.content {
  flex: 1;
  padding: 40px;
  background: #ffffff;
}
</style>
