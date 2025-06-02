<template>
  <div class="register-container">
    <div class="register-card">
      <div class="image-section">
        <div class="overlay">
          <div class="brand-text">
            <h2>Inscription Candidat</h2>
            <p>Rejoignez‑nous et gérez vos candidatures en toute simplicité</p>
          </div>
        </div>
      </div>

      <div class="form-section">
        <h1 class="form-title">Créez votre compte</h1>

        <div class="stepper">
          <div
            v-for="(step, index) in steps"
            :key="index"
            :class="[
              'step',
              { active: currentStep === index, completed: index < currentStep },
            ]"
            @click="goToStep(index)"
          >
            <div class="step-icon">
              <span class="step-number" v-if="index >= currentStep">{{ index + 1 }}</span>
              <span v-else>{{ index + 1 }}</span>
            </div>

            <div class="step-label">{{ step.label }}</div>
          </div>
        </div>

        <div class="progress-container">
          <div class="progress-bar" :style="{ width: progressWidth }"></div>
        </div>

        <form @submit.prevent="nextStep" class="form" novalidate>
          <fieldset class="form-fieldset" v-if="currentStep === 0">
            <legend>Informations personnelles</legend>
            <div class="grid two-columns">
              <div
                class="form-group"
                @mouseenter="showHint('nom')"
                @mouseleave="hideHint('nom')"
              >
                <label for="nom">Nom</label>
                <input
                  id="nom"
                  type="text"
                  v-model.trim="formData.nom"
                  placeholder="Entrez votre nom"
                  :class="inputClass('nom')"
                  autocomplete="family-name"
                  @input="validateField('nom')"
                  required
                />
                <span v-if="errors.nom" class="error-message">{{ errors.nom }}</span>
                <span v-else-if="hints.nom" class="hint-message"
                  >Lettres,Chiffres, espaces, apostrophes et tirets uniquement</span
                >
                <span v-if="formData.nom && validFields.nom" class="valid-icon">✓</span>
              </div>

              <div
                class="form-group"
                @mouseenter="showHint('prenom')"
                @mouseleave="hideHint('prenom')"
              >
                <label for="prenom">Prénom</label>
                <input
                  id="prenom"
                  type="text"
                  v-model.trim="formData.prenom"
                  placeholder="Entrez votre prénom"
                  :class="inputClass('prenom')"
                  autocomplete="given-name"
                  @input="validateField('prenom')"
                  required
                />
                <span v-if="errors.prenom" class="error-message">{{
                  errors.prenom
                }}</span>
                <span v-else-if="hints.prenom" class="hint-message"
                  >Lettres,Chiffres, espaces, apostrophes et tirets uniquement</span
                >
                <span v-if="formData.prenom && validFields.prenom" class="valid-icon"
                  >✓</span
                >
              </div>
            </div>

            <div
              class="form-group"
              @mouseenter="showHint('email')"
              @mouseleave="hideHint('email')"
            >
              <label for="email">Email</label>
              <input
                id="email"
                type="email"
                v-model.trim="formData.email"
                placeholder="exemple@domaine.com"
                :class="inputClass('email')"
                autocomplete="email"
                @input="validateField('email')"
                required
              />
              <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
              <span v-else-if="hints.email" class="hint-message"
                >Format attendu : exemple@domaine.com</span
              >
              <span v-if="formData.email && validFields.email" class="valid-icon">✓</span>
            </div>

            <div class="password-fields-container">
              <div
                class="form-group"
                @mouseenter="showHint('password')"
                @mouseleave="hideHint('password')"
              >
                <label for="password">Mot de passe</label>
                <div class="password-wrapper">
                  <input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    v-model.trim="formData.password"
                    placeholder="••••••••"
                    :class="inputClass('password')"
                    @input="validateField('password')"
                    autocomplete="new-password"
                    required
                  />
                  <button
                    type="button"
                    class="toggle-password"
                    @click="togglePassword"
                    :aria-label="
                      showPassword
                        ? 'Masquer le mot de passe'
                        : 'Afficher le mot de passe'
                    "
                  >
                    <span aria-hidden="true">{{ showPassword ? "🔒" : "👁️" }}</span>
                  </button>
                </div>
                <span v-if="errors.password" class="error-message">{{
                  errors.password
                }}</span>
                <span v-else-if="hints.password" class="hint-message"
                  >Au moins 8 caractères, majuscules, minuscules, chiffres et
                  spéciaux</span
                >
                <span v-if="formData.password && validFields.password" class="valid-icon"
                  >✓</span
                >
              </div>

              <div
                class="form-group"
                @mouseenter="showHint('confirmpassword')"
                @mouseleave="hideHint('confirmpassword')"
              >
                <label for="confirmpassword">Confirmez le mot de passe</label>
                <div class="password-wrapper">
                  <input
                    id="confirmpassword"
                    :type="showPassword ? 'text' : 'password'"
                    v-model.trim="formData.confirmpassword"
                    placeholder="••••••••"
                    :class="inputClass('confirmpassword')"
                    @input="validateField('confirmpassword')"
                    autocomplete="new-password"
                    required
                  />
                  <button
                    type="button"
                    class="toggle-password"
                    @click="togglePassword"
                    :aria-label="
                      showPassword
                        ? 'Masquer le mot de passe'
                        : 'Afficher le mot de passe'
                    "
                  >
                    <span aria-hidden="true">{{ showPassword ? "🔒" : "👁️" }}</span>
                  </button>
                </div>
                <span v-if="errors.confirmpassword" class="error-message">{{
                  errors.confirmpassword
                }}</span>
                <span v-else-if="hints.confirmpassword" class="hint-message"
                  >Les deux mots de passe doivent correspondre</span
                >
                <span
                  v-if="formData.confirmpassword && validFields.confirmpassword"
                  class="valid-icon"
                  >✓</span
                >
              </div>
            </div>
          </fieldset>

          <fieldset class="form-fieldset" v-else-if="currentStep === 1">
            <legend>Détails administratifs</legend>
            <div class="grid two-columns">
              <div
                class="form-group"
                @mouseenter="showHint('cin')"
                @mouseleave="hideHint('cin')"
              >
                <label for="cin">CIN</label>
                <input
                  id="cin"
                  type="text"
                  v-model.trim="formData.cin"
                  placeholder="8 chiffres"
                  maxlength="8"
                  inputmode="numeric"
                  :class="inputClass('cin')"
                  @input="validateField('cin')"
                  required
                />
                <span v-if="errors.cin" class="error-message">{{ errors.cin }}</span>
                <span v-else-if="hints.cin" class="hint-message"
                  >Le CIN doit contenir exactement 8 chiffres</span
                >
                <span v-if="formData.cin && validFields.cin" class="valid-icon">✓</span>
              </div>

              <div
                class="form-group"
                @mouseenter="showHint('phone')"
                @mouseleave="hideHint('phone')"
              >
                <label for="phone">Téléphone</label>
                <input
                  id="phone"
                  type="tel"
                  v-model.trim="formData.phone"
                  placeholder="8 chiffres"
                  maxlength="8"
                  inputmode="tel"
                  autocomplete="tel"
                  :class="inputClass('phone')"
                  @input="validateField('phone')"
                  required
                />
                <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
                <span v-else-if="hints.phone" class="hint-message"
                  >Numéro sans indicatif, 8 chiffres</span
                >
                <span v-if="formData.phone && validFields.phone" class="valid-icon"
                  >✓</span
                >
              </div>
            </div>

            <div class="grid two-columns">
              <div
                class="form-group"
                @mouseenter="showHint('date_naissance')"
                @mouseleave="hideHint('date_naissance')"
              >
                <label for="date_naissance">Date de naissance</label>
                <input
                  id="date_naissance"
                  type="date"
                  v-model="formData.date_naissance"
                  :max="maxDate"
                  :class="inputClass('date_naissance')"
                  @input="validateField('date_naissance')"
                  autocomplete="bday"
                  required
                />
                <span v-if="errors.date_naissance" class="error-message">{{
                  errors.date_naissance
                }}</span>
                <span v-else-if="hints.date_naissance" class="hint-message"
                  >Vous devez avoir au moins 18 ans</span
                >
                <span
                  v-if="formData.date_naissance && validFields.date_naissance"
                  class="valid-icon"
                  >✓</span
                >
              </div>

              <div
                class="form-group"
                @mouseenter="showHint('lieudenaissance')"
                @mouseleave="hideHint('lieudenaissance')"
              >
                <label for="lieudenaissance">Lieu de naissance</label>
                <input
                  id="lieudenaissance"
                  type="text"
                  v-model.trim="formData.lieudenaissance"
                  placeholder="Ville / Commune"
                  :class="inputClass('lieudenaissance')"
                  @input="validateField('lieudenaissance')"
                  autocomplete="address-level2"
                  required
                />
                <span v-if="errors.lieudenaissance" class="error-message">{{
                  errors.lieudenaissance
                }}</span>
                <span v-else-if="hints.lieudenaissance" class="hint-message"
                  >Entrez une ville ou commune valide</span
                >
                <span
                  v-if="formData.lieudenaissance && validFields.lieudenaissance"
                  class="valid-icon"
                  >✓</span
                >
              </div>
            </div>
          </fieldset>

          <fieldset class="form-fieldset" v-else>
            <legend>Confirmer votre inscription</legend>
            <div class="summary-container">
              <h3 class="summary-title">Résumé de vos informations</h3>
              <div class="summary-section">
                <h4>Informations personnelles</h4>
                <div class="summary-grid">
                  <div class="summary-item">
                    <label>Nom :</label><span>{{ formData.nom }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Prénom :</label><span>{{ formData.prenom }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Email :</label><span>{{ formData.email }}</span>
                  </div>
                </div>
              </div>
              <div class="summary-section">
                <h4>Détails administratifs</h4>
                <div class="summary-grid">
                  <div class="summary-item">
                    <label>CIN :</label><span>{{ formData.cin }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Téléphone :</label><span>+212 {{ formData.phone }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Date de naissance :</label
                    ><span>{{ formatDate(formData.date_naissance) }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Lieu de naissance :</label
                    ><span>{{ formData.lieudenaissance }}</span>
                  </div>
                </div>
              </div>

              <div class="terms-checkbox">
                <input id="termsAccepted" type="checkbox" v-model="termsAccepted" />
                <label for="termsAccepted">
                  J'accepte les
                  <a href="#" @click.prevent="showTerms = true"
                    >conditions d'utilisation</a
                  >
                  et la
                  <a href="#" @click.prevent="showPrivacy = true"
                    >politique de confidentialité</a
                  >.
                </label>
              </div>
              <small v-if="errors.terms" class="error-bubble">{{ errors.terms }}</small>
            </div>
          </fieldset>

          <div class="form-actions">
            <button
              v-if="currentStep > 0"
              type="button"
              class="btn-secondary"
              @click="previousStep"
            >
              <i class="fas fa-arrow-left"></i> Précédent
            </button>
            <button type="submit" class="btn-primary" :disabled="!canProceed">
              <template v-if="currentStep < steps.length - 1">
                Suivant <i class="fas fa-arrow-right"></i>
              </template>
              <template v-else>
                <span v-if="loading" class="spinner"></span>
                {{ loading ? "Inscription..." : "S'inscrire" }}
              </template>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "@/axios";
import { useToast } from "vue-toastification";

const toast = useToast();
const router = useRouter();
const loading = ref(false);
const serverError = ref("");
const showPassword = ref(false);
const showTerms = ref(false);
const showPrivacy = ref(false);
const termsAccepted = ref(false);
const currentStep = ref(0);

const hints = reactive({
  nom: false,
  prenom: false,
  email: false,
  password: false,
  confirmpassword: false,
  cin: false,
  phone: false,
  date_naissance: false,
  lieudenaissance: false,
});

function showHint(field) {
  hints[field] = true;
}
function hideHint(field) {
  hints[field] = false;
}

const steps = [
  { label: "Informations personnelles" },
  { label: "Détails administratifs" },
  { label: "Confirmation" },
];

const formData = reactive({
  nom: "",
  prenom: "",
  email: "",
  password: "",
  confirmpassword: "",
  cin: "",
  phone: "",
  date_naissance: "",
  lieudenaissance: "",
});

const errors = reactive({});
const validFields = reactive({
  nom: false,
  prenom: false,
  email: false,
  password: false,
  confirmpassword: false,
  cin: false,
  phone: false,
  date_naissance: false,
  lieudenaissance: false,
});

const maxDate = computed(() => {
  const d = new Date();
  d.setFullYear(d.getFullYear() - 18);
  return d.toISOString().split("T")[0];
});

const isStepValid = computed(() => {
  const { value: step } = currentStep;

  return (
    (step === 0 &&
      validFields.nom &&
      validFields.prenom &&
      validFields.email &&
      validFields.password &&
      validFields.confirmpassword) ||
    (step === 1 &&
      validFields.cin &&
      validFields.phone &&
      validFields.date_naissance &&
      validFields.lieudenaissance) ||
    (step === 2 && termsAccepted.value)
  );
});

const canProceed = computed(() =>
  currentStep.value === steps.length - 1
    ? isStepValid.value && !loading.value
    : isStepValid.value
);

const progressWidth = computed(
  () => `${(currentStep.value / (steps.length - 1)) * 100}%`
);
const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9'\-\s]+$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

const numberRegex = /^\d{8}$/;

function validateField(field) {
  delete errors[field];
  let valid = false;
  const val = formData[field];

  switch (field) {
    case "nom":
    case "prenom":
    case "lieudenaissance":
      valid = nameRegex.test(val);
      if (!valid) errors[field] = "Format invalide";
      break;
    case "email":
      valid = emailRegex.test(val);
      if (!valid) errors[field] = "Email invalide";
      break;
    case "password":
      valid = passwordRegex.test(val);
      if (!valid) errors[field] = "Mot de passe trop faible";
      break;
    case "confirmpassword":
      valid = val === formData.password;
      if (!valid) errors[field] = "Les mots de passe ne correspondent pas";
      break;
    case "cin":
    case "phone":
      valid = numberRegex.test(val);
      if (!valid) errors[field] = "8 chiffres requis";
      break;
    case "date_naissance":
      if (val) {
        const age =
          (Date.now() - new Date(val).getTime()) / (1000 * 60 * 60 * 24 * 365.25);
        valid = age >= 18;
        if (!valid) errors[field] = "Âge minimum : 18 ans";
      }
      break;
    default:
      valid = !!val;
  }

  validFields[field] = valid;
}

function inputClass(field) {
  return {
    input: true,
    "input-error": errors[field],
    "input-valid": formData[field] && validFields[field],
  };
}

function nextStep() {
  if (currentStep.value === steps.length - 1) {
    if (!termsAccepted.value) {
      errors.terms = "Vous devez accepter les conditions d'utilisation.";
      return;
    }
    register();
    return;
  }

  if (isStepValid.value) {
    currentStep.value += 1;
    window.scrollTo({ top: 0, behavior: "smooth" });
  } else {
    Object.keys(validFields).forEach((f) => validateField(f));
  }
}

function previousStep() {
  if (currentStep.value > 0) {
    currentStep.value -= 1;
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
}

function goToStep(index) {
  if (index <= currentStep.value) currentStep.value = index;
}

function formatDate(dateString) {
  return dateString
    ? new Date(dateString).toLocaleDateString("fr-FR", {
        year: "numeric",
        month: "long",
        day: "numeric",
      })
    : "";
}

function togglePassword() {
  showPassword.value = !showPassword.value;
}

async function register() {
  loading.value = true;
  serverError.value = "";
  try {
    await axios.get("/sanctum/csrf-cookie");
    await axios.post("/api/register", { ...formData, role: "candidat" });
    toast.success("inscription réussite");
    setTimeout(() => router.push("/LoginCandidat"), 2000);
  } catch (e) {
    if (e.response?.status === 422) {
      const v = e.response.data?.errors;
      if (v?.email?.length) {
        toast.error("Cet email est déjà utilisé");
        currentStep.value = 0;
      } else {
        serverError.value = e.response.data.message || "Erreur de validation";
      }
    } else {
      serverError.value = e.response?.data?.message || "Erreur serveur";
    }
  } finally {
    loading.value = false;
  }
}

watch(termsAccepted, (val) => {
  if (val) delete errors.terms;
});
</script>

<style scoped>
/* Reset de base */
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
html {
  font-size: 16px;
  font-family: "Inter", sans-serif;
  background-color: #f7fafc;
  color: #1f2937;
  line-height: 1.6;
}
h1,
h2,
h3,
h4,
h5,
h6 {
  font-weight: 600;
}
a {
  color: #1f3d7a;
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}

/* Conteneur et carte */
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 2rem;
  background: #f7fafc;
}
.register-card {
  display: flex;
  margin-top: 50px;
  background: #ffffff;
  border-radius: 24px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  max-width: 900px;
  width: 100%;
}

/* Section image */
.image-section {
  position: relative;
  flex: 0 0 40%;
  min-height: 500px;
  background: #3d90d7;
}
.register-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.overlay {
  position: absolute;
  inset: 0;

  background: linear-gradient(135deg, #1f3d7a 0%, #3c64c8 100%);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
  text-align: center;
  padding: 2rem;
}
.brand-text h2 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}
.brand-text p {
  font-size: 1rem;
}

/* Section formulaire */
.form-section {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}
.form-title {
  text-align: center;
  font-size: 1.75rem;
  color: #1e3a8a;
  margin-bottom: 1.5rem;
}
.stepper {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.step {
  flex: 1;
  text-align: center;
  cursor: pointer;
}
.step-icon {
  margin: 0 auto 0.5rem;
  width: 48px;
  height: 48px;
  border: 2px solid #6b7280;
  border-radius: 50%;
  background: #ffffff; /* Default background */
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

/* Active Step */
.step.active .step-icon {
  border-color: #3d90d7; /* Blue border for active step */
  background: linear-gradient(
    135deg,
    #1f3d7a 0%,
    #3c64c8 100%
  ); /* Blue gradient for active step */
  color: white; /* Keep the number visible */
}

/* Completed Step */
.step.completed .step-icon {
  border-color: #11a85a; /* Vert */
  background: #87c1a3; /* Vert plein */
  color: white;
}

/* Labels under the steps */
.step-label {
  font-size: 0.875rem;
  color: #6b7280;
  transition: color 0.3s ease;
}

/* Active/Completed Labels */
.step.active .step-label,
.step.completed .step-label {
  color: #3d90d7; /* Blue for active or completed */
  font-weight: 600;
}

.password-fields-container {
  display: flex;
  gap: 1rem;
  width: 100%;
}

.password-fields-container .form-group {
  flex: 1;
}

/* Barre de progression */
.progress-container {
  width: 100%;
  height: 6px;
  background: #6b7280;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 2rem;
}
.progress-bar {
  height: 100%;
  background: linear-gradient(135deg, #1f3d7a 0%, #3c64c8 100%);
  width: 0;
  transition: width 0.4s ease;
}

/* Fieldsets */
.form-fieldset {
  border: none;
  animation: fadeIn 0.4s ease;
  margin-bottom: 2rem;
}
.form-fieldset legend {
  font-size: 1.25rem;
  color: #3d90d7;

  border-bottom: 2px solid #6b7280;
  padding-bottom: 0.5rem;
  margin-bottom: 1rem;
}

/* Grilles */
.grid {
  display: grid;
  gap: 1.5rem;
}
.two-columns {
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}

/* Inputs & labels */
.form-group {
  display: flex;
  flex-direction: column;
  position: relative;
}
.form-group label {
  margin-bottom: 0.5rem;
  font-weight: 500;
}
.input {
  padding: 1rem;
  border: 2px solid #6b7280;
  border-radius: 12px;
  font-size: 1rem;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.input:focus {
  border-color: #3d90d7;
  box-shadow: 0 0 0 3px rgba(61, 144, 215, 0.2);
  outline: none;
}
.input-error {
  border-color: #e63946 !important;
}
.input-valid {
  border-color: #4eda92 !important;
}

/* Messages d’aide et d’erreur */
.hint-message {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.25rem;
}
.error-message {
  font-size: 0.875rem;
  color: #e63946;
  margin-top: 0.25rem;
}

/* Icônes de validité */
.valid-icon {
  position: absolute;
  right: 1rem;
  top: 2.5rem;
  color: #4eda92;
  font-size: 1.125rem;
}

/* Boutons */
.form-actions {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 2rem;
}
.btn-primary,
.btn-secondary {
  flex: 1;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.btn-primary {
  background: linear-gradient(135deg, #1f3d7a 0%, #3c64c8 100%);
  color: white;
}
.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}
.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.btn-primary:focus {
  outline: 3px solid rgba(42, 157, 143, 0.5);
}
.btn-secondary {
  background: #3d90d7;
  color: white;
}

/* Spinner */
.spinner {
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Notifications */
.notification {
  margin-top: 1.5rem;
  padding: 1rem;
  border-radius: 12px;
  font-size: 0.875rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.notification.success {
  background: rgba(78, 218, 146, 0.15);
  border-left: 4px solid #4eda92;
  color: #4eda92;
}
.notification.error {
  background: rgba(230, 57, 70, 0.15);
  border-left: 4px solid #e63946;
  color: #e63946;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .register-card {
    flex-direction: column;
  }
  .image-section {
    flex: none;
    height: 200px;
  }
  .two-columns {
    grid-template-columns: 1fr;
  }
}
/* Confirmation summary */
.summary-container {
  display: grid;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.summary-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f3d7a;
  text-align: center;
  margin-bottom: 1rem;
}

.summary-section {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.summary-section h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #3d90d7;
  margin-bottom: 1rem;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
}

.summary-item {
  display: flex;
  flex-direction: column;
}

.summary-item label {
  font-size: 0.875rem;
  color: #6b7280;
}

.summary-item span {
  font-size: 1rem;
  font-weight: 500;
  color: #1f2937;
  margin-top: 0.25rem;
}
</style>
