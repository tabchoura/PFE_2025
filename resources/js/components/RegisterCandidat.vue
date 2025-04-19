<template>
  <div class="auth-container">
    <div class="auth-content">
      <div class="image-section">
        <img :src="hiringImage" alt="Recrutement" class="hiring-image" />
      </div>
      <div class="form-section">
        <div v-if="!isSubmitted" class="auth-box login-box">
          <h2 class="auth-title">Inscription Candidat</h2>
          <form @submit.prevent="register" class="auth-form" novalidate>
            <div class="form-grid">
              <div class="input-group" @mouseenter="showHint('nom')" @mouseleave="hideHint('nom')">
                <label for="nom">Nom</label>
                <input
                  type="text"
                  v-model.trim="formData.nom"
                  id="nom"
                  placeholder="Entrez votre nom"
                  :class="{ 'input-error': errors.nom, 'input-valid': formData.nom && isValidField('nom') }"
                  required
                  autocomplete="family-name"
                  @input="validateField('nom')"
                />
                <span class="error-message" v-if="errors.nom">{{ errors.nom }}</span>
                <span class="hint-message" v-if="hints.nom">Lettres, espaces, apostrophes et tirets uniquement</span>
                <span class="valid-icon" v-if="formData.nom && isValidField('nom')">✓</span>
              </div>
              <div class="input-group" @mouseenter="showHint('prenom')" @mouseleave="hideHint('prenom')">
                <label for="prenom">Prénom</label>
                <input
                  type="text"
                  v-model.trim="formData.prenom"
                  id="prenom"
                  placeholder="Entrez votre prénom"
                  :class="{ 'input-error': errors.prenom, 'input-valid': formData.prenom && isValidField('prenom') }"
                  required
                  autocomplete="given-name"
                  @input="validateField('prenom')"
                />
                <span class="error-message" v-if="errors.prenom">{{ errors.prenom }}</span>
                <span class="hint-message" v-if="hints.prenom">Lettres, espaces, apostrophes et tirets uniquement</span>
                <span class="valid-icon" v-if="formData.prenom && isValidField('prenom')">✓</span>
              </div>
            </div>

            <div class="input-group" @mouseenter="showHint('email')" @mouseleave="hideHint('email')">
              <label for="email">Email</label>
              <input
                type="email"
                v-model.trim="formData.email"
                id="email"
                placeholder="Entrez votre email"
                :class="{ 'input-error': errors.email, 'input-valid': formData.email && isValidField('email') }"
                required
                autocomplete="email"
                @input="validateField('email')"
              />
              <span class="error-message" v-if="errors.email">{{ errors.email }}</span>
              <span class="hint-message" v-if="hints.email">Format attendu: exemple@domaine.com</span>
              <span class="valid-icon" v-if="formData.email && isValidField('email')">✓</span>
            </div>

            <div class="input-group" @mouseenter="showHint('password')" @mouseleave="hideHint('password')">
              <label for="password">Mot de passe</label>
              <div class="password-input-container">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model.trim="formData.password"
                  id="password"
                  placeholder="Entrez votre mot de passe"
                  :class="{ 'input-error': errors.password, 'input-valid': formData.password && isValidField('password') }"
                  required
                  autocomplete="new-password"
                  @input="validateField('password')"
                />
                <button
                  type="button"
                  class="toggle-password"
                  @click="togglePassword"
                  :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                >
                  <span aria-hidden="true">{{ showPassword ? '🔒' : '👁️' }}</span>
                </button>
                <span class="valid-icon" v-if="formData.password && isValidField('password')">✓</span>
              </div>
              <span class="error-message" v-if="errors.password">{{ errors.password }}</span>
              <div class="password-strength" v-if="formData.password">
                <div class="strength-bar" :class="passwordStrengthClass"></div>
                <span>{{ passwordStrengthText }}</span>
              </div>
              <span class="hint-message" v-if="hints.password">
                Le mot de passe doit contenir au moins 8 caractères, incluant majuscules, minuscules,
                chiffres et caractères spéciaux.
              </span>
            </div>

            <div class="form-grid">
              <div class="input-group" @mouseenter="showHint('cin')" @mouseleave="hideHint('cin')">
                <label for="cin">CIN</label>
                <input
                  type="text"
                  v-model.trim="formData.cin"
                  id="cin"
                  placeholder="Entrez votre CIN"
                  :class="{ 'input-error': errors.cin, 'input-valid': formData.cin && isValidField('cin') }"
                  required
                  maxlength="8"
                  inputmode="numeric"
                  @input="validateField('cin')"
                />
                <span class="error-message" v-if="errors.cin">{{ errors.cin }}</span>
                <span class="hint-message" v-if="hints.cin">Le CIN doit contenir exactement 8 chiffres</span>
                <span class="valid-icon" v-if="formData.cin && isValidField('cin')">✓</span>
              </div>
              
              <div class="input-group" @mouseenter="showHint('numtel')" @mouseleave="hideHint('numtel')">
                <label for="numtel">Numéro de téléphone</label>
                <input
                  type="tel"
                  v-model.trim="formData.numtel"
                  id="numtel"
                  placeholder="Votre numéro de téléphone"
                  :class="{ 'input-error': errors.numtel, 'input-valid': formData.numtel && isValidField('numtel') }"
                  required
                  maxlength="8"
                  inputmode="tel"
                  autocomplete="tel"
                  @input="validateField('numtel')"
                />
                <span class="error-message" v-if="errors.numtel">{{ errors.numtel }}</span>
                <span class="hint-message" v-if="hints.numtel">Le numéro de téléphone doit contenir exactement 8 chiffres</span>
                <span class="valid-icon" v-if="formData.numtel && isValidField('numtel')">✓</span>
              </div>
            </div>

            <div class="form-grid">
              <div class="input-group" @mouseenter="showHint('date')" @mouseleave="hideHint('date')">
                <label for="date">Date de naissance</label>
                <input
                  type="date"
                  v-model="formData.date"
                  id="date"
                  :max="maxDate"
                  :class="{ 'input-error': errors.date, 'input-valid': formData.date && isValidField('date') }"
                  required
                  autocomplete="bday"
                  @change="validateField('date')"
                />
                <span class="error-message" v-if="errors.date">{{ errors.date }}</span>
                <span class="hint-message" v-if="hints.date">Vous devez avoir au moins 18 ans</span>
                <span class="valid-icon" v-if="formData.date && isValidField('date')">✓</span>
              </div>
              
              <div class="input-group" @mouseenter="showHint('lieu')" @mouseleave="hideHint('lieu')">
                <label for="lieu">Lieu de naissance</label>
                <input
                  type="text"
                  v-model.trim="formData.lieu"
                  id="lieu"
                  placeholder="Ville / Commune"
                  :class="{ 'input-error': errors.lieu, 'input-valid': formData.lieu && isValidField('lieu') }"
                  required
                  autocomplete="address-level2"
                  @input="validateField('lieu')"
                />
                <span class="error-message" v-if="errors.lieu">{{ errors.lieu }}</span>
                <span class="hint-message" v-if="hints.lieu">Entrez une ville ou commune valide</span>
                <span class="valid-icon" v-if="formData.lieu && isValidField('lieu')">✓</span>
              </div>
            </div>

            <button type="submit" class="btn-submit" :disabled="loading || !isFormValid">
              <span v-if="loading" class="loading-spinner"></span>
              <span>{{ loading ? 'Traitement en cours...' : 'S\'inscrire' }}</span>
            </button>
          </form>
        </div>

        <div v-else class="auth-box success-box">
          <div class="success-icon">✓</div>
          <h2 class="auth-title">Inscription réussie !</h2>
          <p>Votre compte a été créé avec succès.</p>
          <button @click="goToCompteCandidat" class="btn-submit">
            Accéder à votre compte
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/axios';
import hiringImage from '../../assets/Rejoignez-nous.png';

const router = useRouter();
const isSubmitted = ref(false);
const loading = ref(false);
const showPassword = ref(false);
const validFields = reactive({
  nom: false,
  prenom: false,
  email: false,
  password: false,
  cin: false,
  numtel: false,
  date: false,
  lieu: false
});

const formData = reactive({
  nom: '',
  prenom: '',
  email: '',
  password: '',
  cin: '',
  numtel: '',
  date: '',
  lieu: ''
});

const errors = reactive({});
const hints = reactive({
  nom: false,
  prenom: false,
  email: false,
  password: false,
  cin: false,
  numtel: false,
  date: false,
  lieu: false
});

// Validation en temps réel de tous les champs lors de la saisie
watch(formData, (newVal) => {
  Object.keys(newVal).forEach(field => {
    if (newVal[field]) {
      validateField(field);
    }
  });
}, { deep: true });

const isFormValid = computed(() => {
  return Object.values(validFields).every(valid => valid) && 
         Object.keys(formData).every(field => !!formData[field]);
});

function isValidField(field) {
  return validFields[field];
}

const passwordStrengthClass = computed(() => {
  if (!formData.password) return '';
  if (formData.password.length < 8) return 'faible';
  
  const hasUpperCase = /[A-Z]/.test(formData.password);
  const hasLowerCase = /[a-z]/.test(formData.password);
  const hasNumbers = /[0-9]/.test(formData.password);
  const hasSpecialChars = /[^A-Za-z0-9\s]/.test(formData.password);
  
  if (hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChars) {
    return 'fort';
  } else if ((hasUpperCase || hasLowerCase) && hasNumbers) {
    return 'moyen';
  }
  return 'faible';
});

const passwordStrengthText = computed(() => {
  if (!formData.password) return '';
  switch (passwordStrengthClass.value) {
    case 'faible': return 'Faible';
    case 'moyen': return 'Moyen';
    case 'fort': return 'Fort';
    default: return '';
  }
});

const maxDate = computed(() => {
  const today = new Date();
  today.setFullYear(today.getFullYear() - 18);
  return today.toISOString().split('T')[0];
});

function showHint(field) {
  hints[field] = true;
}

function hideHint(field) {
  hints[field] = false;
}

function togglePassword() {
  showPassword.value = !showPassword.value;
}

function validateField(field) {
  // Effacer l'erreur spécifique du champ
  if (errors[field]) {
    delete errors[field];
  }
  
  const nameRegex = /^[a-zA-ZÀ-ſ\s'-]+$/;
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
  switch (field) {
    case 'nom':
      validFields.nom = !!formData.nom && nameRegex.test(formData.nom);
      if (!validFields.nom && formData.nom) {
        errors.nom = 'Nom invalide';
      }
      break;
      
    case 'prenom':
      validFields.prenom = !!formData.prenom && nameRegex.test(formData.prenom);
      if (!validFields.prenom && formData.prenom) {
        errors.prenom = 'Prénom invalide';
      }
      break;
      
    case 'email':
      validFields.email = !!formData.email && emailRegex.test(formData.email);
      if (!validFields.email && formData.email) {
        errors.email = 'Email invalide';
      }
      break;
      
    case 'password':
      const hasUpperCase = /[A-Z]/.test(formData.password);
      const hasLowerCase = /[a-z]/.test(formData.password);
      const hasNumbers = /[0-9]/.test(formData.password);
      const hasSpecialChars = /[^A-Za-z0-9\s]/.test(formData.password);
      
      validFields.password = formData.password.length >= 8 && 
                            hasUpperCase && hasLowerCase && 
                            hasNumbers && hasSpecialChars;
                            
      if (!validFields.password && formData.password) {
        if (formData.password.length < 8) {
          errors.password = 'Mot de passe trop court';
        } else {
          errors.password = 'Le mot de passe doit contenir majuscules, minuscules, chiffres et caractères spéciaux';
        }
      }
      break;
      
    case 'cin':
      validFields.cin = /^\d{8}$/.test(formData.cin);
      if (!validFields.cin && formData.cin) {
        errors.cin = 'CIN invalide (8 chiffres requis)';
      }
      break;
      
    case 'numtel':
      validFields.numtel = /^\d{8}$/.test(formData.numtel);
      if (!validFields.numtel && formData.numtel) {
        errors.numtel = 'Téléphone invalide (8 chiffres requis)';
      }
      break;
      
    case 'date':
      if (formData.date) {
        const birthDate = new Date(formData.date);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        validFields.date = age > 18 || (age === 18 && (monthDiff > 0 || 
           (monthDiff === 0 && today.getDate() >= birthDate.getDate())));
           
        if (!validFields.date) {
          errors.date = 'Vous devez avoir au moins 18 ans';
        }
      } else {
        validFields.date = false;
      }
      break;
      
    case 'lieu':
      validFields.lieu = !!formData.lieu && nameRegex.test(formData.lieu);
      if (!validFields.lieu && formData.lieu) {
        errors.lieu = 'Lieu invalide';
      }
      break;
  }
}

function validateForm() {
  Object.keys(formData).forEach(field => {
    validateField(field);
  });
  
  return Object.values(validFields).every(valid => valid);
}

async function register() {
  if (!validateForm()) return;
  loading.value = true;
  try {
    await api.get('/sanctum/csrf-cookie');
    const payload = {
      nom: formData.nom,
      prenom: formData.prenom,
      email: formData.email,
      password: formData.password,
      role: 'candidat',
      cin: formData.cin,
      numtel: formData.numtel,
      date_naissance: formData.date,
      lieu: formData.lieu
    };
    console.log('payload : ', payload)
    const response = await api.post('/api/register', payload);
    console.log('response : ', response)
    const { user, token } = response.data;

    sessionStorage.setItem('userSession', JSON.stringify({
      token,
      ...user

    }));
    console.log('userSession :', sessionStorage.getItem('userSession'));

    isSubmitted.value = true;
  } catch (error) {
    console.error(error);
    if (error.response && error.response.data && error.response.data.errors) {
      // Gestion des erreurs de validation du serveur
      const serverErrors = error.response.data.errors;
      Object.keys(serverErrors).forEach(key => {
        errors[key] = serverErrors[key][0];
        validFields[key] = false;
      });
    } else {
      alert("Erreur serveur lors de l'inscription");
    }
  } finally {
    loading.value = false;
  }
}

function goToCompteCandidat() {
  router.push('/CompteCandidat');
}
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #e0ecff, #f4f7fc);
  padding: 1rem;
}

.auth-content {
  display: flex;
  flex-direction: row;
  width: 90%;
  max-width: 1200px;
  background-color: #fff;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.image-section {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  align-items: flex-start;
  background: linear-gradient(135deg, #f0f8ff, #dceeff);
  border-right: 1px solid #e0e0e0;
}

.hiring-image {
  width: 100%;
  margin-top: 10rem;
  max-width: 500px;
  height: auto;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
  transition: transform 0.4s ease;
  max-width: 550px; 
  max-height: 480px; 
}

.hiring-image:hover {
  transform: scale(1.05);
}

.form-section {
  flex: 1;
  padding: 2rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  margin-top: 40px;

}

.auth-title {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 25px;
  color: #222;
  text-align: center;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}

.input-group {
  display: flex;
  flex-direction: column;
  position: relative;
  margin-bottom: 0.5rem;
}

.input-group label {
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
}

.input-group input {
  padding: 12px;
  padding-right: 40px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.3s ease;
}

.input-group input:focus {
  border-color: #0056d2;
  box-shadow: 0 0 0 4px rgba(0, 86, 210, 0.15);
  outline: none;
}

.input-error {
  border-color: #dc3545 !important;
}

.input-error:focus {
  box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.2) !important;
}

.input-valid {
  border-color: #28a745 !important;
}

.input-valid:focus {
  box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.2) !important;
}

.error-message {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
  animation: fadeIn 0.3s ease;
}

.hint-message {
  color: #0056d2;
  font-size: 12px;
  margin-top: 5px;
  font-style: italic;
  animation: fadeIn 0.3s ease;
}

.valid-icon {
  position: absolute;
  right: 10px;
  top: 40px;
  color: #28a745;
  font-weight: bold;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.password-input-container {
  position: relative;
  width: 100%;
  display: flex;
  align-items: center;
}

.password-input-container input {
  flex: 1;
  padding-right: 40px;
}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 30px;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: #666;
  padding: 0;
  height: 100%;
  display: flex;
  align-items: center;
}

.btn-submit {
  margin-top: 20px;
  padding: 14px;
  width: 100%;
  font-size: 16px;
  font-weight: bold;
  color: white;
  background-color: #007BFF;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-submit:hover {
  background-color: #0056d2;
}

.btn-submit:disabled {
  background-color: #bbb;
  cursor: not-allowed;
}

.loading-spinner {
  margin-right: 8px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0); }
  100% { transform: rotate(360deg); }
}

/* Force du mot de passe */
.password-strength {
  margin-top: 10px;
  font-size: 13px;
  color: #444;
}

.strength-bar {
  height: 8px;
  border-radius: 5px;
  background: #e0e0e0;
  margin-top: 5px;
  transition: background-color 0.3s ease, width 0.3s ease;
  position: relative;
}

.strength-bar.faible {
  background-color: #dc3545;
  width: 33%;
}

.strength-bar.moyen {
  background-color: #ffc107;
  width: 66%;
}

.strength-bar.fort {
  background-color: #28a745;
  width: 100%;
}

/* Succès */
.success-box {
  text-align: center;
}

.success-icon {
  font-size: 48px;
  color: #28a745;
  background: rgba(40, 167, 69, 0.1);
  border-radius: 50%;
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Responsivité */
@media (max-width: 992px) {
  .auth-content {
    flex-direction: column;
    width: 100%;
  }

  .image-section {
    display: none;
  }
}

@media (max-width: 576px) {
  .form-section {
    padding: 1.5rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .btn-submit {
    font-size: 15px;
  }
}
</style>