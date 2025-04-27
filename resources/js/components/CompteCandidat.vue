<template>
  <div class="mon-espace">
    <!-- Barre Latérale -->
    <aside class="sidebar">
      <h2>Mon Espace</h2>
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
          @click="navigateTo('/offres')"
          :class="{ active: isActive('/offres') }"
          role="button"
          tabindex="0"
        >
          Mes offres enregistrées
        </li>

        <!-- Mes CV (seulement si on a un cvId) -->
        <li
          v-if="cvId"
          @click="navigateTo(`/mescv`)"
          :class="{ active: isActive(`/mescv`) }"
          role="button"
          tabindex="0"
        >
          Mes CV
        </li>

        <li
          @click="navigateTo('/create-lettre')"
          :class="{ active: isActive('/create-lettre') }"
          role="button"
          tabindex="0"
        >
          Créer Lettre
        </li>
        <li
          @click="navigateTo('/test')"
          :class="{ active: isActive('/test') }"
          role="button"
          tabindex="0"
        >
          Mes Tests
        </li>
        <li
          @click="navigateTo('/notifications')"
          :class="{ active: isActive('/notifications') }"
          role="button"
          tabindex="0"
        >
          Mes Notifications
        </li>
        <li
          @click="logout"
          class="logout-item"
          role="button"
          tabindex="0"
        >
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

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from 'axios'   // ton axios préconfiguré

const router   = useRouter()
const route    = useRoute()
const userData = ref<any>(null)
const cvId     = ref<string | null>(null)

/**
 * Détermine si la route passée en argument correspond à la route active
 */
function isActive(path: string) {
  return route.path === path
}

/**
 * Navigue simplement vers la route donnée
 */
function navigateTo(path: string) {
  if (route.path !== path) {
    router.push(path)
  }
}

onMounted(async () => {
  // 1️⃣ Chargement de la session
  const raw = sessionStorage.getItem('userSession')
           || localStorage.getItem('userSession')

  if (!raw) {
    return router.push('/authentification')
  }
  userData.value = JSON.parse(raw)

  // 2️⃣ Récupération de l'ID du premier CV de l'utilisateur
  try {
    const { data: cvs } = await api.get('/api/cv', {
      params: { user_id: userData.value.id }
    })
    if (Array.isArray(cvs) && cvs.length) {
      cvId.value = String(cvs[0].id)
    }
  } catch (e) {
    console.error("Erreur lors du chargement des CV :", e)
  }

  // 3️⃣ Redirection par défaut si on est sur /CompteCandidat
  if (route.path === '/CompteCandidat') {
    router.push('/monprofile')
  }
})

/**
 * Effectue la déconnexion
 */
async function logout() {
  try {
    // Nettoyage côté API
    await api.post('/api/logout')
  } catch {
    /* ignore */
  } finally {
    sessionStorage.removeItem('userSession')
    localStorage.removeItem('userSession')
    router.push('/authentification')
  }
}
</script>

<style scoped>
.mon-espace {
  display: flex;
  min-height: 100vh;
  background-color: #f4f6f9;
  font-family: "Segoe UI", sans-serif;
}
.sidebar {
  width: 250px;
  position: fixed;
  top: 55px;
  left: 0;
  height: calc(100vh - 55px);
  background: #fff;
  padding: 30px;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  border-right: 1px solid #e1e1e1;
  z-index: 10;
}
.sidebar h2 {
  text-align: center;
  color: #0055a5;
  margin-bottom: 20px;
  font-weight: 700;
}
.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.sidebar li {
  margin-bottom: 12px;
  padding: 15px;
  cursor: pointer;
  text-align: center;
  border-radius: 8px;
  transition: background-color .3s, color .3s, transform .3s;
}
.sidebar li:hover,
.sidebar li.active {
  background: #cce0ff;
  color: #0055a5;
  transform: translateX(5px);
}
.logout-item {
  margin-top: 30px;
  border-top: 1px solid #e1e1e1;
  padding-top: 18px;
  color: #e74c3c;
  font-weight: 600;
}
.content {
  margin-left: 250px;
  flex: 1;
  padding: 60px;
  background: #fff;
  border-radius: 9px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
@media (max-width: 768px) {
  .mon-espace { flex-direction: column; }
  .sidebar {
    position: relative;
    width: 100%;
    height: auto;
    top: 0;
    border-right: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  .content {
    margin-left: 0;
    margin-top: 300px;
    padding: 20px;
  }
}
</style>
