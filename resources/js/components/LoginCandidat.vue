<template>
  <div class="login-container">
    <div class="login-card">
      <div class="image-section">
        <img :src="logincandidat" alt="Recrutement" class="login-image" />
        <div class="overlay">
          <div class="brand-text">
            <h2>Bienvenue</h2>
            <p>Accédez à votre espace personnel et gérez vos candidatures</p>
          </div>
        </div>
      </div>

      <div class="form-section">
        <form @submit.prevent="login" class="login-form" novalidate>
          <div class="form-header">
            <h1 class="login-title">Connexion Candidat</h1>
            <p class="login-subtitle">Accédez à votre espace personnel</p>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
              <span class="input-icon">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  width="18"
                  height="18"
                >
                  <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                  />
                </svg>
              </span>
              <input
                id="email"
                type="email"
                v-model="email"
                required
                placeholder="votre@email.com"
                :class="[{ 'input-error': errors.email }]"
              />
            </div>
            <p v-if="errors.email" class="error-message">{{ errors.email }}</p>
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="input-wrapper password-input-container">
              <span class="input-icon">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  width="18"
                  height="18"
                >
                  <path
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 15v3m-6-3v3m12-3v3M3.75 9.765c0-1.125.63-2.16 1.64-2.7l5.25-2.63a2.25 2.25 0 012.1 0l5.25 2.63c1.01.54 1.64 1.575 1.64 2.7V16.5a2.25 2.25 0 01-2.25 2.25h-12A2.25 2.25 0 013.75 16.5V9.765z"
                  />
                </svg>
              </span>
              <input
                id="password"
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                required
                placeholder="Entrez votre mot de passe"
                :class="[{ 'input-error': errors.password }]"
              />
              <button
                type="button"
                class="toggle-password"
                @click="togglePassword"
                :aria-label="
                  showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'
                "
              >
                <span v-if="showPassword">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    width="20"
                    height="20"
                  >
                    <path
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"
                    />
                  </svg>
                </span>
                <span v-else>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    width="20"
                    height="20"
                  >
                    <path
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                    />
                    <path
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                  </svg>
                </span>
              </button>
            </div>
            <p v-if="errors.password" class="error-message">{{ errors.password }}</p>
          </div>

          <!-- Form Options -->
          <div class="form-options">
            <label class="checkbox-container">
              <input type="checkbox" v-model="rememberMe" />
              <span class="checkmark"></span>
              Se souvenir de moi
            </label>
            <a href="#" @click.prevent="resetPassword" class="forgot-password"
              >Mot de passe oublié ?</a
            >
          </div>

          <button type="submit" :disabled="isLoading" class="login-button">
            <span v-if="isLoading" class="loading-spinner"></span>
            <span v-else>Se connecter</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import logincandidat from "../../assets/logincandidat.jpg";
import { useRouter, useRoute } from "vue-router";
import { useToast } from "vue-toastification";
const router = useRouter();
const toast = useToast();
const route = useRoute();
const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const errors = ref({ email: "", password: "" });
const isLoading = ref(false);
const showPassword = ref(false);

const validateForm = () => {
  // errors.value.email = "";
  // errors.value.password = "";
  let valid = true;

  if (!email.value) {
    errors.value.email = "L'email est requis";
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    toast.error("Format d'email invalide");
    valid = false;
  }

  if (!password.value) {
    toast.error("Le mot de passe est requis");
    valid = false;
  }

  return valid;
};

const login = async () => {
  if (!validateForm()) return;

  isLoading.value = true;

  try {
    await axios.get("/sanctum/csrf-cookie");
    const response = await axios.post(
      "/api/login",
      { email: email.value, password: password.value, remember: rememberMe.value },
      { withCredentials: true }
    );

    const userData = response.data;
    const storage = rememberMe.value ? localStorage : sessionStorage;
    storage.setItem("userSession", JSON.stringify(userData));

    successMessage.value = "Connexion réussie ! Redirection en cours...";
    setTimeout(() => {
      const { redirect, offerId, cvId } = route.query;
      if (redirect === "detailsoffre" && offerId && cvId) {
        router.push({ name: "Detailsoffre", params: { offerId, cvId } });
      } else {
        router.push("/monprofile");
      }
    }, 1500);
  } catch (err) {
    if (err.response && err.response.status === 401) {
      toast.error("Identifiants incorrects ou utilisateur non trouvé");
    } else {
      toast.error = "Une erreur est survenue. Veuillez réessayer.";
    }
  } finally {
    isLoading.value = false;
  }
};

const resetPassword = () => (window.location.href = "/reset-password");
const togglePassword = () => (showPassword.value = !showPassword.value);
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
  padding: 1rem;
}

.login-card {
  display: flex;
  width: 100%;
  max-width: 900px;
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
}

.image-section {
  position: relative;
  width: 45%;
  min-height: 500px;
}

.login-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.6));
  display: flex;
  align-items: flex-end;
  justify-content: center;
  text-align: center;
  padding: 2rem;
  box-sizing: border-box;
}

.brand-text {
  margin-bottom: 2rem;
}

.brand-text h2 {
  color: #fff;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.brand-text p {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1.1rem;
  line-height: 1.5;
  margin: 0;
}

.form-section {
  flex: 1;
  padding: 3rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.form-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-title {
  font-size: 1.8rem;
  color: #1e3a8a;
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.login-subtitle {
  color: #3d90d7;
  font-size: 1rem;
  margin: 0;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  margin-bottom: 0.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #2c3e50;
  font-weight: 500;
  font-size: 0.95rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
}

.input-wrapper:focus-within {
  box-shadow: 0 0 0 3px rgba(61, 144, 215, 0.2);
}

.input-icon {
  position: absolute;
  left: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #95a5a6;
  z-index: 1;
}

.input-wrapper input {
  width: 100%;
  padding: 0.85rem 0.75rem 0.85rem 2.5rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  background-color: #f9f9f9;
  transition: all 0.3s ease;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #3d90d7;
  background-color: #fff;
  box-shadow: 0 2px 8px rgba(61, 144, 215, 0.1);
}

.password-input-container {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 0.75rem;
  background: none;
  border: none;
  color: #95a5a6;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s ease;
}

.toggle-password:hover {
  color: #3d90d7;
}

.input-error {
  border-color: #e74c3c !important;
  background-color: #fff5f5 !important;
}

.error-message {
  color: #e74c3c;
  font-size: 0.85rem;
  margin-top: 0.25rem;
  padding-left: 0.25rem;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 1rem 0;
}

.checkbox-container {
  position: relative;
  padding-left: 1.75rem;
  cursor: pointer;
  user-select: none;
  font-size: 0.9rem;
  color: #34495e;
  display: flex;
  align-items: center;
}

.checkbox-container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: absolute;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  height: 1.1rem;
  width: 1.1rem;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.checkbox-container:hover input ~ .checkmark {
  border-color: #3d90d7;
}

.checkbox-container input:checked ~ .checkmark {
  background-color: #3d90d7;
  border-color: #3d90d7;
}

.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.checkbox-container input:checked ~ .checkmark:after {
  display: block;
}

.checkbox-container .checkmark:after {
  left: 0.35rem;
  top: 0.1rem;
  width: 0.25rem;
  height: 0.5rem;
  border: solid white;
  border-width: 0 0.15rem 0.15rem 0;
  transform: rotate(45deg);
}

.forgot-password {
  color: #3d90d7;
  font-size: 0.9rem;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease, text-decoration 0.2s ease;
}

.forgot-password:hover {
  color: #2a7bb9;
  text-decoration: underline;
}

.login-button {
  padding: 0.85rem;
  border: none;
  border-radius: 8px;
  background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);
  color: #fff;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 0.5rem;
  letter-spacing: 0.5px;
}

.login-button:hover:not(:disabled) {
  background-color: #2a7bb9;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(61, 144, 215, 0.3);
}

.login-button:active:not(:disabled) {
  transform: translateY(0);
}

.login-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
}

.loading-spinner {
  display: inline-block;
  width: 1.2rem;
  height: 1.2rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
  margin: 0 auto;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.error-notification {
  display: flex;
  align-items: center;
  background: #fef0f0;
  color: #e74c3c;
  padding: 0.85rem 1rem;
  border-radius: 8px;
  margin-top: 1.5rem;
  font-size: 0.9rem;
  border-left: 4px solid #e74c3c;
}

.success-notification {
  display: flex;
  align-items: center;
  background: #f0f9f0;
  color: #27ae60;
  padding: 0.85rem 1rem;
  border-radius: 8px;
  margin-top: 1.5rem;
  font-size: 0.9rem;
  border-left: 4px solid #27ae60;
}

.error-icon,
.success-icon {
  margin-right: 0.75rem;
  font-weight: bold;
}

.close-error {
  background: none;
  border: none;
  font-size: 1.25rem;
  line-height: 1;
  margin-left: auto;
  cursor: pointer;
  color: #e74c3c;
  opacity: 0.7;
  transition: opacity 0.2s ease;
  padding: 0 0.25rem;
}

.close-error:hover {
  opacity: 1;
}

/* Responsive design */
@media (max-width: 768px) {
  .login-card {
    flex-direction: column;
    max-width: 500px;
  }

  .image-section {
    width: 100%;
    min-height: 200px;
  }

  .overlay {
    align-items: center;
    padding: 1.5rem;
  }

  .brand-text h2 {
    font-size: 1.5rem;
  }

  .brand-text p {
    font-size: 0.9rem;
  }

  .form-section {
    padding: 2rem 1.5rem;
  }
}

@media (max-width: 480px) {
  .form-options {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .forgot-password {
    margin-left: 0;
  }
}
</style>
