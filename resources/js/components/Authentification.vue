<template>
  <div class="auth-container">
    <div class="login-layout">
      <div class="image-container">
        <img :src="recrutlogin" alt="Plateforme de recrutement" />
        <div class="image-overlay">
          <h1 class="platform-name">Jobgp</h1>
          <p class="platform-slogan">Connectez talents et opportunités</p>
        </div>
      </div>

      <div class="form-container">
        <div class="auth-box" v-if="page === 'login'">
          <div class="logo-container">
            <i class="fas fa-briefcase logo-icon"></i>
          </div>
          
          <h2>Connexion à votre espace</h2>

          <div class="toggle-container">
            <span :class="{ active: !isRecruteur }">Candidat</span>
            <label class="switch">
              <input type="checkbox" v-model="isRecruteur" />
              <span class="slider"></span>
            </label>
            <span :class="{ active: isRecruteur }">Recruteur</span>
          </div>

          <form @submit.prevent="login">
            <div class="input-group">
              <label for="email">Email</label>
              <div class="input-with-icon">
                <i class="fas fa-envelope input-icon"></i>
                <input
                  type="email"
                  v-model="email"
                  id="email"
                  placeholder="votre@email.com"
                  required
                  :class="{ error: emailError }"
                  @input="emailError = false"
                />
              </div>
              <p v-if="emailError" class="error-message">
                <i class="fas fa-exclamation-circle"></i> Email invalide
              </p>
            </div>

            <div class="input-group">
  <label for="password">Mot de passe</label>
  <div class="input-with-icon">
    <i class="fas fa-lock input-icon"></i>
    <input
      :type="showPassword ? 'text' : 'password'"
      v-model="password"
      id="password"
      placeholder="Votre mot de passe"
      required
      :class="{ error: passwordError }"
      @input="passwordError = false"
    />
    <button
      type="button"
      class="toggle-password"
      @click="togglePassword"
    >
      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
    </button>
  </div>
  <p v-if="passwordError" class="error-message">
    <i class="fas fa-exclamation-circle"></i> Mot de passe requis (min. 6 caractères)
  </p>
</div>

            <div class="remember-forgot">
              <label class="remember-me">
                <input type="checkbox" v-model="rememberMe" />
                <span class="checkmark"></span>
                Se souvenir de moi
              </label>
              <a href="#" class="forgot-password">Mot de passe oublié?</a>
            </div>

            <button type="submit" class="btn-submit" :disabled="isLoading">
              <span v-if="isLoading" class="loading-spinner"></span>
              <span v-else><i class="fas fa-sign-in-alt"></i> Se connecter</span>
            </button>

            <div class="or-divider">
              <span>OU</span>
            </div>

            <div class="signup-prompt">
              <h3>
                Vous n'avez pas de compte 
                <span class="highlight">{{ isRecruteur ? "Recruteur" : "Candidat" }}</span> ?
              </h3>
              <button class="btn-create" @click.prevent="goToSignup">
                <i class="fas fa-user-plus"></i> Créer un compte
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Composants d'inscription -->
    <div v-if="page === 'registerCandidat'">
      <RegisterCandidat @registration-complete="handleRegistrationComplete" />
    </div>
    <div v-if="page === 'registerRecruteur'">
      <RegisterRecruteur @registration-complete="handleRegistrationComplete" />
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/axios'

import recrutlogin from "../../assets/authentification.jpg"
import RegisterCandidat from "../components/RegisterCandidat.vue"
import RegisterRecruteur from "../components/RegisterRecruteur.vue"

// Initialisation du router
const router = useRouter()

// Variables
const email = ref("")
const password = ref("")
const isRecruteur = ref(false)
const page = ref("login")
const isLoading = ref(false)
const emailError = ref(false)
const passwordError = ref(false)
const showPassword = ref(false)
const rememberMe = ref(false)

// Validation du formulaire
const validateForm = () => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  emailError.value = !emailRegex.test(email.value)
  passwordError.value = password.value.length < 6
  return !emailError.value && !passwordError.value
}

// Fonction de connexion
const login = async () => {
  if (!validateForm()) return
  isLoading.value = true

  try {
    await api.get("/sanctum/csrf-cookie")

    const response = await api.post("/api/login", {
      email: email.value,
      password: password.value
    }, { withCredentials: true })

    if ((isRecruteur.value && response.data.type !== 'recruteur') ||
        (!isRecruteur.value && response.data.type !== 'candidat')) {
      alert("❌ Vous avez sélectionné le mauvais type de compte.")
      isLoading.value = false
      return
    }

    const userData = response.data
    const storage = rememberMe.value ? localStorage : sessionStorage
    storage.setItem("userSession", JSON.stringify(userData))

    router.push(userData.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat")
  } catch (error) {
    alert("❌ Identifiants incorrects ou utilisateur non trouvé")
    console.error("Erreur lors de la connexion:", error)
  } finally {
    isLoading.value = false
  }
}

// Rediriger vers la page d'inscription
const goToSignup = () => {
  page.value = isRecruteur.value ? "registerRecruteur" : "registerCandidat"
  router.push(page.value === "registerRecruteur" ? "/registerRecruteur" : "/registerCandidat")
}

// Gestion après enregistrement
const handleRegistrationComplete = (userData) => {
  const storage = rememberMe.value ? localStorage : sessionStorage
  storage.setItem("userSession", JSON.stringify(userData))
  router.push(userData.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat")
}

// Afficher ou cacher le mot de passe
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

// Vérifier si déjà connecté
onMounted(() => {
  const session = localStorage.getItem("userSession") || sessionStorage.getItem("userSession")
  if (session && document.referrer === "") {
    const user = JSON.parse(session)
    email.value = user.email
    isRecruteur.value = user.type === "recruteur"
    router.push(user.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat")
  }
})
</script>


<style scoped>
:root {
  --primary-color: #3b82f6;
  --primary-hover: #2563eb;
  --secondary-color: #60a5fa;
  --dark-blue: #1d4ed8;
  --light-blue: #e0f2fe;
  --success-color: #10b981;
  --error-color: #ef4444;
  --text-color: #1f2937;
  --text-light: #6b7280;
  --bg-color: #f9fafb;
  --card-bg: #ffffff;
  --border-color: #d1d5db;
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
  --border-radius: 16px;
  --transition: all 0.3s ease;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Inter', 'Segoe UI', sans-serif;
  background-color: var(--bg-color);
  color: var(--text-color);
}

.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 2rem;
  background: linear-gradient(135deg, #dbeafe, #f0f9ff);
}

.login-layout {
  display: flex;
  max-width: 1100px;
  width: 100%;
  background-color: var(--card-bg);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.image-container {
  flex: 1;
  background-color: var(--primary-color);
  position: relative;
  overflow: hidden;
  display: none;
}

.image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 10s ease;
}

.login-layout:hover .image-container img {
  transform: scale(1.1);
}

.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(29, 78, 216, 0.7));
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
  padding: 2rem;
  text-align: center;
}

.platform-name {
  font-size: 3rem;
  font-weight: 800;
  margin-bottom: 1rem;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.platform-slogan {
  font-size: 1.2rem;
  font-weight: 500;
  opacity: 0.9;
}

.form-container {
  flex: 1;
  padding: 3rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background-color: white;
}

.auth-box {
  width: 100%;
  max-width: 450px;
  margin: 0 auto;
}

.logo-container {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.logo-icon {
  font-size: 2.5rem;
  color: var(--primary-color);
  padding: 1rem;
  background-color: var(--light-blue);
  border-radius: 50%;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.auth-box h2 {
  font-size: 1.8rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 1.5rem;
  color: var(--text-color);
  position: relative;
}

h2::after {
  content: "";
  display: block;
  width: 60px;
  height: 3px;
  background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
  margin: 0.5rem auto 0;
  border-radius: 2px;
}

/* Toggle switch */
.toggle-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-bottom: 30px;
  background: var(--light-blue);
  padding: 10px 18px;
  border-radius: 50px;
  box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
}

.toggle-container span {
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--text-light);
  transition: var(--transition);
}

.toggle-container span.active {
  color: var(--primary-color);
  font-weight: 700;
}

.switch {
  position: relative;
  width: 50px;
  height: 26px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e1;
  transition: var(--transition);
  border-radius: 50px;
}

.slider:before {
  content: "";
  position: absolute;
  height: 20px;
  width: 20px;
  background: white;
  border-radius: 50%;
  bottom: 3px;
  left: 3px;
  transition: var(--transition);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input:checked + .slider {
  background-color: var(--primary-color);
}

input:checked + .slider:before {
  transform: translateX(24px);
}

/* Inputs */
.input-group {
  margin-bottom: 1.5rem;
}

label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  display: block;
  color: var(--text-color);
  font-size: 0.9rem;
}

.input-with-icon {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-light);
  font-size: 1rem;
}

.input-with-icon input {
  width: 100%;
  padding: 0.9rem 2.5rem 0.9rem 2.75rem;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  background-color: #f8fafc;
  transition: var(--transition);
  font-size: 0.95rem;
}

.input-with-icon input::placeholder {
  color: #a3aeb8;
  font-size: 0.9rem;
}

.input-with-icon input:focus {
  border-color: var(--primary-color);
  background-color: #ffffff;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
  outline: none;
}

.input-with-icon .toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--text-light);
  cursor: pointer;
  font-size: 1rem;
  transition: var(--transition);
  padding: 8px;
  border-radius: 50%;
}

.input-with-icon .toggle-password:hover {
  color: var(--primary-color);
  background-color: var(--light-blue);
}


input.error {
  border-color: var(--error-color);
  background-color: #fef2f2;
}

.error-message {
  color: var(--error-color);
  font-size: 0.85rem;
  margin-top: 0.5rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Options */
.remember-forgot {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: var(--text-light);
  cursor: pointer;
  user-select: none;
  position: relative;
}

.remember-me input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkmark {
  position: relative;
  height: 18px;
  width: 18px;
  background-color: #f1f5f9;
  border: 1px solid var(--border-color);
  border-radius: 4px;
  transition: var(--transition);
}

.remember-me:hover input ~ .checkmark {
  background-color: #e2e8f0;
}

.remember-me input:checked ~ .checkmark {
  background-color: var(--primary-color);
}

.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.remember-me input:checked ~ .checkmark:after {
  display: block;
}

.remember-me .checkmark:after {
  left: 6px;
  top: 2px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.forgot-password {
  color: var(--primary-color);
  font-weight: 500;
  text-decoration: none;
  transition: var(--transition);
  font-size: 0.9rem;
}

.forgot-password:hover {
  color: var(--primary-hover);
  text-decoration: underline;
}

/* Boutons */
.btn-submit {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  transition: var(--transition);
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}

.btn-submit:hover {
  background: linear-gradient(to right, var(--primary-hover), var(--dark-blue));
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
}

.btn-submit:active {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Divider */
.or-divider {
  display: flex;
  align-items: center;
  margin: 1.5rem 0;
  color: var(--text-light);
  font-size: 0.9rem;
}

.or-divider::before,
.or-divider::after {
  content: "";
  flex: 1;
  border-bottom: 1px solid var(--border-color);
}

.or-divider span {
  padding: 0 10px;
}

/* Social Login */
.social-login {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.btn-social {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  border: none;
  cursor: pointer;
  transition: var(--transition);
  color: white;
}

.btn-social.google {
  background-color: #ea4335;
}

.btn-social.linkedin {
  background-color: #0077b5;
}

.btn-social:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
}

/* Signup Prompt */
.signup-prompt {
  margin-top: 2rem;
  text-align: center;
}

.signup-prompt h3 {
  font-size: 1rem;
  font-weight: 500;
  margin-bottom: 1rem;
  color: var(--text-light);
}

.highlight {
  color: var(--primary-color);
  font-weight: 700;
}

.btn-create {
  width: 100%;
  padding: 0.9rem;
  background-color: white;
  border: 2px solid var(--primary-color);
  color: var(--primary-color);
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  font-size: 0.95rem;
}

.btn-create:hover {
  background-color: var(--light-blue);
  transform: translateY(-2px);
}

.btn-create:active {
  transform: translateY(0);
}

/* Responsive */
@media (min-width: 768px) {
  .image-container {
    display: block;
  }
}

@media (max-width: 767px) {
  .login-layout {
    flex-direction: column;
  }
  
  .form-container {
    padding: 2rem 1.5rem;
  }
  
  .auth-box h2 {
    font-size: 1.6rem;
  }
  
  .platform-name {
    font-size: 2.2rem;
  }
  
  .platform-slogan {
    font-size: 1rem;
  }
  
  .logo-icon {
    font-size: 2rem;
  }
}
</style>