<template>
  <div class="auth-container">
    <div class="auth-content">
      <!-- Section pour les images avec meilleure disposition -->
      <div class="image-section">
        <img :src="hiringImage" alt="Recrutement" class="hiring-image" />
      </div>

      <!-- Section pour le formulaire -->
      <div class="form-section">
        <div v-if="!isSubmitted" class="auth-box login-box">
          <h2 class="auth-title">Inscription Candidat</h2>
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
                  autocomplete="family-name"
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
                  autocomplete="given-name"
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
                autocomplete="email"
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
                  maxlength="8"
                  inputmode="numeric"
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
                  maxlength="8"
                  inputmode="tel"
                  autocomplete="tel"
                />
                <span class="error-message" v-if="errors.numtel">{{ errors.numtel }}</span>
              </div>
            </div>

            <div class="form-grid">
              <div class="input-group">
                <label for="date">Date de naissance</label>
                <input
                  type="date"
                  v-model="formData.date"
                  id="date"
                  :max="maxDate"
                  :class="{ 'input-error': errors.date }"
                  required
                  autocomplete="bday"
                />
                <span class="error-message" v-if="errors.date">{{ errors.date }}</span>
              </div>
              <div class="input-group">
                <label for="lieu">Lieu de naissance</label>
                <input
                  type="text"
                  v-model.trim="formData.lieu"
                  id="lieu"
                  placeholder="Ville / Commune"
                  :class="{ 'input-error': errors.lieu }"
                  required
                  autocomplete="address-level2"
                />
                <span class="error-message" v-if="errors.lieu">{{ errors.lieu }}</span>
              </div>
            </div>

            <button type="submit" class="btn-submit" :disabled="loading">
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

<script>
import { defineComponent, ref, computed, reactive } from 'vue';
import { useRouter } from 'vue-router';
import hiringImage from "../../assets/Rejoignez-nous.png";

export default defineComponent({
  name: "RegisterCandidat",
  setup() {
    const router = useRouter();
    const isSubmitted = ref(false);
    const formData = reactive({
      email: "",
      password: "",
      prenom: "",
      nom: "",
      date: "",
      lieu: "",
      cin: "",
      numtel: ""
    });

    const loading = ref(false);
    const errors = reactive({});
    const showPassword = ref(false);

    const maxDate = computed(() => {
      const today = new Date();
      const eighteenYearsAgo = new Date(
        today.getFullYear() - 18,
        today.getMonth(),
        today.getDate()
      );
      return eighteenYearsAgo.toISOString().split('T')[0];
    });

    const passwordStrength = computed(() => {
      if (!formData.password) return 0;
      let strength = 0;
      const password = formData.password;
      if (password.length >= 8) strength += 1;
      if (/[a-z]/.test(password)) strength += 1;
      if (/[A-Z]/.test(password)) strength += 1;
      if (/[0-9]/.test(password)) strength += 1;
      if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
      return strength;
    });

    const passwordStrengthClass = computed(() => {
      const strength = passwordStrength.value;
      if (strength <= 2) return 'weak';
      if (strength <= 4) return 'medium';
      return 'strong';
    });

    const passwordStrengthText = computed(() => {
      const strength = passwordStrength.value;
      if (strength <= 2) return 'Faible';
      if (strength <= 4) return 'Moyen';
      return 'Fort';
    });

    function togglePassword() {
      showPassword.value = !showPassword.value;
    }

    function validateForm() {
      Object.keys(errors).forEach(key => delete errors[key]);
      let isValid = true;

      if (!formData.nom) {
        errors.nom = "Le nom est requis";
        isValid = false;
      }

      if (!formData.prenom) {
        errors.prenom = "Le prénom est requis";
        isValid = false;
      }

      if (!formData.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
        errors.email = "Email invalide";
        isValid = false;
      }

      if (!formData.password || formData.password.length < 8) {
        errors.password = "Mot de passe trop court";
        isValid = false;
      }

      if (!formData.cin || !/^\d{8}$/.test(formData.cin)) {
        errors.cin = "CIN invalide";
        isValid = false;
      }

      if (!formData.numtel || !/^\d{8}$/.test(formData.numtel)) {
        errors.numtel = "Numéro de téléphone invalide";
        isValid = false;
      }

      if (!formData.date) {
        errors.date = "Date de naissance requise";
        isValid = false;
      }

      if (!formData.lieu) {
        errors.lieu = "Lieu de naissance requis";
        isValid = false;
      }

      return isValid;
    }

    async function register() {
      if (!validateForm()) return;
      loading.value = true;

      try {
        // Simulate async registration process
        await new Promise(resolve => setTimeout(resolve, 2000));
        loading.value = false;
        isSubmitted.value = true;
      } catch (error) {
        loading.value = false;
        console.error("Erreur d'inscription:", error);
      }
    }
    function goToCompteCandidat() {
  router.push({
    path: "/CompteCandidat",
    state: { userData: formData }
  });
}

    return {
      formData,
      errors,
      loading,
      showPassword,
      maxDate,
      passwordStrengthClass,
      passwordStrengthText,
      togglePassword,
      register,
      goToCompteCandidat,
      isSubmitted,
      hiringImage,
    };
  }
});
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
  max-width: 550px; /* Augmenté depuis 500px */
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