<template>
  <div class="candidature-details-container">
    <!-- Chargement -->
    <div v-if="loading" class="loading-state">
      <div class="loader"></div>
      <p>Chargement des détails…</p>
    </div>

    <!-- Erreur -->
    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="loadData" class="btn-retry">🔄 Réessayer</button>
    </div>

    <!-- Contenu principal -->
    <div v-else class="candidature-content">
      <h1>Détails de la candidature</h1>

      <!-- Offre -->
      <div class="section-card offer-details">
        <h2>Offre d’emploi</h2>
        <h3>{{ offer.titre }}</h3>
        <p><strong>Description :</strong> {{ offer.description }}</p>
        <p><strong>Salaire :</strong> {{ offer.salaire }}</p>
        <p v-if="offer.details"><strong>Détails :</strong> {{ offer.details }}</p>
        <p class="status-line">
          <strong>Statut :</strong>
          <span :class="statusClass">{{ statutLabel }}</span>
        </p>
      </div>

      <!-- Infos candidature -->
      <div class="section-card candidature-info">
        <h2>Infos candidature</h2>
        <p><strong>Date :</strong> {{ formatDate(candidature.created_at) }}</p>
        <div class="message-section">
          <h3>Message du candidat</h3>
          <div class="message-content">
            {{ candidature.message || "Aucun message" }}
          </div>
        </div>

        <!-- Boutons Accepter / Refuser -->
        <div class="action-buttons" v-if="statusKey === 'pending'">
          <button @click="accepter" class="btn-accept">✅ Accepter</button>
          <button @click="refuser" class="btn-refuse">❌ Refuser</button>
        </div>
      </div>

      <!-- Aperçu CV -->
      <div ref="cvElement" class="section-card cv-preview">
        <h2>CV du candidat</h2>
        <div class="cv-content">
          <div class="cv-left-column">
            <div class="profile-picture-container">
              <div class="profile-picture" :style="profileImageStyle"></div>
            </div>
            <h3>{{ cv.prenom }} {{ cv.nom }}</h3>
            <p v-if="cv.date_naissance">
              🗓️ Né(e) le {{ formatDate(cv.date_naissance) }}
            </p>
            <p v-if="cv.email">
              📧 <a :href="`mailto:${cv.email}`">{{ cv.email }}</a>
            </p>
            <p v-if="cv.adresse">📍 {{ cv.adresse }}</p>
          </div>
          <div class="cv-right-column">
            <section v-if="cv.presentation" class="section">
              <h4>Présentation</h4>
              <p>{{ cv.presentation }}</p>
            </section>
            <section v-if="cv.experiences.length" class="section">
              <h4>Expériences</h4>
              <ul>
                <li v-for="(e, i) in cv.experiences" :key="i">{{ e }}</li>
              </ul>
            </section>
            <section v-if="cv.educations_formations.length" class="section">
              <h4>Éducation & Formation</h4>
              <ul>
                <li v-for="(f, i) in cv.educations_formations" :key="i">{{ f }}</li>
              </ul>
            </section>
            <div class="two-columns" v-if="cv.competences.length || cv.langues.length">
              <section v-if="cv.competences.length" class="section">
                <h4>Compétences</h4>
                <ul>
                  <li v-for="(c, i) in cv.competences" :key="i">{{ c }}</li>
                </ul>
              </section>
              <section v-if="cv.langues.length" class="section">
                <h4>Langues</h4>
                <ul>
                  <li v-for="(l, i) in cv.langues" :key="i">{{ l }}</li>
                </ul>
              </section>
            </div>
            <section v-if="cv.projets.length" class="section">
              <h4>Projets</h4>
              <ul>
                <li v-for="(p, i) in cv.projets" :key="i">{{ p }}</li>
              </ul>
            </section>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <div class="navigation-actions">
        <button @click="goBack" class="btn-back">← Retour</button>
        <button @click="downloadPdf" class="btn-download">⬇️ Télécharger CV</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import html2pdf from "html2pdf.js";

const route = useRoute();
const router = useRouter();
const candidatureId = route.params.candidatureId as string;

// États
const loading = ref(true);
const error = ref<string | null>(null);
const candidature = ref<any>({});
const offer = ref<any>({});
const cv = ref<any>({
  experiences: [],
  educations_formations: [],
  competences: [],
  langues: [],
  projets: [],
});
const cvElement = ref<HTMLElement | null>(null);

// Normalisation du statut
const statusKey = computed<"pending" | "accepter" | "refuser">(() => {
  const raw = (candidature.value.statut || "").toString().toLowerCase();
  if (raw.includes("accept")) return "Accepter";
  if (raw.includes("refus")) return "Refuser";
  return "pending";
});

// CSS + label
const statusClass = computed(() => `status-${statusKey.value}`);
const statutLabel = computed(() => {
  switch (statusKey.value) {
    case "accepter":
      return "Acceptée";
    case "refused":
      return "Refusée";
    default:
      return "En attente";
  }
});

// Image de profil
const profileImageStyle = computed(() =>
  cv.value.profilePictureUrl
    ? {
        backgroundImage: `url(${cv.value.profilePictureUrl})`,
        backgroundSize: "cover",
        backgroundPosition: "center",
      }
    : { backgroundColor: "#ccc" }
);

// Formatage date
function formatDate(d?: string) {
  return d
    ? new Date(d).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
      })
    : "";
}

// Chargement initial
async function loadData() {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get(`/api/candidatures/${candidatureId}`);
    candidature.value = data;
    offer.value = data.offre;
    cv.value = data.cv;
  } catch (err) {
    console.error(err);
    error.value = "Impossible de charger les détails.";
  } finally {
    loading.value = false;
  }
}

// Acceptation / refus + notification + redirection
async function accepter() {
  try {
    await axios.put(`/api/candidatures/${candidatureId}/accept`);
    alert("Candidature acceptée ✅");
    router.push({ name: "Candidaturesrecruteur" });
  } catch {
    alert("Échec de l'acceptation.");
  }
}
async function refuser() {
  try {
    await axios.put(`/api/candidatures/${candidatureId}/refuse`);
    alert("Candidature refusée ❌");
    router.push({ name: "Candidaturesrecruteur" });
  } catch {
    alert("Échec du refus.");
  }
}

// PDF & retour
function downloadPdf() {
  if (!cvElement.value) return;
  html2pdf()
    .set({ margin: 10, filename: `CV_${cv.value.prenom}_${cv.value.nom}.pdf` })
    .from(cvElement.value)
    .save();
}
function goBack() {
  router.back();
}

onMounted(loadData);
</script>

<style scoped>
/* Container global */
.candidature-details-container {
  max-width: 900px;
  margin: 3rem auto;
  padding: 1rem;
  font-family: "Segoe UI", sans-serif;
  color: #333;
}

/* Cartes */
.section-card {
  background: #fff;
  padding: 2rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.section-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

/* En-têtes */
h1 {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
  color: #1a202c;
}
h2 {
  font-size: 1.75rem;
  margin-bottom: 1rem;
  color: #2d3748;
}
h3 {
  font-size: 1.25rem;
  margin-bottom: 0.75rem;
  color: #4a5568;
}
h4 {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
  color: #4a5568;
}

/* Ligne statut */
.status-line {
  margin-top: 1rem;
  font-size: 1rem;
}
.status-line strong {
  color: #2d3748;
}

/* Loader */
.loading-state {
  text-align: center;
  padding: 3rem;
}
.loader {
  width: 60px;
  height: 60px;
  border: 6px solid #e2e8f0;
  border-top: 6px solid #3182ce;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Erreur */
.error-state {
  background: #fff5f5;
  border: 1px solid #feb2b2;
  padding: 1.5rem;
  border-radius: 8px;
  color: #c53030;
  margin-bottom: 2rem;
}
.btn-retry {
  margin-top: 1rem;
  padding: 0.6rem 1.2rem;
  background: #e53e3e;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s ease;
}
.btn-retry:hover {
  background: #c53030;
}

/* Statut */
.status-pending {
  color: #dd6b20;
  font-weight: 600;
}
.status-accepted {
  color: #38a169;
  font-weight: 600;
}
.status-refused {
  color: #e53e3e;
  font-weight: 600;
}

/* Boutons Accepter/Refuser */
.action-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}
.btn-accept,
.btn-refuse {
  flex: 1;
  padding: 0.8rem;
  border: none;
  border-radius: 8px;
  color: #fff;
  cursor: pointer;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.1s ease;
}
.btn-accept {
  background: #48bb78;
}
.btn-accept:hover {
  transform: translateY(-2px);
  background: #38a169;
}
.btn-refuse {
  background: #f56565;
}
.btn-refuse:hover {
  transform: translateY(-2px);
  background: #e53e3e;
}

/* CV */
.cv-content {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
}
.cv-left-column {
  flex: 1;
  min-width: 250px;
  background: #2a4365;
  color: #fff;
  padding: 2rem;
  border-radius: 12px;
}
.profile-picture-container {
  text-align: center;
  margin-bottom: 1.5rem;
}
.profile-picture {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  background-size: cover;
  background-position: center;
  margin: 0 auto 1rem;
}
.cv-left-column h3 {
  margin-bottom: 1rem;
}
.cv-right-column {
  flex: 2;
  min-width: 300px;
  padding: 2rem;
  background: #f7fafc;
  border-radius: 12px;
}
.section {
  margin-bottom: 1.5rem;
}
.two-columns {
  display: flex;
  gap: 1.5rem;
}
.two-columns section {
  flex: 1;
}

/* Navigation */
.navigation-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 2rem;
}
.btn-back,
.btn-download {
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 8px;
  background: #3182ce;
  color: #fff;
  cursor: pointer;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: background 0.2s ease, transform 0.1s ease;
}
.btn-back:hover,
.btn-download:hover {
  transform: translateY(-2px);
  background: #2b6cb0;
}

/* Responsive */
@media (max-width: 768px) {
  .cv-content {
    flex-direction: column;
  }
  .navigation-actions {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>
