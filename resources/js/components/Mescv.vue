<template>
  <div class="mes-cv-container">
    <h1>Mes CV</h1>

    <ul v-if="cvs.length">
      <li v-for="cv in cvs" :key="cv.id">
        <router-link :to="{ name: 'VoirCv', params: { id: cv.id } }">
          {{ cv.prenom }} {{ cv.nom }}
        </router-link>
      </li>
    </ul>

    <p v-else>
      Vous n'avez encore créé aucun CV.
    </p>

    <button @click="ajoutercv" class="ajouter-button">➕ Ajouter un nouveau CV</button>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

// Interface pour les CV
interface Cv {
  id: number
  prenom: string
  nom: string
}

const cvs = ref<Cv[]>([])

// 🔵 Correction ici : fonction ajoutercv
function ajoutercv() {
  router.push({ name: 'CreerCv', query: { from: 'mescv' } })
}


onMounted(async () => {
  try {
    const { data } = await axios.get('/api/cv')
    cvs.value = data
  } catch (err) {
    console.error('Erreur lors du chargement des CV :', err)
  }
})
</script>

<style scoped>
.mes-cv-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2rem;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.mes-cv-container h1 {
  text-align: center;
  margin-bottom: 2rem;
  color: #2c3e50;
  font-weight: 600;
  position: relative;
}

.mes-cv-container h1::after {
  content: "";
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 50px;
  height: 3px;
  background-color: #3498db;
  border-radius: 3px;
}

ul {
  list-style: none;
  padding: 0;
}

li {
  background-color: white;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

li:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

li + li {
  margin-top: 0.8rem;
}

a {
  color: #3498db;
  text-decoration: none;
  display: block;
  padding: 0.8rem 1rem;
  font-weight: 500;
}

a:hover {
  text-decoration: none;
  color: #2980b9;
}

p {
  text-align: center;
  color: #7f8c8d;
  font-style: italic;
  padding: 1.5rem;
  background-color: white;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Bouton ajouter CV */
.ajouter-button {
  display: block;
  margin: 2rem auto 0;
  padding: 0.8rem 1.5rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.ajouter-button:hover {
  background-color: #2980b9;
}

@media (max-width: 600px) {
  .mes-cv-container {
    padding: 1.5rem;
    margin: 1rem;
  }
}
</style>
