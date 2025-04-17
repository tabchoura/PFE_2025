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
              <div class="input-group">
                <label for="nom">Nom</label>
                <input
                  type="text"
                  v-model.trim="formData.nom"
                  id="nom"
                  placeholder="Entrez votre nom"
                  :class="{ 'input-error': errors.nom }"
                  required
                />
                <span class="error-message" v-if="errors.nom">{{ errors.nom }}</span>
              </div>
              <div class="input-group">
                <label for="prenom">Prénom</label>
                <input
                  type="text"
                  v-model.trim="formData.prenom"
                  id="prenom"
                  placeholder="Entrez votre prénom"
                  :class="{ 'input-error': errors.prenom }"
                  required
                />
                <span class="error-message" v-if="errors.prenom">{{ errors.prenom }}</span>
              </div>
            </div>

            <div class="input-group">
              <label for="email">Email</label>
              <input
                type="email"
                v-model.trim="formData.email"
                id="email"
                placeholder="Entrez votre email"
                :class="{ 'input-error': errors.email }"
                required
              />
              <span class="error-message" v-if="errors.email">{{ errors.email }}</span>
            </div>

            <div class="input-group">
              <label for="password">Mot de passe</label>
              <div class="password-input-container">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model.trim="formData.password"
                  id="password"
                  placeholder="Entrez votre mot de passe"
                  :class="{ 'input-error': errors.password }"
                  required
                  autocomplete="new-password"
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
              <p class="password-requirements" v-if="formData.password">
                Le mot de passe doit contenir au moins 8 caractères, incluant majuscules, minuscules,
                chiffres et caractères spéciaux.
              </p>
            </div>

            <div class="form-grid">
              <div class="input-group">
                <label for="cin">CIN</label>
                <input
                  type="text"
                  v-model.trim="formData.cin"
                  id="cin"
                  placeholder="Entrez votre CIN"
                  :class="{ 'input-error': errors.cin }"
                  required
                />
                <span class="error-message" v-if="errors.cin">{{ errors.cin }}</span>
              </div>
              <div class="input-group">
                <label for="numtel">Numéro de téléphone</label>
                <input
                  type="tel"
                  v-model.trim="formData.numtel"
                  id="numtel"
                  placeholder="Votre numéro de téléphone"
                  :class="{ 'input-error': errors.numtel }"
                  required
                />
                <span class="error-message" v-if="errors.numtel">{{ errors.numtel }}</span>
              </div>
            </div>

            <div class="form-grid">
              <div class="input-group">
                <label for="nomentreprise">Nom de l'entreprise</label>
                <input
                  type="text"
                  v-model="formData.nomentreprise"
                  id="nomentreprise"
                  :class="{ 'input-error': errors.nomentreprise }"
                  required
                />
                <span class="error-message" v-if="errors.nomentreprise">{{ errors.nomentreprise }}</span>
              </div>
              <div class="input-group">
                <label for="site">Site</label>
                <input
                  type="url"
                  v-model.trim="formData.site"
                  id="site"
                  placeholder="Écrire votre site web"
                  :class="{ 'input-error': errors.Site }"
                  required
                />
                <span class="error-message" v-if="errors.Site">{{ errors.Site }}</span>
              </div>
            </div>
            <div class="input-group">
  <label for="departement">Département</label>
  <input
    type="text"
    v-model.trim="formData.departement"
    id="departement"
    placeholder="Écrire votre département"
    :class="{ 'input-error': errors.departement }"
    required
  />
  <span class="error-message" v-if="errors.departement">{{ errors.departement }}</span>
</div>

            <div class="input-group">
              <label for="lieu">Localisation</label>
              <input
                type="text"
                v-model.trim="formData.lieu"
                id="lieu"
                placeholder="Écrire votre localisation"
                :class="{ 'input-error': errors.lieu }"
                required
              />
              <span class="error-message" v-if="errors.lieu">{{ errors.lieu }}</span>
            </div>

            <div class="input-group">
              <label for="decription">Description de l'entreprise</label>
              <textarea
                v-model.trim="formData.decription"
                id="decription"
                placeholder="Écrire votre description"
                :class="{ 'input-error': errors.decription }"
                required
              ></textarea>
              <span class="error-message" v-if="errors.decription">{{ errors.decription }}</span>
            </div>

            <button type="submit" class="btn-submit" :disabled="loading">
              <span v-if="loading" class="loading-spinner"></span>
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


<script>
import { defineComponent, ref, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/axios';
import hiringImage from '../../assets/recruteurhiring.png';

export default defineComponent({
  name: 'RegisterRecruteur',
  setup() {
    const router = useRouter();
    const isSubmitted = ref(false);
    const loading = ref(false);
    const showPassword = ref(false);

    const formData = reactive({
      nom: '', prenom: '', email: '', password: '',
      cin: '', numtel: '', nomentreprise: '', site: '',
      departement: '', lieu: '', decription: ''
    });

    const errors = reactive({});

    const passwordStrength = computed(() => {
      const p = formData.password;
      return [/[a-z]/, /[A-Z]/, /\d/, /[^a-zA-Z\d]/].reduce((a, r) => a + r.test(p), 0);
    });

    const passwordStrengthClass = computed(() => {
      return ['weak', 'medium', 'strong'][Math.min(passwordStrength.value, 2)];
    });

    const passwordStrengthText = computed(() => {
      return ['Faible', 'Moyen', 'Fort'][Math.min(passwordStrength.value, 2)];
    });

    function togglePassword() {
      showPassword.value = !showPassword.value;
    }

    function validateForm() {
      Object.keys(errors).forEach(k => delete errors[k]);
      let valid = true;
      const nameRegex = /^[a-zA-ZÀ-ÿ\s'-]+$/;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const urlRegex = /^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/\S*)?$/;

      if (!formData.nom || !nameRegex.test(formData.nom)) {
        errors.nom = 'Nom invalide'; valid = false;
      }
      if (!formData.prenom || !nameRegex.test(formData.prenom)) {
        errors.prenom = 'Prénom invalide'; valid = false;
      }
      if (!formData.email || !emailRegex.test(formData.email)) {
        errors.email = 'Email invalide'; valid = false;
      }
      if (formData.password.length < 8) {
        errors.password = 'Mot de passe trop court'; valid = false;
      }
      if (!/^\d{8}$/.test(formData.cin)) {
        errors.cin = 'CIN invalide'; valid = false;
      }
      if (!/^\d{8}$/.test(formData.numtel)) {
        errors.numtel = 'Téléphone invalide'; valid = false;
      }
      if (!formData.nomentreprise || !nameRegex.test(formData.nomentreprise)) {
        errors.nomentreprise = 'Nom entreprise invalide'; valid = false;
      }
      if (!urlRegex.test(formData.site)) {
        errors.site = 'Site web invalide'; valid = false;
      }
      if (!formData.departement || !nameRegex.test(formData.departement)) {
        errors.departement = 'Département invalide'; valid = false;
      }
      if (!formData.lieu || !nameRegex.test(formData.lieu)) {
        errors.lieu = 'Lieu invalide'; valid = false;
      }
      if (!formData.decription || formData.decription.length < 10) {
        errors.decription = 'Description trop courte'; valid = false;
      }
      return valid;
    }

    async function register() {
      if (!validateForm()) return;
      loading.value = true;
      try {
        await api.get('/sanctum/csrf-cookie');
        const response = await api.post('/api/register-recruteur', formData);
        sessionStorage.setItem('userSession', JSON.stringify(response.data));
        isSubmitted.value = true;
      } catch (error) {
        console.error(error);
        alert("Erreur serveur lors de l'inscription");
      } finally {
        loading.value = false;
      }
    }

    function goToCompteRecruteur() {
      router.push('/CompteRecruteur');
    }

    return {
      formData, errors, loading, showPassword, togglePassword,
      register, isSubmitted, goToCompteRecruteur,
      passwordStrengthClass, passwordStrengthText, hiringImage
    };
  }
});
</script>


<style>
/* ===== Variables et styles de base ===== */

/* ===== Auth Container ===== */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #e0ecff, #f4f7fc);
  padding: 1rem;
}.auth-content {
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
  max-width: 550px; /* Augmenté depuis 500px */
  max-height: 480px; 
}

.hiring-image:hover {
  transform: scale(1.05);
}

.auth-box {
  margin-top: 60px;
  background: #fff;
  border-radius: var(--border-radius);
  max-width: 520px;
  width: 100%;
  padding: 40px;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
  border: 1px solid var(--input-border);
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}


.auth-title {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 25px;
  color: #222;
  text-align: center;
}
.auth-title::after {
  content: '';
  display: block;
  width: 80px;
  height: 3px;
  background: var(--primary-color);
  margin: 10px auto 0;
  border-radius: 2px;
}

/* ===== Form Styles ===== */
.form-section {
  flex: 1;
  padding: 2rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
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
}

.input-group label {
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
}

.input-group input {
  padding: 12px;
  padding-right: 40px; /* ✅ Ajout pour éviter le chevauchement avec l’icône 👁️ */
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

.error-message {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
}

.password-input-container {
  position: relative;
  width: 100%; /* 🔥 Fait en sorte que le container ne soit pas plus petit */
  display: flex;
  align-items: center;
}

.password-input-container input {
  flex: 1; /* 🔥 Permet à l’input de prendre toute la largeur disponible */
  padding-right: 40px; /* Assure de la place pour l’icône */
}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 10px;
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


.toggle-password:hover {
  color: var(--primary-color);
}

.password-strength {
  margin-top: 8px;
}

.strength-bar {
  height: 5px;
  border-radius: 3px;
  margin-bottom: 5px;
  transition: all 0.3s ease;
}

.weak {
  background-color: var(--danger-color);
  width: 30%;
}

.medium {
  background-color: var(--warning-color);
  width: 60%;
}

.strong {
  background-color: var(--success-color);
  width: 100%;
}

/* ===== Checkbox ===== */
.checkbox-group {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0;
}

.checkbox-group input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.checkbox-group label {
  margin-bottom: 0;
  font-weight: normal;
  font-size: 0.9rem;
  cursor: pointer;
}

.checkbox-group a {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
}

.checkbox-group a:hover {
  text-decoration: underline;
}
.btn-submit {
  margin-top: 20px;
  padding: 14px 20px;
  width: 100%;
  font-size: 16px;
  font-weight: bold;
  color: white;
  background-color: #007BFF;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px; /* ✅ pour espacer le spinner du texte */
}

.btn-submit:hover {
  background-color: #0056d2;
}

.btn-submit:disabled {
  background-color: #bbb;
  cursor: not-allowed;
}
.loading-spinner {
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  animation: spin 0.6s linear infinite;
  display: inline-block;
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
  transition: background-color 0.3s ease;
}

.strength-bar.weak {
  background-color: #dc3545;
}

.strength-bar.medium {
  background-color: #ffc107;
}

.strength-bar.strong {
  background-color: #28a745;
}

.password-requirements {
  font-size: 12px;
  color: #666;
  margin-top: 5px;
  line-height: 1.4;
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