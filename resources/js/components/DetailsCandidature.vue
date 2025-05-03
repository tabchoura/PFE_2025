<template>
  <!-- Détails de l'offre -->
  <div v-if="offer" class="offer-details">
    <h1>{{ offer.titre }}</h1>
    <p><strong>Description:</strong> {{ offer.description }}</p>
    <p><strong>Salaire:</strong> {{ offer.salaire }}</p>
    <p><strong>Détails:</strong> {{ offer.details }}</p>
  </div>

  <!-- Aperçu du CV -->
  <div class="cv-container">
    <!-- Loader -->
    <div v-if="isLoading" class="loading-container">
      <div class="loader"></div>
      <p class="loading-text">Chargement du CV et de l'offre…</p>
    </div>

    <!-- Message d'erreur -->
    <div v-else-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="loadData" class="retry-btn">Réessayer</button>
    </div>

    <!-- Contenu du CV et de l'offre -->
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
              <li v-for="(c, i) in cv.competences" :key="i">{{ c }}</li>
            </ul>
          </section>
          <section v-if="cv.langues.length" class="section">
            <h3 class="section-title">Langues</h3>
            <ul>
              <li v-for="(l, i) in cv.langues" :key="i">{{ l }}</li>
            </ul>
          </section>
        </div>
        <section v-if="cv.projets.length" class="section">
          <h3 class="section-title">Projets</h3>
          <ul>
            <li v-for="(p, i) in cv.projets" :key="i">{{ p }}</li>
          </ul>
        </section>
      </div>
    </div>

    <!-- Boutons d'actions -->
    <div v-if="cv && !isLoading" class="form-actions">
      <button @click="downloadPdf" class="action-btn download-btn">
        📥 Télécharger PDF
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import html2pdf from "html2pdf.js";

const route = useRoute();
const cv = ref<any>(null);
const offer = ref<any>(null);
const offerId = route.params.offerId as string;
const cvId = route.params.cvId as string;
const isLoading = ref(true);
const error = ref<string | null>(null);
const cvElement = ref<HTMLElement | null>(null);

const profileImageStyle = computed(() => {
  if (cv.value?.profilePictureUrl) {
    return {
      backgroundImage: `url(${cv.value.profilePictureUrl})`,
      backgroundSize: "cover",
      backgroundPosition: "center",
    };
  }
  return { backgroundColor: "#ccc" };
});

const formatDate = (dateStr: string) => {
  if (!dateStr) return "Date inconnue";
  const date = new Date(dateStr);
  return date.toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

async function loadData() {
  isLoading.value = true;
  error.value = null;

  try {
    const offerResponse = await axios.get(`/api/offres/${offerId}`);
    offer.value = offerResponse.data;
  } catch (error) {
    console.error("Erreur lors de la récupération de l'offre:", error);
    error.value = "Offre non trouvée.";
  }

  try {
    const cvResponse = await axios.get(`/api/cv/${cvId}`);
    cv.value = {
      ...cvResponse.data,
      experiences: cvResponse.data.experiences || [],
      educations_formations: cvResponse.data.educations_formations || [],
      competences: cvResponse.data.competences || [],
      langues: cvResponse.data.langues || [],
      projets: cvResponse.data.projets || [],
    };
  } catch (e) {
    console.error("Erreur lors de la récupération du CV:", e);
    error.value = "CV non trouvé.";
  } finally {
    isLoading.value = false;
  }
}

onMounted(loadData);

function downloadPdf() {
  if (!cvElement.value) return;
  cvElement.value.classList.add("printing");
  html2pdf()
    .set({
      margin: 10,
      filename: `CV_${cv.value.prenom}_${cv.value.nom}.pdf`,
      html2canvas: { scale: 2, useCORS: true },
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
    })
    .from(cvElement.value)
    .save()
    .finally(() => cvElement.value?.classList.remove("printing"));
}
</script>

<style scoped>
/* Styles CSS */
.offer-details {
  max-width: 900px;
  margin: 30px auto;
  margin-top: 100px;
  padding: 30px;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease-in-out;
  min-height: 300px;
  max-height: 400px;
  overflow-y: auto;
}

.offer-details:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.button-container {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
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

h1 {
  font-size: 2rem;
  text-align: center;
  color: #2980b9;
  margin-bottom: 20px;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1px;
}

p {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #555;
  margin-bottom: 15px;
}

strong {
  color: #2c3e50;
  font-weight: 600;
}

.offer-details p:first-child {
  margin-top: 0;
}
.offer-details p:last-child {
  margin-bottom: 0;
}

@media (max-width: 768px) {
  .offer-details {
    padding: 20px;
  }
  h1 {
    font-size: 1.8rem;
  }
  p {
    font-size: 1rem;
  }
}

.cv-container {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  max-width: 900px;
  margin: 2rem auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
  overflow: hidden;
}

.cv-form {
  display: flex;
  flex-wrap: wrap;
  min-height: 100vh;
}

.cv-title {
  position: absolute;
  top: 0;
  right: 150px;
  width: 100%;
  color: #2c3e50;
  text-align: center;
  padding: 1rem;
  margin: 0;
  font-size: 1.8rem;
  font-weight: 500;
  z-index: 10;
}

.cv-left-column {
  flex: 1;
  min-width: 300px;
  background-color: #0f3164;
  color: #fff;
  padding: 6rem 2rem 2rem;
}

.cv-right-column {
  flex: 2;
  min-width: 400px;
  background-color: #fff;
  padding: 6rem 2rem 2rem;
}

.section {
  margin-bottom: 2rem;
  padding: 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.cv-left-column .section {
  background-color: rgba(255, 255, 255, 0.05);
}

.cv-right-column .section {
  background-color: #f8f9fa;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.section:hover {
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.section-title {
  color: inherit;
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #3498db;
}

.input-group {
  margin-bottom: 1.2rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  font-size: 0.9rem;
}

input,
textarea {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: inherit;
  font-size: 0.95rem;
  background-color: rgba(255, 255, 255, 0.9);
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input:focus,
textarea:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.cv-left-column input,
.cv-left-column textarea {
  background-color: rgba(255, 255, 255, 0.1);
  border-color: transparent;
  color: #fff;
}

.cv-left-column input::placeholder,
.cv-left-column textarea::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

textarea {
  resize: vertical;
}

.profile-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 2rem;
}

.profile-picture-container {
  position: relative;
  margin-bottom: 1.5rem;
  text-align: center;
}

.profile-picture {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background-color: #ddd;
  margin: 0 auto 1rem;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid #3498db;
  background-size: cover;
  background-position: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s ease;
}

.profile-picture:hover {
  transform: scale(1.05);
}

.upload-label {
  cursor: pointer;
  padding: 0.5rem 1rem;
  background-color: #3498db;
  color: #fff;
  border-radius: 4px;
  font-size: 0.8rem;
  transition: background-color 0.3s ease;
}

.upload-label:hover {
  background-color: #2980b9;
}

.hidden {
  display: none;
}

.form-actions {
  margin-top: 2rem;
  text-align: center;
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.submit-btn,
.download-btn,
.next-btn {
  padding: 1rem 2rem;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.submit-btn {
  background-color: #3498db;
}

.download-btn {
  background-color: #27ae60;
}

.next-btn {
  background-color: #9b59b6;
}

.submit-btn:hover,
.download-btn:hover,
.next-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.submit-btn:hover {
  background-color: #2980b9;
}

.download-btn:hover {
  background-color: #219653;
}

.next-btn:hover {
  background-color: #8e44ad;
}

.submit-btn:disabled,
.next-btn:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.dynamic-field {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin-bottom: 0.5rem;
}

.dynamic-field input,
.dynamic-field textarea {
  flex: 1;
}

.add-btn {
  background: none;
  border: 1px dashed #3498db;
  color: #3498db;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: block;
  width: 100%;
  margin-top: 0.5rem;
}

.add-btn:hover {
  background: rgba(52, 152, 219, 0.1);
  transform: translateY(-1px);
}

.delete-btn {
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  color: #e74c3c;
  transition: transform 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
}

.delete-btn:hover {
  transform: scale(1.2);
  background-color: rgba(231, 76, 60, 0.1);
}

.error {
  color: #e74c3c;
  font-size: 0.8rem;
  margin-top: 0.3rem;
}

@media (max-width: 768px) {
  .cv-form {
    flex-direction: column;
  }

  .cv-left-column,
  .cv-right-column {
    width: 100%;
    padding-top: 5rem;
  }

  .form-actions {
    flex-direction: column;
  }

  .submit-btn,
  .download-btn,
  .next-btn {
    width: 100%;
  }
}
</style>
