<template>
  <div class="login-container">
    <div class="login-card">
      <!-- IMAGE SECTION -->
      <div class="image-section">
        <img :src="logincandidat" alt="Recrutement" class="login-image" />
        <div class="overlay">
          <div class="brand-text">
            <h2>Bienvenue</h2>
            <p>Accédez à votre espace personnel et gérez vos candidatures</p>
          </div>
        </div>
      </div>

      <!-- FORM SECTION -->
      <div class="form-section">
        <form @submit.prevent="login" class="login-form" novalidate>
          <div class="form-header">
            <h1 class="login-title">Connexion Candidat</h1>
            <p class="login-subtitle">Accédez à votre espace personnel</p>
          </div>

          <!-- Email Field -->
          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
              <span class="input-icon">
                <!-- Email Icon SVG -->
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

          <!-- Password Field -->
          <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="input-wrapper password-input-container">
              <span class="input-icon">
                <!-- Lock Icon SVG -->
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
                  <!-- Eye Off Icon SVG -->
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
                  <!-- Eye Icon SVG -->
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

          <!-- Submit Button -->
          <button type="submit" :disabled="isLoading" class="login-button">
            <span v-if="isLoading" class="loading-spinner"></span>
            <span v-else>Se connecter</span>
          </button>
        </form>

        <!-- Notifications -->
        <div v-if="errorMessage" class="error-notification">
          <span class="error-icon">
            <!-- Warning Icon SVG -->
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
                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
              />
            </svg>
          </span>
          {{ errorMessage }}
          <button @click="errorMessage = ''" class="close-error">×</button>
        </div>
        <div v-if="successMessage" class="success-notification">
          <span class="success-icon">
            <!-- Check Icon SVG -->
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
                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </span>
          {{ successMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import logincandidat from "../../assets/logincandidat.jpg";
import { useRouter } from "vue-router";

const router = useRouter();
const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const errors = ref({ email: "", password: "" });
const isLoading = ref(false);
const showPassword = ref(false);

const validateForm = () => {
  errors.value.email = "";
  errors.value.password = "";
  let valid = true;

  if (!email.value) {
    errors.value.email = "L'email est requis";
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = "Format d'email invalide";
    valid = false;
  }

  if (!password.value) {
    errors.value.password = "Le mot de passe est requis";
    valid = false;
  }

  return valid;
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
  background-color: #f5f5f5;
}
.login-card {
  display: flex;
  width: 800px;
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.image-section {
  position: relative;
  width: 40%;
}
.login-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 1rem;
}
.brand-text h2,
.brand-text p {
  color: #fff;
}
.brand-text h2 {
  font-size: 1.75rem;
}
.brand-text p {
  margin-top: 0.5rem;
  font-size: 1rem;
}
.form-section {
  flex: 1;
  padding: 2rem;
}
.form-header {
  text-align: center;
  margin-bottom: 1.5rem;
}
.login-title {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 0.5rem;
}
.login-subtitle {
  color: #666;
  font-size: 1rem;
}
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.input-icon {
  position: absolute;
  left: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #888;
}
.input-wrapper input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2.5rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
}
.input-error {
  border-color: #e63946 !important;
}
.error-message {
  color: #e63946;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}
.checkbox-container {
  position: relative;
  padding-left: 1.5rem;
  cursor: pointer;
  user-select: none;
  font-size: 0.875rem;
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
  top: 0;
  left: 0;
  height: 1rem;
  width: 1rem;
  background-color: #eee;
  border-radius: 4px;
}
.checkbox-container:hover input ~ .checkmark {
  background-color: #ccc;
}
.checkbox-container input:checked ~ .checkmark {
  background-color: #2a9d8f;
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
  top: 0.15rem;
  width: 0.25rem;
  height: 0.5rem;
  border: solid white;
  border-width: 0 0.2rem 0.2rem 0;
  transform: rotate(45deg);
}
.forgot-password {
  color: #2a9d8f;
  font-size: 0.875rem;
  text-decoration: none;
}
.forgot-password:hover {
  text-decoration: underline;
}
.login-button {
  padding: 0.75rem;
  border: none;
  border-radius: 8px;
  background-color: #3d90d7;
  color: #fff;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}
.login-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.loading-spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid #fff;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
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
  background: #fdecea;
  color: #b00020;
  padding: 0.75rem;
  border-radius: 8px;
  margin-top: 1rem;
}
.error-icon,
.success-icon {
  margin-right: 0.5rem;
}
.success-notification {
  display: flex;
  align-items: center;
  background: #e6f4ea;
  color: #2a9d8f;
  padding: 0.75rem;
  border-radius: 8px;
  margin-top: 1rem;
}
.close-error {
  background: none;
  border: none;
  font-size: 1.25rem;
  line-height: 1;
  margin-left: auto;
  cursor: pointer;
  color: #b00020;
}
</style>
