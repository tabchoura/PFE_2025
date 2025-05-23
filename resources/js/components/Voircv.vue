<template>
  <div class="page-wrapper">
    <div class="cv-container">
      <!-- Loader -->
      <div v-if="isLoading" class="loading-container" role="status" aria-live="polite">
        <div class="loader"></div>
        <p class="loading-text">Chargement du CV…</p>
      </div>

      <!-- Message d'erreur -->
      <div v-else-if="error" class="error-message" role="alert">
        <p>{{ error }}</p>
        <button @click="loadCv" class="retry-btn">Réessayer</button>
      </div>

      <!-- Aperçu du CV -->
      <div v-else ref="cvElement" class="cv-form preview-form">
        <div class="cv-left-column">
          <div class="profile-section">
            <div class="profile-picture-container">
              <div class="profile-picture" :style="profileImageStyle"></div>
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
          <div class="section" v-if="cv.competences.length">
            <h3 class="section-title1">Compétences</h3>
            <ul>
              <li v-for="(c, i) in cv.competences" :key="i">{{ c }}</li>
            </ul>
          </div>
          <div class="section" v-if="cv.langues.length">
            <h3 class="section-title1">Langues</h3>
            <ul>
              <li v-for="(l, i) in cv.langues" :key="i">{{ l }}</li>
            </ul>
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

          <section v-if="cv.projets.length" class="section">
            <h3 class="section-title">Projets</h3>
            <ul>
              <li v-for="(p, i) in cv.projets" :key="i">{{ p }}</li>
            </ul>
          </section>
        </div>
      </div>
    </div>

    <!-- Boutons d'actions -->
    <div
      v-if="cv && !isLoading"
      class="form-actions"
      role="group"
      aria-label="Actions sur le CV"
    >
      <button
        class="btn-retour"
        @click="rollback"
        aria-label="Retourner à la liste des CVs"
      >
        ← Retourner
      </button>
      <div class="btn-group-right">
        <button
          @click="downloadPdf"
          class="action-btn download-btn"
          aria-label="Télécharger le CV au format PDF"
        >
          📥 Télécharger PDF
        </button>
        <button @click="modifyCv" class="action-btn edit-btn" aria-label="Modifier le CV">
          ✏️ Modifier
        </button>
        <button
          @click="deleteCv"
          class="action-btn btn-delete"
          aria-label="Supprimer le CV"
        >
          🗑️ Supprimer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import html2pdf from "html2pdf.js";

const route = useRoute();
const router = useRouter();
const cv = ref(null);
const isLoading = ref(true);
const error = ref(null);
const cvElement = ref(null);

async function loadCv() {
  const idcv = route.params.id;
  isLoading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get(`/api/cv/${idcv}`);
    cv.value = {
      ...data,
      experiences: data.experiences || [],
      educations_formations: data.educations_formations || [],
      competences: data.competences || [],
      langues: data.langues || [],
      projets: data.projets || [],
    };
  } catch (e) {
    error.value = e.response?.data?.message || "Impossible de charger le CV";
  } finally {
    isLoading.value = false;
  }
}
onMounted(loadCv);

const DEFAULT_PROFILE = "/assets/default-profile.png";
const profileImageStyle = computed(() => ({
  backgroundImage: `url(${cv.value?.image || DEFAULT_PROFILE})`,
}));

function formatDate(d) {
  return new Date(d).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}
function rollback() {
  router.push("/mescv");
}

function downloadPdf() {
  if (!cvElement.value) return;
  cvElement.value.classList.add("printing");
  html2pdf()
    .set({
      margin: 10,
      filename: `CV_${cv.value.prenom}_${cv.value.nom}.pdf`,
      image: { type: "jpeg", quality: 0.98 },
      html2canvas: { scale: 2, useCORS: true },
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
    })
    .from(cvElement.value)
    .save()
    .then(() => cvElement.value?.classList.remove("printing"));
}

function modifyCv() {
  const idcv = route.params.id;
  router.push(`/modifiercv/${idcv}`);
}

async function deleteCv() {
  const idcv = route.params.id;
  if (
    !confirm(
      `Confirmez-vous la suppression du CV de ${cv.value.prenom} ${cv.value.nom} ?`
    )
  )
    return;
  try {
    await axios.delete(`/api/cv/${idcv}`);
    router.push("/mescv");
  } catch (e) {
    alert(e.response?.data?.message || "Erreur lors de la suppression");
  }
}
</script>

<style scoped>
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  min-height: 100vh;
  padding: 2rem;
  border-radius: 3%;
}

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
  background: #0f3164;
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

.section-title1 {
  font-size: 1.4rem;
  font-weight: 600;
  color: white;
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

/* Form actions */
.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-top: 1px solid #eee;
  flex-wrap: wrap;
  gap: 1rem;
}

/* Bouton Retour */
.form-actions .btn-retour {
  background-color: #10b981;
  color: white;
  padding: 0.8rem 1.8rem;
  font-weight: 700;
  border-radius: 25px;
  border: none;
  cursor: pointer;
  box-shadow: 0 6px 18px rgba(16, 185, 129, 0.5);
  transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
  user-select: none;
  white-space: nowrap;
  flex-shrink: 0;
  min-width: 140px;
  order: 1;
}
.btn-retour:hover,
.btn-retour:focus-visible {
  background-color: #047857;
  transform: translateY(-3px);
  box-shadow: 0 10px 28px rgba(4, 120, 87, 0.7);
  outline: none;
}

/* Groupe boutons à droite */
.form-actions .btn-group-right {
  display: flex;
  gap: 1rem;
  order: 2;
  flex-wrap: wrap;
  flex-grow: 1;
  justify-content: flex-end;
  min-width: 0;
}

/* Styles communs aux autres boutons */
.action-btn {
  padding: 0.8rem 2.2rem;
  font-weight: 700;
  border-radius: 25px;
  border: none;
  cursor: pointer;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
  user-select: none;
  white-space: nowrap;
  flex-shrink: 0;
  min-width: 140px;
  transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
}

.download-btn {
  background-color: #2563eb;
  color: white;
  box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
}
.download-btn:hover,
.download-btn:focus-visible {
  background-color: #1e40af;
  transform: translateY(-3px);
  box-shadow: 0 12px 32px rgba(30, 64, 175, 0.7);
}

.edit-btn {
  background-color: #ca8a04;
  color: white;
  box-shadow: 0 6px 20px rgba(107, 114, 128, 0.5);
}
.edit-btn:hover,
.edit-btn:focus-visible {
  background-color: #4b5563;
  transform: translateY(-3px);
  box-shadow: 0 12px 32px rgba(75, 85, 99, 0.7);
}

.btn-delete {
  background-color: #ef4444;
  color: white;
  box-shadow: 0 6px 20px rgba(239, 68, 68, 0.5);
}
.btn-delete:hover,
.btn-delete:focus-visible {
  background-color: #b91c1c;
  box-shadow: 0 12px 32px rgba(185, 28, 28, 0.7);
}

/* Impression PDF */
.printing .cv-left-column {
  background: #0f3164 !important;
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
    flex-direction: column;
    align-items: stretch;
  }
  .btn-retour,
  .action-btn {
    width: 100%;
    min-width: auto;
    flex-shrink: 1;
  }
  .form-actions .btn-group-right {
    justify-content: center;
    order: 3;
    margin-top: 1rem;
  }
}
</style>
