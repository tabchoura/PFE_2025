<template>
  <div class="page-wrapper">
    <div class="cv-container">
      <div v-if="isLoading" class="loading-container" role="status" aria-live="polite">
        <div class="loader"></div>
        <p class="loading-text">Chargement du CV…</p>
      </div>

      <div v-else-if="error" class="error-message" role="alert">
        <p>{{ error }}</p>
        <button @click="loadCv" class="btn btn-retry">Réessayer</button>
      </div>

      <form v-else @submit.prevent="updatecv" class="cv-form edit-form" ref="cvElement">
        <div class="cv-left-column">
          <div class="profile-section">
            <div class="profile-picture-container">
              <div class="profile-picture" :style="profileImageStyle"></div>
            </div>
          </div>
          <div class="section">
            <input v-model="form.prenom" placeholder="Prénom" aria-label="Prénom" />
            <input v-model="form.nom" placeholder="Nom" aria-label="Nom" />
            <input
              v-model="form.date_naissance"
              type="date"
              placeholder="Date de naissance"
              aria-label="Date de naissance"
            />
            <input
              v-model="form.email"
              type="email"
              placeholder="Email"
              aria-label="Email"
            />
            <input v-model="form.adresse" placeholder="Adresse" aria-label="Adresse" />
          </div>

          <div class="section">
            <h3 class="section-title1">Compétences</h3>
            <div v-for="(comp, i) in form.competences" :key="`comp-${i}`">
              <input
                v-model="form.competences[i]"
                type="text"
                placeholder="Ex : Python, Excel…"
              />
              <button
                v-if="form.competences.length > 1 || i > 0"
                type="button"
                @click="removeItem('competences', i)"
                class="btn btn-icon delete-btn"
                aria-label="Supprimer cette compétence"
              >
                ✕
              </button>
            </div>
            <button type="button" class="btn btn-add" @click="addItem('competences')">
              + Ajouter une compétence
            </button>
          </div>

          <div class="section">
            <h3 class="section-title1">Langues</h3>
            <div
              v-for="(lang, i) in form.langues"
              :key="`lang-${i}`"
              class="dynamic-field"
            >
              <input v-model="form.langues[i]" type="text" placeholder="Français (C2)" />
              <button
                v-if="form.langues.length > 1 || i > 0"
                type="button"
                @click="removeItem('langues', i)"
                class="btn btn-icon delete-btn"
                aria-label="Supprimer cette langue"
              >
                ✕
              </button>
            </div>
            <button type="button" class="btn btn-add" @click="addItem('langues')">
              + Ajouter une langue
            </button>
          </div>
        </div>

        <div class="cv-right-column">
          <section class="section">
            <h3 class="section-title">Présentation</h3>
            <textarea
              v-model="form.presentation"
              rows="4"
              aria-label="Présentation"
              placeholder="Quelques lignes pour vous présenter…"
            ></textarea>
          </section>

          <section class="section">
            <h3 class="section-title">Expériences professionnelles</h3>
            <div
              v-for="(exp, i) in form.experiences"
              :key="`exp-${i}`"
              class="dynamic-field"
            >
              <textarea
                v-model="form.experiences[i]"
                rows="3"
                placeholder="2023 | Entreprise ● Poste…"
              ></textarea>
              <button
                v-if="form.experiences.length > 1 || i > 0"
                type="button"
                @click="removeItem('experiences', i)"
                class="btn btn-icon delete-btn"
                aria-label="Supprimer cette expérience"
              >
                ✕
              </button>
            </div>
            <button type="button" class="btn btn-add" @click="addItem('experiences')">
              + Ajouter une expérience
            </button>
          </section>

          <section class="section">
            <h3 class="section-title">Éducation & Formation</h3>
            <div
              v-for="(edu, i) in form.educations_formations"
              :key="`edu-${i}`"
              class="dynamic-field"
            >
              <textarea
                v-model="form.educations_formations[i]"
                rows="3"
                placeholder="2020-2024 | Université ● Diplôme…"
              ></textarea>
              <button
                v-if="form.educations_formations.length > 1 || i > 0"
                type="button"
                @click="removeItem('educations_formations', i)"
                class="btn btn-icon delete-btn"
                aria-label="Supprimer cette formation"
              >
                ✕
              </button>
            </div>
            <button
              type="button"
              class="btn btn-add"
              @click="addItem('educations_formations')"
            >
              + Ajouter une formation
            </button>
          </section>

          <section class="section">
            <h3 class="section-title">Projets</h3>
            <div
              v-for="(proj, i) in form.projets"
              :key="`proj-${i}`"
              class="dynamic-field"
            >
              <textarea
                v-model="form.projets[i]"
                rows="2"
                placeholder="Projet personnel / académique…"
              ></textarea>
              <button
                v-if="form.projets.length > 1 || i > 0"
                type="button"
                @click="removeItem('projets', i)"
                class="btn btn-icon delete-btn"
                aria-label="Supprimer ce projet"
              >
                ✕
              </button>
            </div>
            <button type="button" class="btn btn-add" @click="addItem('projets')">
              + Ajouter un projet
            </button>
          </section>

          <div class="form-actions">
            <button type="button" class="btn btn-cancel" @click="cancelEditing">
              ❌ Annuler
            </button>
            <button type="submit" class="btn btn-save" @click="enregistrer">
              💾 Sauvegarder
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import html2pdf from "html2pdf.js";
import { useToast } from "vue-toastification";

const route = useRoute();
const router = useRouter();
const toast = useToast();

const cv = ref(null);
const isLoading = ref(true);
const error = ref(null);
const cvElement = ref(null);
const editMode = ref(false);

const form = reactive({
  prenom: "",
  nom: "",
  date_naissance: "",
  email: "",
  adresse: "",
  presentation: "",
  competences: [""],
  langues: [""],
  experiences: [""],
  educations_formations: [""],
  projets: [""],
  image: "",
});

async function loadCv() {
  isLoading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get(`/api/cv/${route.params.id}`);
    cv.value = {
      prenom: data.prenom || "",
      nom: data.nom || "",
      date_naissance: data.date_naissance || "",
      email: data.email || "",
      adresse: data.adresse || "",
      presentation: data.presentation || "",
      competences: Array.isArray(data.competences) ? data.competences : [],
      langues: Array.isArray(data.langues) ? data.langues : [],
      experiences: Array.isArray(data.experiences) ? data.experiences : [],
      educations_formations: Array.isArray(data.educations_formations)
        ? data.educations_formations
        : [],
      projets: Array.isArray(data.projets) ? data.projets : [],
      image: data.image || "",
    };

    form.prenom = cv.value.prenom;
    form.nom = cv.value.nom;
    form.date_naissance = cv.value.date_naissance.substring(0, 10) || "";
    form.email = cv.value.email;
    form.adresse = cv.value.adresse;
    form.presentation = cv.value.presentation;
    form.competences = cv.value.competences.length ? [...cv.value.competences] : [""];
    form.langues = cv.value.langues.length ? [...cv.value.langues] : [""];
    form.experiences = cv.value.experiences.length ? [...cv.value.experiences] : [""];
    form.educations_formations = cv.value.educations_formations.length
      ? [...cv.value.educations_formations]
      : [""];
    form.projets = cv.value.projets.length ? [...cv.value.projets] : [""];
    form.image = cv.value.image;
  } catch (e) {
    error.value = e.response?.data?.message || "Impossible de charger le CV";
  } finally {
    isLoading.value = false;
  }
}
onMounted(loadCv);

async function cancelEditing() {
  editMode.value = false;

  router.push("/mescv");
}

function formatDate(d) {
  if (!d) return "";
  return new Date(d).toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

function addItem(field) {
  form[field].push("");
}

function removeItem(field, index) {
  form[field].splice(index, 1);
  if (form[field].length === 0) form[field].push("");
}

async function updatecv() {
  isLoading.value = true;
  error.value = null;

  const dataToSend = {
    prenom: form.prenom.trim(),
    nom: form.nom.trim(),
    date_naissance: form.date_naissance,
    email: form.email.trim(),
    adresse: form.adresse.trim(),
    presentation: form.presentation.trim(),
    competences: form.competences.filter((c) => c.trim() !== ""),
    langues: form.langues.filter((l) => l.trim() !== ""),
    experiences: form.experiences.filter((e) => e.trim() !== ""),
    educations_formations: form.educations_formations.filter((f) => f.trim() !== ""),
    projets: form.projets.filter((p) => p.trim() !== ""),
    image: form.image,
  };

  try {
    await axios.put(`/api/cv/${route.params.id}`, dataToSend);
    cv.value = { ...dataToSend };
    editMode.value = false;
    toast.success("Cv mis a jour avec succès !");
    router.push("/mescv");
  } catch (e) {
    toast.error("Erreur lors de la mise à jour");
  } finally {
    isLoading.value = false;
  }
}

const DEFAULT_PROFILE = "/assets/default-profile.png";
const profileImageStyle = computed(() => ({
  backgroundImage: `url(${
    (editMode.value ? form.image : cv.value?.image) || DEFAULT_PROFILE
  })`,
}));
</script>

<style scoped>
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  padding: 1rem 2rem;
  padding: 2rem;
}
/* Base des boutons */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.25s ease;
  border: none;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.btn::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.2);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.btn:hover::after {
  opacity: 1;
}

.btn-icon {
  font-size: 1.2em;
  transition: transform 0.2s ease;
}

.btn:hover .btn-icon {
  transform: scale(1.1);
}

/* Bouton Retour */
.btn-back {
  background-color: #64748b;
  color: white;
}

.btn-back:hover {
  filter: brightness(0.9);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(100, 116, 139, 0.3);
}

/* Bouton Télécharger */
.btn-download {
  background-color: #10b981;
  color: white;
}

.btn-download:hover {
  filter: brightness(0.9);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

/* Bouton Modifier */
.btn-edit {
  background-color: #3b82f6;
  color: white;
}

.btn-edit:hover {
  filter: brightness(0.9);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

/* Bouton Annuler (quand en mode édition) */

.btn-cancel {
  padding: 0.875rem 1.5rem;
  background: #f1f5f9;
  color: #475569;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-cancel:hover {
  background: #e2e8f0;
}
/* Bouton Supprimer */
.btn-delete {
  background-color: #ef4444;
  color: white;
}

.btn-delete:hover {
  filter: brightness(0.9);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

/* Bouton Sauvegarder */

.btn-save {
  padding: 0.875rem 1.5rem;
  background: linear-gradient(135deg, #20c599, #1fae8d, #178467);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3);
}

.btn-save:hover {
  filter: brightness(0.9);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
}

/* Bouton Ajouter */
.btn-add {
  background: none;
  border: 2px dashed #3b82f6;
  color: #3b82f6;
  width: 100%;
  margin-top: 0.5rem;
}

/* Bouton Réessayer */
.btn-retry {
  background-color: #f59e0b;
  color: white;
}

.btn-retry:hover {
  filter: brightness(0.9);
}

/* Groupe de boutons */
.global-actions,
.form-actions {
  grid-column: 1 / -1;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
}
.btn-group-right {
  display: flex;
  gap: 0.75rem;
}

/* Boutons dans le formulaire */
.dynamic-field {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin-bottom: 0.75rem;
}

.dynamic-field input,
.dynamic-field textarea {
  flex: 1;
}

.delete-btn {
  background: none;
  color: #ef4444;
  border: none;
  font-size: 1.2rem;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  background-color: rgba(239, 68, 68, 0.1);
  transform: scale(1.1);
}

/* Styles généraux du CV */
.cv-container {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  max-width: 900px;
  margin: 2rem auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
  overflow: hidden;
  background: white;
}

.cv-form {
  display: flex;
  flex-wrap: wrap;
  min-height: 100vh;
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
  color: #0f3164;

  padding-bottom: 0.5rem;
  border-bottom: 2px solid #3b82f6;
}

.section-title1 {
  font-size: 1.4rem;

  color: #0f3164;
  font-weight: 600;
  color: white;
  margin-bottom: 1rem;
  border-bottom: 2px solid #3b82f6;
  padding-bottom: 0.5rem;
}

input,
textarea {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-family: inherit;
  margin-bottom: 1rem;
  font-size: 0.95rem;
  background-color: rgba(255, 255, 255, 0.9);
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input:focus,
textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
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
  min-height: 80px;
}

.profile-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 2rem;
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
  border: 3px solid #3b82f6;
  background-size: cover;
  background-position: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s ease;
}

.profile-picture:hover {
  transform: scale(1.05);
}

/* Messages d'erreur */
.error-message {
  padding: 2rem;
  background: #fdecea;
  color: #c0392b;
  border-radius: 8px;
  text-align: center;
  margin: 2rem;
}

/* Loader */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  min-height: 300px;
}

.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3b82f6;
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

/* Responsive */
@media (max-width: 768px) {
  .cv-form {
    flex-direction: column;
  }

  .cv-left-column,
  .cv-right-column {
    width: 100%;
    padding-top: 5rem;
  }

  .global-actions,
  .form-actions {
    flex-direction: column;
    gap: 0.75rem;
  }

  .btn-group-right {
    width: 100%;
    justify-content: space-between;
  }

  .btn {
    flex: 1;
    padding: 0.75rem;
  }
}
</style>
