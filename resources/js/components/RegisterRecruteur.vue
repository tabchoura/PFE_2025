<template>
  <div class="register-container">
    <div class="register-card">
      <!-- IMAGE SECTION -->
      <div class="image-section">
        <img :src="hiringImage" alt="Recrutement" class="register-image" />
        <div class="overlay">
          <div class="brand-text">
            <h2>Inscription Recruteur</h2>
            <p>Rejoignez‑nous et gérez vos candidatures en toute simplicité</p>
          </div>
        </div>
      </div>

      <!-- FORM SECTION -->
      <div class="form-section">
        <h1 class="form-title">Créez votre compte</h1>

        <!-- STEPPER NAVIGATION -->
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
              <i class="fas fa-check" v-else></i>
            </div>
            <div class="step-label">{{ step.label }}</div>
          </div>
        </div>

        <!-- PROGRESS BAR -->
        <div class="progress-container">
          <div class="progress-bar" :style="{ width: progressWidth }"></div>
        </div>

        <!-- === FORM === -->
        <form @submit.prevent="nextStep" class="form" novalidate>
          <!-- STEP 1: PERSONAL INFORMATION -->
          <fieldset class="form-fieldset" v-if="currentStep === 0">
            <legend>Informations personnelles</legend>
            <div class="grid two-columns">
              <!-- NOM -->
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
                  >Lettres, espaces, apostrophes et tirets uniquement</span
                >
                <span v-if="formData.nom && validFields.nom" class="valid-icon">✓</span>
              </div>

              <!-- PRENOM -->
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
                  >Lettres, espaces, apostrophes et tirets uniquement</span
                >
                <span v-if="formData.prenom && validFields.prenom" class="valid-icon"
                  >✓</span
                >
              </div>
            </div>

            <!-- EMAIL -->
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
>Format attendu : exemple@domaine.com</span
              >
              <span v-if="formData.email && validFields.email" class="valid-icon">✓</span>
            </div>

            <!-- PASSWORDS -->
           <!-- GROUPE DE MOTS DE PASSE -->
<div class="password-fields-container">
  <!-- PASSWORD -->
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

  <!-- CONFIRM PASSWORD -->
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

          <!-- STEP 2: ADMINISTRATIVE DETAILS -->
          <fieldset class="form-fieldset" v-else-if="currentStep === 1">
            <legend>Détails administratifs</legend>
            <div class="grid two-columns">
              <!-- CIN -->
              <div
                class="form-group"
                @mouseenter="showHint('cin')"
                @mouseleave="hideHint('cin')"
              >
                <label for="cin">Cin</label>
                <input
                  id="cin"
                  type="text"
                  v-model.trim="formData.cin"
                  placeholder="8 chiffres"
                  maxlength="8"
                  inputmode="numeric"
                  :class="inputClass('cin')"
                  @input="validateField('cin')"
                  required
                />
                <span v-if="errors.cin" class="error-message">{{ errors.cin }}</span>
                <span v-else-if="hints.cin" class="hint-message"
                  >Le CIN doit contenir exactement 8 chiffres</span
                >
                <span v-if="formData.cin && validFields.cin" class="valid-icon">✓</span>
              </div>

              <!-- PHONE -->
              <div
                class="form-group"
                @mouseenter="showHint('phone')"
                @mouseleave="hideHint('phone')"
              >
                <label for="phone">Téléphone</label>
                <input
                  id="phone"
                  type="text"
                  v-model.trim="formData.phone"
                  placeholder="8 chiffres"
                  maxlength="8"
                  inputmode="phone"
                  autocomplete="phone"
                  :class="inputClass('phone')"
                  @input="validateField('phone')"
                  required
                />
                <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
                <span v-else-if="hints.phone" class="hint-message"
                  >Numéro sans indicatif, 8 chiffres</span
                >
                <span v-if="formData.phone && validFields.phone" class="valid-icon"
                  >✓</span
                >
              </div>
            </div>

            <!-- DATE & PLACE OF BIRTH -->
            <div class="grid two-columns">
              <!-- DATE -->
              <div
                class="form-group"
                @mouseenter="showHint('nomsociete')"
                @mouseleave="hideHint('nomsociete')"
              >
                <label for="nomsociete">Nom de la societe</label>
                <input
                  id="nomsociete"
                  type="text"
                  v-model="formData.nomsociete"
                  :max="maxDate"
                  :class="inputClass('nomsociete')"
                  @input="validateField('nomsociete')"
                  required
                />
                <span v-if="errors.nomsociete" class="error-message">{{
                  errors.nomsociete
                }}</span>
                <span v-else-if="hints.nomsociete" class="hint-message"
                  >Vous devez avoir au moins 18 ans</span
                >
                <span
                  v-if="formData.nomsociete && validFields.nomsociete"
                  class="valid-icon"
                  >✓</span
                >
              </div>

              <!-- LIEU -->
              <div
                class="form-group"
                @mouseenter="showHint('siteweb')"
                @mouseleave="hideHint('siteweb')"
              >
                <label for="siteweb">Site web </label>
                <input
                  id="siteweb"
                  type="text"
                  v-model.trim="formData.siteweb"
                  placeholder="www.nomdomaine.com"
                  :class="inputClass('siteweb')"
                  @input="validateField('siteweb')"
                  required
                />
                <span v-if="errors.siteweb" class="error-message">{{ errors.siteweb }}</span>
                <span v-else-if="hints.siteweb" class="hint-message"
                  >Le site web est invalide</span
                >
                <span v-if="formData.siteweb && validFields.siteweb" class="valid-icon">✓</span>
              </div>

              <div
                class="form-group"
                @mouseenter="showHint('departement')"
                @mouseleave="hideHint('departement')"
              >
                <label for="departement">Département</label>
                <input
                  id="departement"
                  type="text"
                  v-model.trim="formData.departement"
                  placeholder="Ecrire votre département"
                  :class="inputClass('departement')"
                  @input="validateField('departement')"
                  autocomplete="address-level2"
                  required
                />
                <span v-if="errors.departement" class="error-message">{{
                  errors.departement
                }}</span>
                <span v-else-if="hints.departement" class="hint-message"
                  >Entrez un departement valide
                </span>
                <span
                  v-if="formData.departement && validFields.departement"
                  class="valid-icon"
                  >✓</span
                >
              </div>

              <div
                class="form-group"
                @mouseenter="showHint('localisation')"
                @mouseleave="hideHint('localisation')"
              >
                <label for="localisation">Localisation</label>
                <input
                  id="localisation"
                  type="text"
                  v-model.trim="formData.localisation"
                  placeholder="Ecrire votre localisation"
                  :class="inputClass('localisation')"
                  @input="validateField('localisation')"
                  autocomplete="address-level2"
                  required
                />
                <span v-if="errors.localisation" class="error-message">{{
                  errors.localisation
                }}</span>
                <span v-else-if="hints.localisation" class="hint-message"
                  >Entrez une ville ou commune valide</span
                >
                <span
                  v-if="formData.localisation && validFields.localisation"
                  class="valid-icon"
                  >✓</span
                >
              </div>

              <div
                class="form-group"
                @mouseenter="showHint('description')"
                @mouseleave="hideHint('description')"
              >
                <label for="description">Description</label>
                <textarea
                  id="description"
                  type="text"
                  v-model.trim="formData.description"
                  placeholder="Ecrire votre description"
                  :class="inputClass('description')"
                  @input="validateField('description')"
                  autocomplete="address-level2"
                  required
                />
                <span v-if="errors.description" class="error-message">{{
                  errors.description
                }}</span>
                <span v-else-if="hints.description" class="hint-message"
                  >Entrez une ville ou commune valide</span
                >
                <span
                  v-if="formData.description && validFields.description"
                  class="valid-icon"
                  >✓</span
                >
              </div>
            </div>
          </fieldset>

          <!-- STEP 3: CONFIRMATION -->
          <fieldset class="form-fieldset" v-else>
            <legend>Confirmer votre inscription</legend>
            <div class="summary-container">
              <h3 class="summary-title">Résumé de vos informations</h3>
              <!-- PERSONAL SUMMARY -->
              <div class="summary-section">
                <h4>Informations personnelles</h4>
                <div class="summary-grid">
                  <div class="summary-item">
                    <label>Nom :</label><span>{{ formData.nom }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Prénom :</label><span>{{ formData.prenom }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Email :</label><span>{{ formData.email }}</span>
                  </div>
                </div>
              </div>
              <!-- ADMIN SUMMARY -->
              <div class="summary-section">
                <h4>Détails administratifs</h4>
                <div class="summary-grid">
                  <div class="summary-item">
                    <label>CIN :</label><span>{{ formData.cin }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Téléphone :</label><span> {{ formData.phone }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Nom de la societe:</label
                    ><span>{{ formData.nomsociete }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Site web:</label><span>{{ formData.siteweb }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Département:</label><span>{{ formData.departement }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Localisation:</label><span>{{ formData.localisation }}</span>
                  </div>
                  <div class="summary-item">
                    <label>Description:</label><span>{{ formData.description }}</span>
                  </div>
                </div>
              </div>

              <!-- TERMS -->
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

          <!-- ACTION BUTTONS -->
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

          <!-- NOTIFICATIONS -->
          <transition name="fade">
            <div v-if="success" class="notification success">
              <p>
                Inscription réussie !
                <router-link to="/LoginCandidat">Connectez‑vous</router-link>
              </p>
            </div>
          </transition>
          <transition name="fade">
            <div v-if="serverError" class="notification error">
              <p>{{ serverError }}</p>
            </div>
          </transition>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
import { useRouter } from "vue-router";
import api from "@/axios";
// import hiringImage from '../..assets/rec';

// === STATE ===
const router = useRouter();
const loading = ref(false);
const success = ref(false);
const serverError = ref("");
const showPassword = ref(false);
const showTerms = ref(false);
const showPrivacy = ref(false);
const termsAccepted = ref(false);
const currentStep = ref(0);


const steps = [
  { label: "Informations personnelles" },
  { label: "Détails administratifs" },
  { label: "Confirmation" },
];
// === UI HELPERS ===
const hints = reactive({
  nom: false,
  prenom: false,
  email: false,
  password: false,
  confirmpassword: false,
  cin: false,
  phone: false,
  nomsociete: false,
  siteweb: false,
  departement: false,
  localisation: "",
  description: false,
});

function showHint(field) {
  hints[field] = true;
}
function hideHint(field) {
  hints[field] = false;
}



// === FORM DATA ===
const formData = reactive({
  nom: "",
  prenom: "",
  email: "",
  password: "",
  confirmpassword: "",
  cin: "",
  phone: "",
  nomsociete: "",
  siteweb: "",
  departement: "",
  localisation: "",
  description: "",
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
  nomsociete: false,
  siteweb: false,
  departement: false,
  localisation: false,
  description: false,
});

// === VALIDATION CONSTANTS ===
const nameRegex = /^[a-zA-ZÀ-ſ0-9\s'-]+$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const descriptionRegex = /^[a-zA-Z0-9\s,.'-()&;!?%]*$/;
const siteRegex = /^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/; // Regex pour une URL

const numberRegex = /^\d{8}$/;



function validateField(field) {
  delete errors[field];
  let valid = false;
  const val = formData[field];

  switch (field) {
    case "nom":
    case "prenom":
    case "localisation":
    case "nomsociete":
    case "departement":
      valid = nameRegex.test(val);
      if (!valid) errors[field] = "Format invalide";
      break;
    case "email":
      valid = emailRegex.test(val);
      if (!valid) errors[field] = "Email invalide";
      break;
    case "password":
      valid = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(val);
      if (!valid) errors[field] = "Mot de passe trop faible";
      break;
    case "confirmpassword":
      valid = val === formData.password;
      if (!valid) errors[field] = "Les mots de passe ne correspondent pas";
      break;
    case "cin":
    case "phone":
      valid = numberRegex.test(val);
      if (!valid) errors[field] = "8 chiffres requis";
      break;
    case "siteweb":
      valid = siteRegex.test(val);
      if (!valid) errors[field] = "Site invalide";
      validFields.siteweb = false;      //  ⬅️ retire la coche verte

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
      validFields.nomsociete &&
      validFields.localisation &&
      validFields.departement &&
      validFields.siteweb &&
      validFields.description) ||
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




// === NAVIGATION ===
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
    // force validation display
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
  if (index <= currentStep.value) 
  currentStep.value = index;
}


function togglePassword() {
  showPassword.value = !showPassword.value;
}

// === API ===
async function register() {
  loading.value = true;
  serverError.value = "";
  try {
    await api.get("/sanctum/csrf-cookie");
    await api.post("/api/register", { ...formData, role: "recruteur" });
    success.value = true;
    setTimeout(() => router.push("/LoginRecruteur"), 2000);
  } catch (e) {
    if (e.response?.status === 422) {
      const v = e.response.data?.errors;
      if (v?.email?.length) {
        errors.email = "Cet email est déjà utilisé";
        currentStep.value = 0;
        validFields.email = false;      

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
  font-family: 'Inter', sans-serif;
  background-color: #F7FAFC;
  color: #1F2937;
  line-height: 1.6;
}
h1, h2, h3, h4, h5, h6 {
  font-weight: 600;
}
a {
  color: #2A9D8F;
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
  background: #F7FAFC;
}
.register-card {
  display: flex;
  margin-top: 50px;
  background: #FFFFFF;
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
  background: #3D90D7;
}
.register-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0,0,0,0.2), rgba(0,0,0,0.7));
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
  margin-bottom: .5rem;
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
  color: #3D90D7;
  margin-bottom: 1.5rem;
}

/* Stepper */
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
  margin: 0 auto .5rem;
  width: 48px;
  height: 48px;
  border: 2px solid #6B7280;
  border-radius: 50%;
  background: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .3s ease;
}
.step.active .step-icon {
  border-color: #3D90D7;
  background: #3D90D7;
  color: white;
}
.step.completed .step-icon {
  border-color: #4EDA92;
  background: #4EDA92;
  color: white;
}
.step-label {
  font-size: .875rem;
  color: #6B7280;
  transition: color .3s ease;
}
.step.active .step-label,
.step.completed .step-label {
  color: #3D90D7;
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
  background: #6B7280;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 2rem;
}
.progress-bar {
  height: 100%;
  background: #3D90D7;
  width: 0;
  transition: width .4s ease;
}

/* Fieldsets */
.form-fieldset {
  border: none;
  animation: fadeIn .4s ease;
  margin-bottom: 2rem;
}
.form-fieldset legend {
  font-size: 1.25rem;
  border-bottom: 2px solid #6B7280;
  padding-bottom: .5rem;
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
  margin-bottom: .5rem;
  font-weight: 500;
}
.input {
  padding: 1rem;
  border: 2px solid #6B7280;
  border-radius: 12px;
  font-size: 1rem;
  transition: border-color .3s ease, box-shadow .3s ease;
}
.input:focus {
  border-color: #3D90D7;
  box-shadow: 0 0 0 3px rgba(61,144,215,0.2);
  outline: none;
}
.input-error {
  border-color: #E63946 !important;
}
.input-valid {
  border-color: #4EDA92 !important;
}

/* Messages d’aide et d’erreur */
.hint-message {
  font-size: .875rem;
  color: #6B7280;
  margin-top: .25rem;
}
.error-message {
  font-size: .875rem;
  color: #E63946;
  margin-top: .25rem;
}

/* Icônes de validité */
.valid-icon {
  position: absolute;
  right: 1rem;
  top: 2.5rem;
  color: #4EDA92;
  font-size: 1.125rem;
}

/* Boutons */
.form-actions {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 2rem;
}
.btn-primary, .btn-secondary {
  flex: 1;
  padding: .75rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .5rem;
}
.btn-primary {
  background: #2A9D8F;
  color: white;
}
.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.12);
}
.btn-primary:disabled {
  opacity: .6;
  cursor: not-allowed;
}
.btn-primary:focus {
  outline: 3px solid rgba(42,157,143,0.5);
}
.btn-secondary {
  background: #3D90D7;
  color: white;
}

/* Spinner */
.spinner {
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Notifications */
.notification {
  margin-top: 1.5rem;
  padding: 1rem;
  border-radius: 12px;
  font-size: .875rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.notification.success {
  background: rgba(78,218,146,0.15);
  border-left: 4px solid #4EDA92;
  color: #4EDA92;
}
.notification.error {
  background: rgba(230,57,70,0.15);
  border-left: 4px solid #E63946;
  color: #E63946;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
  .register-card { flex-direction: column; }
  .image-section { flex: none; height: 200px; }
  .two-columns { grid-template-columns: 1fr; }
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
  color: #2A9D8F;
  text-align: center;
  margin-bottom: 1rem;
}

.summary-section {
  background: #ffffff;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.summary-section h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #3D90D7;
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
  color: #6B7280;
}

.summary-item span {
  font-size: 1rem;
  font-weight: 500;
  color: #1F2937;
  margin-top: 0.25rem;
}

</style>