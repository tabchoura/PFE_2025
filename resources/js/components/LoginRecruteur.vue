<template>
  <!-- Formulaire avec affichage amélioré -->
  <div class="login-container">
    <div class="login-card">
      <div class="image-section">
        <img :src="logincandidat" alt="Login" class="login-image" />
        <div class="overlay">
          <div class="brand-text">
            <h2>Bienvenue</h2>
            <p>Accédez à votre espace personnel et gérez vos candidatures</p>
          </div>
        </div>
      </div>

      <div class="form-section">
        <form @submit.prevent="login" class="login-form">
          <div class="form-header">
            <h1>Connexion Recruteur</h1>
            <p class="form-subtitle">Accédez à votre espace personnel</p>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
              <span class="input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </span>
              <input 
                type="email" 
                v-model="email" 
                required 
                placeholder="votre@email.com"
                :class="{ 'input-error': errors.email }"
              />
            </div>
            <p v-if="errors.email" class="error-message">{{ errors.email }}</p>
          </div>

          <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="input-wrapper password-input-container">
              <span class="input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v3m-6-3v3m12-3v3M3.75 9.765c0-1.125.63-2.16 1.64-2.7l5.25-2.63a2.25 2.25 0 012.1 0l5.25 2.63c1.01.54 1.64 1.575 1.64 2.7V16.5a2.25 2.25 0 01-2.25 2.25h-12A2.25 2.25 0 013.75 16.5V9.765z"/></svg>
              </span>
              <input
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                required
                placeholder="Entrez votre mot de passe"
                :class="{ 'input-error': errors.password }"
              />
              <button
                type="button"
                class="toggle-password"
                @click="togglePassword"
                :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
              >
                <span v-if="showPassword">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </span>
                <span v-else>
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
              </button>
            </div>
            <p v-if="errors.password" class="error-message">{{ errors.password }}</p>
          </div>

          <div class="form-options">
            <div class="remember-me">
              <label class="checkbox-container">
                <input type="checkbox" v-model="rememberMe">
                <span class="checkmark"></span>
                Se souvenir de moi
              </label>
            </div>
            <a href="#" @click.prevent="resetPassword" class="forgot-password">Mot de passe oublié ?</a>
          </div>

          <button type="submit" :disabled="isLoading" class="login-button">
            <span v-if="isLoading" class="loading-spinner"></span>
            <span v-else>Se connecter</span>
          </button>
        </form>

        <div v-if="error" class="error-notification">
          <span class="error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
          </span>
          {{ error }}
          <button @click="error = ''" class="close-error">×</button>
        </div>
        
        <div v-if="successMessage" class="success-notification">
          <span class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
          {{ successMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import logincandidat from "../../assets/logincandidat.jpg";

const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const error = ref("");
const successMessage = ref("");
const errors = ref({
  email: "",
  password: ""
});
const isLoading = ref(false);
const showPassword = ref(false);

const validateForm = () => {
 

  if (!email.value) {
    errors.value.email = "L'email est requis";
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = "Format d'email invalide";
    isValid = false;
  }

  if (!password.value) {
    errors.value.password = "Le mot de passe est requis";
    isValid = false;
  }

  return isValid;
};

const login = async () => {
  if (!validateForm()) return;

  isLoading.value = true;
  error.value = "";

  try {
    await axios.get("/sanctum/csrf-cookie");

    const response = await axios.post("/api/login", {
      email: email.value,
      password: password.value,
      remember: rememberMe.value
    }, {
      withCredentials: true
    });

    const userData = response.data;
    const storage = rememberMe.value ? localStorage : sessionStorage;
    storage.setItem("userSession", JSON.stringify(userData));

    showSuccessMessage();
    
    setTimeout(() => {
      router.push( "/monprofilerecruteur");
    }, 1500);
  } catch (err) {
    if (err.response && err.response.status === 401) {
      error.value = "Identifiants incorrects ou utilisateur non trouvé";
    } else {
      error.value = "Une erreur est survenue. Veuillez réessayer.";
    }
  } finally {
    isLoading.value = false;
  }
};

const resetPassword = () => {
  window.location.href = "/reset-password";
};



const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const showSuccessMessage = () => {
  successMessage.value = "Connexion réussie! Redirection en cours...";
  setTimeout(() => {
    successMessage.value = "";
  }, 3000);
};
</script>

<style scoped>
/* Variables pour les couleurs et les styles */
:root {
  --primary-color: #2563eb;
  --secondary-color: #1d4ed8;
  --secondary-color: #3b82f6;
  --dark-blue: #1e40af;
  --light-blue: #dbeafe;
  --success-color: #10b981;
  --error-color: #ef4444;
  --text-color: #1e293b;
  --text-light: #64748b;
  --bg-color: #f8fafc;
  --card-bg: #ffffff;
  --border-color: #e2e8f0;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --border-radius: 12px;
  --transition: all 0.3s ease;
}

/* Styles de base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Inter', 'Segoe UI', sans-serif;
  background-color: var(--bg-color);
  color: var(--text-color);
  line-height: 1.5;
}

/* Layout principal */
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f0f5ff;
  padding: 20px;
  margin-top: 60px;
  text-align: left;
  font-family: 'Poppins', sans-serif;
}

.login-card {
  display: flex;
  max-width: 1000px;
  width: 100%;
  background-color: var(--card-bg);
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--shadow-lg);
  animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Section image */
.image-section {
  flex: 1;
  position: relative;
  background-color: var(#2563eb);
  overflow: hidden;
  display: none;
}

.login-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  opacity: 0.85;
  transition: transform 1s ease;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(17, 24, 39, 0.2), rgba(17, 24, 39, 0.7));
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 2.5rem;
}

.brand-text {
  color: white;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.brand-text h2 {
  font-size: 2.2rem;
  font-weight: 700;
  margin-bottom: 0.8rem;
}

.brand-text p {
  font-size: 1rem;
  font-weight: 400;
  opacity: 0.9;
  max-width: 80%;
}

/* Section formulaire */
.form-section {
  flex: 1;
  padding: 2.5rem;
  background-color: var(--card-bg);
  position: relative;
}

.login-form {
  max-width: 450px;
  width: 100%;
  margin: 0 auto;
}

.form-header {
  text-align: center;
  margin-bottom: 2rem;
}

h1 {
  color: var(--text-color);
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  position: relative;
  display: inline-block;
}

h1::after {
  content: '';
  display: block;
  width: 80px;
  height: 3px;
  background: linear-gradient(to right, var(#2563eb), var(--secondary-color));
  margin: 0.5rem auto 0;
  border-radius: 2px;
}

.form-subtitle {
  color: var(--text-light);
  font-size: 1rem;
}

/* Groupes de formulaire */
.form-group {
  margin-bottom: 1.5rem;
}

label {
  display: block;
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-light);
  transition: var(--transition);
  z-index: 1;
  display: flex;
  align-items: center;
}

input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.75rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  color: var(--text-color);
  background-color: #f9fafb;
  transition: var(--transition);
}

input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
  background-color: white;
  outline: none;
}

input:focus + .input-icon {
  color: #2563eb;
}

input::placeholder {
  color: #a0aec0;
}

.input-error {
  border-color: var(--error-color) !important;
}

.input-error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
}

.error-message {
  color: var(--error-color);
  font-size: 0.8rem;
  margin-top: 0.4rem;
  animation: shake 0.5s ease;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
  20%, 40%, 60%, 80% { transform: translateX(4px); }
}

/* Basculement du mot de passe */
.password-input-container {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 0.8rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  padding: 0.25rem;
  color: var(--text-light);
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
}

.toggle-password:hover {
  color: #2563eb;
}

/* Options de connexion */
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.remember-me {
  display: flex;
  align-items: center;
}

.checkbox-container {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 0.875rem;
  color: var(--text-color);
  user-select: none;
}

.checkbox-container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  height: 18px;
  width: 18px;
  background-color: #f1f5f9;
  border: 1.5px solid var(--border-color);
  border-radius: 4px;
  margin-right: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
}

.checkbox-container:hover input ~ .checkmark {
  background-color: #e2e8f0;
}

.checkbox-container input:checked ~ .checkmark {
  background-color: var(#2563eb);
  border-color: var(#2563eb);
}

.checkmark:after {
  content: "";
  position: absolute;
  display: none;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
  margin-top: -2px;
}

.checkbox-container input:checked ~ .checkmark:after {
  display: block;
}

.forgot-password {
  font-size: 0.875rem;
  color: var(#2563eb);
  text-decoration: none;
  transition: var(--transition);
}

.forgot-password:hover {
  color: var(#1d4ed8);
  text-decoration: underline;
}

/* Bouton de connexion */
.login-button {
  width: 100%;
  padding: 0.8rem 1rem;
  margin-top: 0.5rem;
  background:#2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 3rem;
  box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
}

.login-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: 0.6s;
}

.login-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
  background: linear-gradient(to right, var(#1d4ed8), var(--dark-blue));
}

.login-button:hover::before {
  left: 100%;
}

.login-button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.login-button:disabled {
  background: #cbd5e1;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Animation de chargement */
.loading-spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Pied de formulaire */
.form-footer {
  text-align: center;
  margin-top: 1.5rem;
}

.no-account {
  font-size: 0.875rem;
  color: var(--text-light);
}

.no-account a {
  color: var(#2563eb);
  font-weight: 600;
  text-decoration: none;
  transition: var(--transition);
}

.no-account a:hover {
  color: var(#1d4ed8);
  text-decoration: underline;
}

/* Notifications */
.error-notification, 
.success-notification {
  position: relative;
  padding: 1rem;
  border-radius: 8px;
  margin-top: 1.5rem;
  animation: slideIn 0.4s ease;
  display: flex;
  align-items: center;
}

.error-notification {
  background-color: #fef2f2;
  color: #b91c1c;
  border-left: 4px solid var(--error-color);
}

.success-notification {
  background-color: #ecfdf5;
  color: #065f46;
  border-left: 4px solid var(--success-color);
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.error-icon, 
.success-icon {
  margin-right: 0.75rem;
  flex-shrink: 0;
}

.close-error {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #b91c1c;
  cursor: pointer;
  width: 1.5rem;
  height: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: var(--transition);
}

.close-error:hover {
  background-color: rgba(239, 68, 68, 0.1);
}

/* Media queries pour responsive */
@media (min-width: 768px) {
  .image-section {
    display: block;
  }
  
  .login-card {
    flex-direction: row;
  }
  
  .form-section {
    padding: 3rem;
  }
}

@media (max-width: 767px) {
  .login-card {
    flex-direction: column;
    max-width: 500px;
  }
  
  .form-section {
    padding: 2rem 1.5rem;
  }
  
  h1 {
    font-size: 1.6rem;
  }
  
  .form-options {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .login-container {
    padding: 1rem;
  }
  
  .form-section {
    padding: 1.5rem 1rem;
  }
  
  input {
    padding: 0.7rem 1rem 0.7rem 2.5rem;
    font-size: 0.95rem;
  }
  
  .login-button {
    height: 2.75rem;
    font-size: 0.95rem;
  }
}

/* Animation pour l'image */
.login-card:hover .login-image {
  transform: scale(1.05);
}

/* Animation de pulse pour les boutons et effets de focus */
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
  70% { box-shadow: 0 0 0 10px rgba(37, 99, 235, 0); }
  100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
}

.login-button:focus:not(:disabled) {
  animation: pulse 1.5s infinite;
}
</style>