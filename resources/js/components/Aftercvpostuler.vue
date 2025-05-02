<template>
  <div class="cv-container">
    <div v-if="isLoading" class="loading-container">
      <div class="loader"></div>
      <p class="loading-text">Chargement du CV…</p>
    </div>

    <div v-else-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="loadCv" class="retry-btn">Réessayer</button>
    </div>

    <div v-else ref="cvElement" class="cv-form preview-form">
      <div class="cv-left-column">
        <div class="profile-section">
          <div class="profile-picture-container">
            <div class="profile-picture" :style="profileImageStyle" />
          </div>
        </div>
        <div class="section">
          <h1 class="cv-title-preview">{{ cv.prenom }} {{ cv.nom }}</h1>
          <p v-if="cv.date_naissance" class="info">
            🗓️ Né(e) le {{ formatDate(cv.date_naissance) }}
          </p>
          <p v-if="cv.email" class="info">
            📧 <a :href="`mailto:${cv.email}`">{{ cv.email }}</a>
          </p>
          <p v-if="cv.adresse" class="info">📍 {{ cv.adresse }}</p>
        </div>
      </div>

      <div class="cv-right-column">
        <section v-if="cv.presentation" class="section">
          <h3 class="section-title">Présentation</h3>
          <p>{{ cv.presentation }}</p>
        </section>

        <section v-if="cv.experiences.length" class="section">
          <h3 class="section-title">Expériences professionnelles</h3>
          <ul>
            <li v-for="(exp, i) in cv.experiences" :key="i">{{ exp }}</li>
          </ul>
        </section>

        <section v-if="cv.educations_formations.length" class="section">
          <h3 class="section-title">Éducation & Formation</h3>
          <ul>
            <li v-for="(edu, i) in cv.educations_formations" :key="i">{{ edu }}</li>
          </ul>
        </section>

        <div class="two-columns" v-if="cv.competences.length || cv.langues.length">
          <section v-if="cv.competences.length" class="section">
            <h3 class="section-title">Compétences</h3>
            <ul>
              <li v-for="(comp, i) in cv.competences" :key="i">{{ comp }}</li>
            </ul>
          </section>
          <section v-if="cv.langues.length" class="section">
            <h3 class="section-title">Langues</h3>
            <ul>
              <li v-for="(lang, i) in cv.langues" :key="i">{{ lang }}</li>
            </ul>
          </section>
        </div>

        <section v-if="cv.projets.length" class="section">
          <h3 class="section-title">Projets</h3>
          <ul>
            <li v-for="(proj, i) in cv.projets" :key="i">{{ proj }}</li>
          </ul>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const cv = ref<any>(null);
const isLoading = ref(true);
const error = ref<string | null>(null);
const cvElement = ref<HTMLElement | null>(null);

const DEFAULT_PROFILE = "/assets/default-profile.png";

const profileImageStyle = computed(() => ({
  backgroundImage: `url(${cv.value?.image || DEFAULT_PROFILE})`,
}));

function formatDate(dateStr: string) {
  const date = new Date(dateStr);
  return date.toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

async function loadCv() {
  const id = route.params.id as string;
  if (!id) {
    error.value = "ID de CV non trouvé";
    return;
  }

  isLoading.value = true;
  error.value = null;

  try {
    const { data } = await axios.get(`/api/cv/${id}`);
    cv.value = {
      ...data,
      experiences: data.experiences || [],
      educations_formations: data.educations_formations || [],
      competences: data.competences || [],
      langues: data.langues || [],
      projets: data.projets || [],
    };
  } catch (e: any) {
    error.value = e.response?.data?.message || "Erreur lors du chargement du CV";
  } finally {
    isLoading.value = false;
  }
}

onMounted(loadCv);
</script>

<style scoped>
/* Container général */
.cv-container {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  max-width: 900px;
  margin: 2rem auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
  overflow: hidden;
  background: white;
}

/* Loader */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
}
.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.loading-text {
  color: #555;
  font-size: 1.1rem;
}

/* Erreur */
.error-message {
  padding: 2rem;
  background: #fdecea;
  color: #c0392b;
  border-radius: 8px;
  text-align: center;
  margin: 2rem;
}
.retry-btn {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background: #c0392b;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.retry-btn:hover {
  background: #a93226;
}

/* Aperçu */
.cv-form.preview-form {
  display: flex;
  flex-wrap: wrap;
  min-height: 100vh;
  animation: fadeIn 0.5s;
}
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.cv-left-column,
.cv-right-column {
  padding: 2.5rem 2rem;
}
.cv-left-column {
  flex: 1;
  min-width: 260px;
  background: linear-gradient(to bottom, #2c3e50, #34495e);
  color: white;
}
.cv-right-column {
  flex: 2;
  min-width: 400px;
  background: #fff;
}

/* Photo de profil */
.profile-picture-container {
  text-align: center;
  margin-bottom: 1.5rem;
}
.profile-picture {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-position: center;
  background-size: cover;
  border: 4px solid #3498db;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  margin: 0 auto;
  transition: transform 0.3s;
}
.profile-picture:hover {
  transform: scale(1.05);
}

/* Titres & infos */
.cv-title-preview {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 0.5rem;
}
.info {
  display: flex;
  align-items: center;
  font-size: 0.95rem;
  margin-bottom: 0.8rem;
}
.info a {
  color: #3498db;
  text-decoration: none;
}
.info a:hover {
  text-decoration: underline;
}

/* Sections */
.section {
  margin-bottom: 2rem;
}
.section-title {
  font-size: 1.4rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 1rem;
  border-bottom: 2px solid #3498db;
  padding-bottom: 0.5rem;
}

/* Listes */
ul {
  list-style: none;
  padding: 0;
}
ul li {
  position: relative;
  padding-left: 1.2rem;
  margin-bottom: 0.7rem;
}
ul li::before {
  content: "•";
  position: absolute;
  left: 0;
  color: #3498db;
  font-weight: bold;
}

/* Deux colonnes */
.two-columns {
  display: flex;
  gap: 2rem;
}

/* Boutons d'actions */
.form-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-top: 1px solid #eee;
}
.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}
.download-btn {
  background: #27ae60;
  color: white;
}
.download-btn:hover {
  background: #219653;
  transform: translateY(-2px);
}
.edit-btn {
  background: #3498db;
  color: white;
}
.edit-btn:hover {
  background: #2980b9;
  transform: translateY(-2px);
}
.btn-delete {
  background: #e74c3c;
  color: white;
}
.btn-delete:hover {
  background: #c0392b;
  transform: translateY(-2px);
}

/* Print */
.printing .cv-left-column {
  background: #2c3e50 !important;
}

/* Responsive */
@media (max-width: 768px) {
  .cv-form.preview-form {
    flex-direction: column;
  }
  .two-columns {
    flex-direction: column;
  }
  .form-actions {
    flex-wrap: wrap;
  }
  .action-btn {
    flex: 1;
  }
}
.btnapply {
  padding: 12px 24px;
  background-color: #09a1cb;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.btnapply:hover {
  background-color: #428beb;
}
</style>
