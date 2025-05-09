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
        <h2>Offre d'emploi</h2>
        <h3>{{ offer.titre }}</h3>
        <p><strong>Description :</strong> {{ offer.description }}</p>
        <p><strong>Salaire :</strong> {{ offer.salaire }}</p>
        <p v-if="offer.details"><strong>Détails :</strong> {{ offer.details }}</p>
        <p class="status-line">
          <strong>Statut :</strong>
          <span :class="statusClass">{{ statutLabel }}</span>
        </p>
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

          <!-- Boutons Accepter / Refuser - Ne s'affiche que si le statut est "en attente" et qu'aucun entretien n'est planifié -->
          <div
            class="action-buttons"
            v-if="statusKey === 'pending' && !candidature.date_entretien"
          >
            <button @click="accepter" class="btn-accept">✅ Accepter</button>
            <button @click="refuser" class="btn-refuse">❌ Refuser</button>
          </div>

          <!-- Afficher un message si la candidature a été acceptée et un entretien est prévu -->
          <div v-if="candidature.date_entretien" class="entretien-info">
            <p class="entretien-status">
              ✅ Entretien planifié pour le
              {{ formatDateTime12h(candidature.date_entretien) }}
            </p>
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
<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import html2pdf from "html2pdf.js";

const route = useRoute();
const router = useRouter();
const candidatureId = route.params.candidatureId;

// États
const loading = ref(true);
const error = ref(null);
const candidature = ref({});
const offer = ref({});
const cv = ref({
  experiences: [],
  educations_formations: [],
  competences: [],
  langues: [],
  projets: [],
});
const cvElement = ref(null);

// Normalisation du statut
const statusKey = computed(() => {
  const raw = (candidature.value.statut || "").toString().toLowerCase().trim();

  if (candidature.value.date_entretien) {
    return "accepted";
  }
  if (raw.includes("accept") || raw === "acceptee" || raw === "acceptée") {
    return "accepted";
  }
  if (raw.includes("refus") || raw === "refusee" || raw === "refusée") {
    return "refused";
  }
  if (raw.includes("attente") || raw === "enattente") {
    return "pending";
  }
  return "pending";
});

const statusClass = computed(() => `status-${statusKey.value}`);
const statutLabel = computed(() => {
  switch (statusKey.value) {
    case "accepted":
      return "Acceptée";
    case "refused":
      return "Refusée";
    default:
      return "En attente";
  }
});

// Style de la photo de profil
const profileImageStyle = computed(() => {
  if (cv.value.profilePictureUrl) {
    return {
      backgroundImage: `url(${cv.value.profilePictureUrl})`,
      backgroundSize: "cover",
      backgroundPosition: "center",
    };
  }
  return { backgroundColor: "#ccc" };
});

// Formatage de date
function formatDate(d) {
  return d
    ? new Date(d).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
      })
    : "";
}

function formatDateTime12h(dateString) {
  if (!dateString) return "—";
  const date = new Date(dateString);

  // Options pour Intl.DateTimeFormat
  const options = {
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "numeric",
    minute: "numeric",
    hour12: true, // active le format 12 h avec AM/PM
  };

  // Formatter en fr-FR (mais avec hour12: true pour avoir AM/PM)
  return new Intl.DateTimeFormat("fr-FR", options).format(date);
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

    // Correction éventuelle du statut
    if (
      data.date_entretien &&
      data.statut &&
      data.statut.toString().toLowerCase().includes("attente")
    ) {
      candidature.value.statut = "acceptée";
    }
  } catch (err) {
    console.error(err);
    error.value = "Impossible de charger les détails.";
  } finally {
    loading.value = false;
  }
}

// Accepter / Refuser
async function accepter() {
  try {
    await axios.put(`/api/candidatures/${candidatureId}/accept`);
    alert("Candidature acceptée ✅");
    candidature.value.statut = "acceptée";
    router.push({ name: "Candidaturesrecruteur" });
    setTimeout(() => {
      router.push({
        name: "Entretiens",
        params: { candidatureId },
      });
    }, 1000);
  } catch {
    alert("Échec de l'acceptation.");
  }
}

async function refuser() {
  try {
    await axios.put(`/api/candidatures/${candidatureId}/refuse`);
    alert("Candidature refusée ❌");
    candidature.value.statut = "refusée";
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
  max-width: 1000px;
  margin: 3rem auto;
  padding: 1.5rem;
  font-family: "Poppins", "Segoe UI", system-ui, sans-serif;
  color: #2d3748;
  background-color: #f8fafc;
  border-radius: 16px;
}

/* Cartes */
.section-card {
  background: #fff;
  padding: 2.5rem;
  border-radius: 16px;
  margin-bottom: 2.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  border: 1px solid #f0f4f8;
}

.section-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  border-color: #e2e8f0;
}

/* En-têtes */
h1 {
  font-size: 2.75rem;
  margin-bottom: 2rem;
  color: #1a202c;
  font-weight: 700;
  letter-spacing: -0.5px;
  position: relative;
  padding-bottom: 0.5rem;
}

h1::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, #3182ce, #63b3ed);
  border-radius: 2px;
}

h2 {
  font-size: 1.85rem;
  margin-bottom: 1.25rem;
  color: #2c5282;
  font-weight: 600;
  letter-spacing: -0.3px;
}

h3 {
  font-size: 1.35rem;
  margin-bottom: 1rem;
  color: #2d3748;
  font-weight: 600;
}

h4 {
  font-size: 1.15rem;
  margin-bottom: 0.75rem;
  color: #2d3748;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.95rem;
}

/* Ligne statut */
.status-line {
  margin-top: 1.5rem;
  font-size: 1.1rem;
  padding: 0.75rem 0;
  border-top: 1px dashed #e2e8f0;
}

.status-line strong {
  color: #1a365d;
}

/* Loader */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}

.loader {
  width: 70px;
  height: 70px;
  border: 4px solid #edf2f7;
  border-top: 4px solid #3182ce;
  border-radius: 50%;
  animation: spin 1.2s cubic-bezier(0.5, 0.1, 0.5, 0.9) infinite;
  margin: 0 auto 1.5rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.loading-state p {
  color: #4a5568;
  font-size: 1.1rem;
}

/* Erreur */
.error-state {
  background: #fff5f7;
  border: 1px solid #fed7e2;
  padding: 2rem;
  border-radius: 12px;
  color: #c53030;
  margin-bottom: 2rem;
  text-align: center;
}

.btn-retry {
  margin-top: 1.5rem;
  padding: 0.8rem 1.8rem;
  background: linear-gradient(135deg, #e53e3e, #c53030);
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
}

.btn-retry:hover {
  background: linear-gradient(135deg, #c53030, #9b2c2c);
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(229, 62, 62, 0.4);
}

/* Statut */
.status-pending {
  color: #c05621;
  font-weight: 600;
  background-color: #fffaf0;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  border: 1px solid #feebc8;
}

.status-accepted {
  color: #2f855a;
  font-weight: 600;
  background-color: #f0fff4;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  border: 1px solid #c6f6d5;
}

.status-refused {
  color: #c53030;
  font-weight: 600;
  background-color: #fff5f5;
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.9rem;
  border: 1px solid #fed7d7;
}

/* Boutons Accepter/Refuser */
.action-buttons {
  display: flex;
  gap: 1.5rem;
  margin-top: 2rem;
}

.btn-accept,
.btn-refuse {
  flex: 1;
  padding: 1rem 1.2rem;
  border: none;
  border-radius: 10px;
  color: #fff;
  cursor: pointer;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  font-size: 1.05rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-accept {
  background: linear-gradient(135deg, #48bb78, #38a169);
  box-shadow: 0 4px 12px rgba(56, 161, 105, 0.2);
}

.btn-accept:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(56, 161, 105, 0.3);
  background: linear-gradient(135deg, #38a169, #2f855a);
}

.btn-refuse {
  background: linear-gradient(135deg, #f56565, #e53e3e);
  box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2);
}

.btn-refuse:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(229, 62, 62, 0.3);
  background: linear-gradient(135deg, #e53e3e, #c53030);
}

/* Entretien info */
.entretien-info {
  margin-top: 1.5rem;
  padding: 1.2rem;
  background-color: #ebf8ff;
  border-radius: 10px;
  border-left: 4px solid #3182ce;
}

.entretien-status {
  color: #2c5282;
  font-weight: 600;
  margin: 0;
  font-size: 1.05rem;
}

/* CV */
.cv-content {
  display: flex;
  flex-wrap: wrap;
  gap: 2.5rem;
  margin-top: 1rem;
}

.cv-left-column {
  flex: 1;
  min-width: 250px;
  background: linear-gradient(135deg, #2c5282, #1a365d);
  color: #fff;
  padding: 2.5rem;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(26, 54, 93, 0.2);
}

.profile-picture-container {
  text-align: center;
  margin-bottom: 2rem;
}

.profile-picture {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-size: cover;
  background-position: center;
  margin: 0 auto 1.5rem;
  border: 4px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.profile-picture:hover {
  transform: scale(1.05);
  border-color: rgba(255, 255, 255, 0.5);
}

.cv-left-column h3 {
  margin-bottom: 1.5rem;
  color: white;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
}

.cv-left-column p {
  margin-bottom: 1rem;
  color: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.cv-left-column a {
  color: #90cdf4;
  text-decoration: none;
  transition: color 0.2s ease;
}

.cv-left-column a:hover {
  color: #63b3ed;
  text-decoration: underline;
}

.cv-right-column {
  flex: 2;
  min-width: 300px;
  padding: 2.5rem;
  background: #f8fafc;
  border-radius: 16px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  border: 1px solid #edf2f7;
}

.section {
  margin-bottom: 2rem;
  position: relative;
}

.section h4 {
  color: #2b6cb0;
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 0.5rem;
  margin-bottom: 1rem;
}

.section ul {
  list-style-type: none;
  padding-left: 0;
}

.section ul li {
  position: relative;
  padding-left: 1.5rem;
  margin-bottom: 0.8rem;
  line-height: 1.6;
}

.section ul li::before {
  content: "•";
  position: absolute;
  left: 0;
  color: #3182ce;
  font-weight: bold;
  font-size: 1.2rem;
}

.two-columns {
  display: flex;
  gap: 2rem;
}

.two-columns section {
  flex: 1;
}

/* Message du candidat */
.message-section {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background-color: #f7fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.message-section h3 {
  margin-top: 0;
  font-size: 1.2rem;
  color: #2d3748;
  margin-bottom: 1rem;
}

.message-content {
  line-height: 1.7;
  color: #4a5568;
  white-space: pre-line;
  font-style: italic;
  padding: 1rem;
  background-color: white;
  border-radius: 8px;
  border-left: 3px solid #3182ce;
}

/* Navigation */
.navigation-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 2.5rem;
}

.btn-back,
.btn-download {
  padding: 1rem 2rem;
  border: none;
  border-radius: 10px;
  color: #fff;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.05rem;
}

.btn-back {
  background: linear-gradient(135deg, #4a5568, #2d3748);
  box-shadow: 0 4px 15px rgba(74, 85, 104, 0.2);
}

.btn-back:hover {
  transform: translateY(-3px);
  background: linear-gradient(135deg, #2d3748, #1a202c);
  box-shadow: 0 8px 20px rgba(74, 85, 104, 0.3);
}

.btn-download {
  background: linear-gradient(135deg, #3182ce, #2b6cb0);
  box-shadow: 0 4px 15px rgba(49, 130, 206, 0.2);
}

.btn-download:hover {
  transform: translateY(-3px);
  background: linear-gradient(135deg, #2b6cb0, #2c5282);
  box-shadow: 0 8px 20px rgba(49, 130, 206, 0.3);
}

/* Offre détails */
.offer-details h3 {
  color: #2b6cb0;
  font-size: 1.5rem;
  margin-bottom: 1.25rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e2e8f0;
}

.offer-details p {
  margin-bottom: 1rem;
  line-height: 1.7;
}

.offer-details p strong {
  color: #2d3748;
  font-weight: 600;
}

/* Candidature info */
.candidature-info h2 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.candidature-info h2::before {
  content: "📋";
  font-size: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .candidature-details-container {
    padding: 1rem;
    margin: 2rem auto;
  }

  .section-card {
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .cv-content {
    flex-direction: column;
    gap: 1.5rem;
  }

  .two-columns {
    flex-direction: column;
    gap: 1.5rem;
  }

  h1 {
    font-size: 2rem;
  }

  h2 {
    font-size: 1.5rem;
  }

  .navigation-actions {
    flex-direction: column;
    gap: 1rem;
  }

  .btn-back,
  .btn-download {
    width: 100%;
    justify-content: center;
  }

  .action-buttons {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  h1 {
    font-size: 1.75rem;
  }

  .profile-picture {
    width: 120px;
    height: 120px;
  }
}

/* Animation pour le contenu principal */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.candidature-content {
  animation: fadeIn 0.6s ease-out;
}

/* Effet de focus pour les boutons */
button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}

/* Police pour tout le document */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");
</style>
