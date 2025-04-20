<template>
  <div class="auth-container">
    <div class="auth-content">
      <div class="image-section">
        <img :src="hiringImage" alt="Recrutement" class="hiring-image" />
      </div>

      <div class="form-section">
        <div v-if="!isSubmitted" class="auth-box login-box">
          <h2 class="auth-title">Inscription Recruteur</h2>
          <form @submit.prevent="register" class="auth-form" novalidate>
            <div class="form-grid">
              <div class="input-group" @mouseenter="showHint('nom')" @mouseleave="hideHint('nom')">
                <label for="nom">Nom</label>
                <input
                  type="text"
                  v-model.trim="formData.nom"
                  id="nom"
                  placeholder="Entrez votre nom"
                  :class="{ 'input-error': errors.nom, 'input-valid': validFields.nom }"
                  required
                  autocomplete="family-name"
                  @input="validateField('nom')"
                />
                <span class="error-message" v-if="errors.nom">{{ errors.nom }}</span>
                <span class="hint-message" v-if="hints.nom">Lettres, espaces, apostrophes et tirets uniquement</span>
                <span class="valid-icon" v-if="formData.nom && validFields.nom">✓</span>
              </div>

              <div class="input-group" @mouseenter="showHint('prenom')" @mouseleave="hideHint('prenom')">
                <label for="prenom">Prénom</label>
                <input
                  type="text"
                  v-model.trim="formData.prenom"
                  id="prenom"
                  placeholder="Entrez votre prénom"
                  :class="{ 'input-error': errors.prenom, 'input-valid': validFields.prenom }"
                  required
                  autocomplete="given-name"
                  @input="validateField('prenom')"
                />
                <span class="error-message" v-if="errors.prenom">{{ errors.prenom }}</span>
                <span class="hint-message" v-if="hints.prenom">Lettres, espaces, apostrophes et tirets uniquement</span>
                <span class="valid-icon" v-if="formData.prenom && validFields.prenom">✓</span>
              </div>
            </div>

            <div class="input-group" @mouseenter="showHint('email')" @mouseleave="hideHint('email')">
              <label for="email">Email</label>
              <input
                type="email"
                v-model.trim="formData.email"
                id="email"
                placeholder="Entrez votre email"
                :class="{ 'input-error': errors.email, 'input-valid': validFields.email }"
                required
                autocomplete="email"
                @input="validateField('email')"
              />

              <span class="error-message" v-if="errors.email">{{ errors.email }}</span>
              <span class="hint-message" v-if="hints.email">Format attendu: exemple@domaine.com</span>
              <span class="valid-icon" v-if="formData.email && validFields.email">✓</span>
            </div>

            <div class="input-group" @mouseenter="showHint('password')" @mouseleave="hideHint('password')">
              <label for="password">Mot de passe</label>
              <div class="password-input-container">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model.trim="formData.password"
                  id="password"
                  placeholder="Entrez votre mot de passe"
                  :class="{ 'input-error': errors.password, 'input-valid': validFields.password }"
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
                  :class="{ 'input-error': errors.cin, 'input-valid': validFields.cin }"
                  required
                  maxlength="8"
                  inputmode="numeric"
                  @input="validateField('cin')"
                />
                <span class="error-message" v-if="errors.cin">{{ errors.cin }}</span>
                <span class="hint-message" v-if="hints.cin">Le CIN doit contenir exactement 8 chiffres</span>
                <span class="valid-icon" v-if="formData.cin && validFields.cin">✓</span>
              </div>

              <div class="input-group" @mouseenter="showHint('phone')" @mouseleave="hideHint('phone')">
                <label for="phone">Numéro de téléphone</label>
                <input
                  type="tel"
                  v-model.trim="formData.phone"
                  id="phone"
                  placeholder="Votre numéro de téléphone"
                  :class="{ 'input-error': errors.phone, 'input-valid': validFields.phone }"
                  required
                  maxlength="8"
                  inputmode="tel"
                  autocomplete="tel"
                  @input="validateField('phone')"
                />
                <span class="error-message" v-if="errors.phone">{{ errors.phone }}</span>
                <span class="hint-message" v-if="hints.phone">Le numéro de téléphone doit contenir exactement 8 chiffres</span>
                <span class="valid-icon" v-if="formData.phone && validFields.phone">✓</span>
              </div>
            </div>

            <div class="form-grid">
              <div class="input-group" @mouseenter="showHint('nomsociete')" @mouseleave="hideHint('nomsociete')">
                <label for="nomsociete">Nom de l'entreprise</label>
                <input
                placeholder="Écrire votre nom de l'entreprise "
                  type="text"
                  v-model="formData.nomsociete"
                  id="nomsociete"
                  :class="{ 'input-error': errors.nomsociete, 'input-valid': validFields.nomsociete }"
                  required
                  autocomplete="organization"
                  @input="validateField('nomsociete')"
                />
                <span class="error-message" v-if="errors.nomsociete">{{ errors.nomsociete }}</span>
                <span class="valid-icon" v-if="formData.nomsociete && validFields.nomsociete">✓</span>
              </div>

              <div class="input-group" @mouseenter="showHint('siteweb')" @mouseleave="hideHint('siteweb')">
                <label for="siteweb">Site web</label>
                <input
                  type="url"
                  v-model.trim="formData.siteweb"
                  id="siteweb"
                  placeholder="Écrire votre site web"
                  :class="{ 'input-error': errors.siteweb, 'input-valid': validFields.siteweb }"
                  required
                  autocomplete="url"
                  @input="validateField('siteweb')"
                />
                <span class="error-message" v-if="errors.site">{{ errors.siteweb }}</span>
                <span class="hint-message" v-if="hints.site">Entrez votre site web sous la forme nomdomaine.extension</span>
                <span class="valid-icon" v-if="formData.site && validFields.siteweb">✓</span>
              </div>
            </div>

            <div class="input-group" @mouseenter="showHint('departement')" @mouseleave="hideHint('departement')">
              <label for="departement">Département</label>
              <input
                type="text"
                v-model.trim="formData.departement"
                id="departement"
                placeholder="Écrire votre département"
                :class="{ 'input-error': errors.departement, 'input-valid': validFields.departement }"
                required
                autocomplete="department"
                @input="validateField('departement')"
              />
              <span class="error-message" v-if="errors.departement">{{ errors.departement }}</span>
            

              <span class="valid-icon" v-if="formData.departement && validFields.departement">✓</span>
            </div>

            <div class="input-group" @mouseenter="showHint('localisation')" @mouseleave="hideHint('localisation')">
              <label for="localisation">Localisation</label>
              <input
                type="text"
                v-model.trim="formData.localisation"
                id="localisation"
                placeholder="Écrire votre localisation"
                :class="{ 'input-error': errors.localisation, 'input-valid': validFields.localisation }"
                required
                autocomplete="address-level2"
                @input="validateField('localisation')"
              />
              <span class="error-message" v-if="errors.localisation">{{ errors.localisation }}</span>

              <span class="valid-icon" v-if="formData.localisation && validFields.localisation">✓</span>
            </div>

            <div class="input-group" @mouseenter="showHint('description')" @mouseleave="hideHint('description')">
              <label for="description">Description de l'entreprise</label>
              <textarea
              
                v-model.trim="formData.description"
                id="description"
                placeholder="Écrire votre description"
                :class="{ 'input-error': errors.description, 'input-valid': validFields.description }"
                required
                autocomplete="description"
                @input="validateField('description')"
              ></textarea>
              <span class="error-message" v-if="errors.description">{{ errors.description }}</span>

              <span class="valid-icon" v-if="formData.description && validFields.description">✓</span>
            </div>

            <button 
  type="submit" 
  class="btn-submit" 
  :disabled="!isFormValid || loading"
  aria-disabled="!isFormValid || loading"
>
  <!-- Affichage de l'icône de chargement si `loading` est vrai -->
  <span v-if="loading" class="loading-spinner"></span>
  
  <!-- Affichage du texte en fonction du statut de `loading` -->
  <span>{{ loading ? 'Traitement en cours...' : "S'inscrire" }}</span>
</button>


          </form>
        </div>

        <div v-else class="auth-box success-box">
          <h2 class="auth-title">Inscription réussie !</h2>
          <p>Votre compte a été créé avec succès.</p>
          <button @click="goToCompteRecruteur" class="btn-submit">
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
import hiringImage from '../../assets/recruteurhiring.png';
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
  phone: false,
  localisation: false,
  departement:false,
  description:false,
  nomsociete:false,
  siteweb:false
});

const formData = reactive({
  nom: '',
  prenom: '',
  email: '',
  password: '',
  cin: '',
  phone: '',
  departement: '',
  localisation: '',
  description:'',
  nomsociete:'',
  siteweb:''
});

const errors = reactive({});
const hints = reactive({
  nom: false,
  prenom: false,
  email: false,
  password: false,
  cin: false,
  phone: false,
  localisation: false,
  departement:false,
  description:false,
  nomsociete:false,
  siteweb:false
});


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
  
  const nameRegex = /^[a-zA-ZÀ-ſ0-9\s'-]+$/;
  const emailRegex =/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

  
  const siteRegex = /^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/;  // Regex pour une URL
  const descriptionRegex = /^[a-zA-Z0-9\s,.'-()&;!?%]*$/;


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
      
    case 'phone':
      validFields.phone = /^\d{8}$/.test(formData.phone);
      if (!validFields.phone && formData.phone) {
        errors.phone = 'Téléphone invalide (8 chiffres requis)';
      }
      break;
      

        
    case 'departement':
      validFields.departement = !!formData.departement && nameRegex.test(formData.departement);
      if (!validFields.departement && formData.departement) {
        errors.departement = 'Département invalide';
      }
      break;

  
      
    case 'localisation':
      validFields.localisation = !!formData.localisation && nameRegex.test(formData.localisation);
      if (!validFields.localisation && formData.localisation) {
        errors.localisation = 'localisation invalide';
      }
      break;
      case 'nomsociete':
      validFields.nomsociete = !!formData.nomsociete && nameRegex.test(formData.nomsociete);
      if (!validFields.nomsociete && formData.nomsociete) {
        errors.nomsociete = 'nomsociete invalide';
      }
      break;

case 'siteweb':
  // Vérifie si le champ 'site' est rempli et s'il correspond au regex de site
  validFields.siteweb = !!formData.siteweb && siteRegex.test(formData.siteweb);

  // Si la validation échoue et que le champ 'site' est non vide, on affiche un message d'erreur
  if (!validFields.siteweb && formData.siteweb) {
    errors.siteweb = 'Site invalide';
  }
  break;

      break;
      case 'description':
      validFields.description = !!formData.description && descriptionRegex.test(formData.description);
      if (!validFields.description && formData.description) {
        errors.description = 'description invalide';
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
  console.log("Formulaire soumis", formData);  // Vérifier si la méthode est bien appelée
  if (!validateForm()) return;
  loading.value = true;
  try {
    await api.get('/sanctum/csrf-cookie');
    const payload = {
      nom: formData.nom,
      prenom: formData.prenom,
      email: formData.email,
      password: formData.password,
      role: 'recruteur',
      cin: formData.cin,
      phone: formData.phone,
      localisation: formData.localisation,
      departement: formData.departement,
      description: formData.description,
      nomsociete: formData.nomsociete,
      siteweb: formData.siteweb,

    };
    console.log('Payload : ', payload);  // Vérifier les données envoyées
    const response = await api.post('/api/register', payload);
    console.log('Response : ', response);  // Vérifier la réponse du serveur
    const { user, token } = response.data;
    

    sessionStorage.setItem('userSession', JSON.stringify({
      token,
      ...user
    }));
    router.push('/monprofilerecruteur');



    isSubmitted.value = true;  // Mettre à jour l'état de soumission
  } catch (error) {
    console.error(error);  // Vérifier si une erreur se produit
    if (error.response && error.response.data && error.response.data.errors) {
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


function goToCompteRecruteur() {
  router.push('/monprofilerecruteur'); // ✅ maintenant elle existe
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
  margin-top: 40px;
  padding: 2rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
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