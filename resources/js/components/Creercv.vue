<template>
  <div class="page-wrapper">
    <div class="cv-container">
      <form @submit.prevent="submitCv" class="cv-form" enctype="multipart/form-data">
        <h1 class="cv-title">Créer Votre CV&nbsp;!</h1>

        <!-- Colonne gauche -->
        <div class="cv-left-column">
          <!-- Photo de profil -->
          <div class="profile-section">
            <div class="profile-picture-container">
              <div
                class="profile-picture"
                :style="{
                  backgroundImage: profilePicture ? `url(${profilePicture})` : '',
                }"
                aria-label="Photo de profil"
              />
              <label for="image" class="upload-label">Ajouter photo</label>
              <input
                id="image"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleImage"
              />
              <span v-if="errors.image" class="error">{{ errors.image }}</span>
            </div>

            <!-- Nom / Prénom -->
            <div class="section">
              <div class="input-group">
                <label for="nom">Nom</label>
                <input
                  id="nom"
                  v-model="form.nom"
                  type="text"
                  placeholder="Nom"
                  required
                />
                <span v-if="errors.nom" class="error">{{ errors.nom }}</span>
              </div>
              <div class="input-group">
                <label for="prenom">Prénom</label>
                <input
                  id="prenom"
                  v-model="form.prenom"
                  type="text"
                  placeholder="Prénom"
                  required
                />
                <span v-if="errors.prenom" class="error">{{ errors.prenom }}</span>
              </div>
            </div>
          </div>

          <!-- Infos personnelles -->
          <div class="section">
            <div class="input-group">
              <label for="date_naissance">Date de naissance</label>
              <input
                id="date_naissance"
                v-model="form.date_naissance"
                type="date"
                :max="maxDate"
              />
              <span v-if="errors.date_naissance" class="error">{{
                errors.date_naissance
              }}</span>
            </div>
            <div class="input-group">
              <label for="adresse">Adresse</label>
              <input id="adresse" v-model="form.adresse" type="text" />
            </div>
            <div class="input-group">
              <label for="email">E-mail</label>
              <input id="email" v-model="form.email" type="email" required />
              <span v-if="errors.email" class="error">{{ errors.email }}</span>
            </div>
          </div>

          <!-- Compétences -->
          <div class="section">
            <h3 class="section-title2">Compétences</h3>
            <div
              v-for="(comp, i) in form.competences"
              :key="`comp-${i}`"
              class="dynamic-field"
            >
              <input
                v-model="form.competences[i]"
                type="text"
                placeholder="Ex : Python, Excel…"
              />
              <button
                v-if="form.competences.length > 1 || i > 0"
                type="button"
                @click="removeItem('competences', i)"
                class="delete-btn"
                aria-label="Supprimer cette compétence"
              >
                ✕
              </button>
            </div>
            <button type="button" class="add-btn" @click="addItem('competences')">
              + Ajouter une compétence
            </button>
          </div>

          <!-- Langues -->
          <div class="section">
            <h3 class="section-title2">Langues</h3>
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
                class="delete-btn"
                aria-label="Supprimer cette langue"
              >
                ✕
              </button>
            </div>
            <button type="button" class="add-btn" @click="addItem('langues')">
              + Ajouter une langue
            </button>
          </div>
        </div>

        <!-- Colonne droite -->
        <div class="cv-right-column">
          <!-- Présentation -->
          <div class="section">
            <h3 class="section-title">Présentation</h3>
            <textarea
              id="presentation"
              v-model="form.presentation"
              rows="4"
              placeholder="Quelques lignes pour vous présenter…"
            />
          </div>

          <!-- Expériences -->
          <div class="section">
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
              />
              <button
                v-if="form.experiences.length > 1 || i > 0"
                type="button"
                @click="removeItem('experiences', i)"
                class="delete-btn"
                aria-label="Supprimer cette expérience"
              >
                ✕
              </button>
            </div>
            <button type="button" class="add-btn" @click="addItem('experiences')">
              + Ajouter une expérience
            </button>
          </div>

          <!-- Éducation -->
          <div class="section">
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
              />
              <button
                v-if="form.educations_formations.length > 1 || i > 0"
                type="button"
                @click="removeItem('educations_formations', i)"
                class="delete-btn"
                aria-label="Supprimer cette formation"
              >
                ✕
              </button>
            </div>
            <button
              type="button"
              class="add-btn"
              @click="addItem('educations_formations')"
            >
              + Ajouter une formation
            </button>
          </div>

          <!-- Projets -->
          <div class="section">
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
              />
              <button
                v-if="form.projets.length > 1 || i > 0"
                type="button"
                @click="removeItem('projets', i)"
                class="delete-btn"
                aria-label="Supprimer ce projet"
              >
                ✕
              </button>
            </div>
            <button type="button" class="add-btn" @click="addItem('projets')">
              + Ajouter un projet
            </button>
          </div>

          <div class="form-actions">
            <button type="submit" class="submit-btn" :disabled="isSubmitting">
              {{ isSubmitting ? "Création..." : "Générer mon CV" }}
            </button>
            <button type="button" class="btn btn-cancel" @click="cancelEditing">
              ❌ Annuler
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { reactive, ref, onMounted, computed } from "vue";
import axios from "axios";
import { useToast } from "vue-toastification";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const toast = useToast();

const maxDate = computed(() => {
  const d = new Date();
  d.setFullYear(d.getFullYear() - 18);
  return d.toISOString().split("T")[0];
});

const form = reactive({
  nom: "",
  prenom: "",
  email: "",
  date_naissance: "",
  adresse: "",
  presentation: "",
  competences: [""],
  langues: [""],
  experiences: [""],
  educations_formations: [""],
  projets: [""],
  image: "",
});

const profilePicture = ref("");
const errors = ref({});
const isSubmitting = ref(false);

onMounted(() => {
  toast.clear();
});

function validateForm() {
  const newErrors = {};
  if (!form.nom.trim()) newErrors.nom = "Le nom est requis";
  if (!form.prenom.trim()) newErrors.prenom = "Le prénom est requis";
  if (!form.email.trim()) {
    newErrors.email = "L'email est requis";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    newErrors.email = "Veuillez entrer un email valide";
  }
  const birthDate = new Date(form.date_naissance);
  const today = new Date();
  const age = today.getFullYear() - birthDate.getFullYear();
  if (age < 18) {
    newErrors.date_naissance = "L'âge doit être supérieur ou égal à 18 ans";
  }

  errors.value = newErrors;
  return Object.keys(newErrors).length === 0;
}

function addItem(field) {
  form[field].push("");
}
function removeItem(field, index) {
  const list = form[field];
  list.splice(index, 1);
  if (!list.length) list.push("");
}

async function handleImage(e) {
  const input = e.target;
  const file = input.files?.[0];
  if (!file) return;

  toast.clear();

  if (file.size > 5 * 1024 * 1024) {
    errors.value.image = "L'image ne doit pas dépasser 5MB";
    toast.error(errors.value.image);
    return;
  }

  const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
  if (!allowedTypes.includes(file.type)) {
    errors.value.image = "Format d'image non supporté (JPG ou PNG uniquement)";
    toast.error(errors.value.image);
    return;
  }

  profilePicture.value = URL.createObjectURL(file);

  const fd = new FormData();
  fd.append("image", file);

  try {
    const { data } = await axios.post("/api/ajouterimage", fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    form.image = data.image_url;
    toast.success("Image téléchargée avec succès");
    delete errors.value.image;
  } catch (err) {
    console.error("Erreur upload image :", err);
    errors.value.image = err.response?.data?.message || "Échec d'upload de l'image";
    toast.error(errors.value.image);
  }
}

async function submitCv() {
  if (isSubmitting.value) return;
  toast.clear();

  if (!validateForm()) {
    toast.error("Veuillez corriger les erreurs dans le formulaire");
    return;
  }

  isSubmitting.value = true;
  const from = route.query.from;
  const offreId = route.query.offreId;

  try {
    const response = await axios.post("/api/cv", form, { timeout: 30000 });
    const data = response.data;

    toast.success("CV créé avec succès !");

    if (from === "postuler" && offreId) {
      try {
        await axios.post(`/api/offres/${offreId}/postuler`, {
          cv_id: data.id,
          message: null,
        });
        toast.success("Candidature envoyée automatiquement !");
        router.push("/candidature");
        return;
      } catch (postulerError) {
        console.error("Erreur candidature automatique :", postulerError);
        toast.error("CV créé, mais erreur lors de la candidature.");
      }
    }

    router.push("/mescv");
  } catch (err) {
    console.error("Erreur création CV :", err);
    toast.error("Erreur lors de la création du CV");
  } finally {
    isSubmitting.value = false;
  }
}
function cancelEditing() {
  router.push("/mescv");
}
</script>

<style scoped>
.btn-cancel {
  background: #f5f7fa;
  color: #34495e;
  border: 2px solid #dfe6e9;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.btn-cancel:hover {
  filter: brightness(0.9);
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
.page-wrapper {
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  padding: 1rem 2rem;
  border-radius: 2%;
  padding: 2rem;
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
  position: absolute; /* Pas besoin d'absolute sauf si tu veux superposer */
  width: 100%;
  color: #2c3e50;
  text-align: center;
  padding: 1rem 0; /* Padding positif pour espacer */
  margin: 0 0 2rem 0; /* Marge en bas pour espacer du contenu */
  font-size: 1.8rem;
  font-weight: 500;
  z-index: 10; /* Optionnel si pas d'overlay */
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
  color: #0f3164;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #3498db;
}
.section-title {
  color: inherit;
  font-size: 1.3rem;
  font-weight: 600;
  color: #0f3164;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #3498db;
}
.section-title2 {
  color: inherit;
  font-size: 1.3rem;
  font-weight: 600;
  color: white;
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
  filter: brightness(0.9);
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
  background: linear-gradient(135deg, #20c599, #1fae8d, #178467);
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
  filter: brightness(0.9);
}

.download-btn:hover {
  filter: brightness(0.9);
}

.next-btn:hover {
  filter: brightness(0.9);
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
  filter: brightness(0.9);

  transform: scale(1.2);
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
