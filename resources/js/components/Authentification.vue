<template>
  <div class="auth-container">
    <div class="login-layout">
      <!-- VISUEL GAUCHE -->
      <div class="image-container">
        <div class="image-overlay">
          <h1 class="platform-name">Jobgo</h1>
          <p class="platform-slogan">Connectez talents et opportunités</p>
        </div>
      </div>

      <!-- FORMULAIRE -->
      <div class="form-container">
        <div v-if="page === 'login'" class="auth-box">
          <div class="logo-container">
            <i class="fas fa-briefcase logo-icon"></i>
          </div>

          <h2>Connexion à votre espace</h2>

          <!-- Bascule Candidat / Recruteur -->
          <div class="toggle-container">
            <!-- Ajout highlight sur les deux et active selon isRecruteur -->
            <span class="highlight" :class="{ active: !isRecruteur }">Candidat</span>
            <label class="switch">
              <input
                type="checkbox"
                v-model="isRecruteur"
                aria-label="Basculer le type de compte"
              />
              <span class="slider"></span>
            </label>
            <span class="highlight" :class="{ active: isRecruteur }">Recruteur</span>
          </div>

          <!-- Formulaire -->
          <form @submit.prevent="login" novalidate>
            <!-- Email -->
            <div class="input-group">
              <label for="email">Email</label>
              <div class="input-with-icon">
                <i class="fas fa-envelope input-icon"></i>
                <input
                  id="email"
                  type="email"
                  placeholder="votre@email.com"
                  v-model.trim="email"
                  :class="{ error: emailError }"
                  @input="emailError = false"
                  required
                />
              </div>
              <p v-if="emailError" class="error-message">
                <i class="fas fa-exclamation-circle"></i> Email invalide
              </p>
            </div>

            <!-- Mot de passe -->
            <div class="input-group">
              <label for="password">Mot de passe</label>
              <div class="input-with-icon">
                <i class="fas fa-lock input-icon"></i>
                <input
                  id="password"
                  :type="showPassword ? 'text' : 'password'"
                  v-model.trim="password"
                  placeholder="Votre mot de passe"
                  :class="{ error: passwordError }"
                  @input="passwordError = false"
                  required
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
              <p v-if="passwordError" class="error-message">
                <i class="fas fa-exclamation-circle"></i> Mot de passe requis (min. 6
                caractères)
              </p>
            </div>

            <div class="remember-forgot">
              <label class="remember-me">
                <input type="checkbox" v-model="rememberMe" />
                <span class="checkmark"></span>
                Se souvenir de moi
              </label>
              <a href="#" class="forgot-password">Mot de passe oublié&nbsp;?</a>
            </div>

            <button :disabled="isLoading" type="submit" class="btn-submit">
              <span v-if="isLoading" class="loading-spinner"></span>
              <span v-else><i class="fas fa-sign-in-alt"></i> Se connecter</span>
            </button>

            <div class="or-divider"><span>OU</span></div>

            <!-- Phrase en bas avec un seul mot en highlight actif -->
            <div class="signup-prompt">
              <h3>
                Vous n'avez pas de compte
                <span class="highlight">{{ isRecruteur ? "Recruteur" : "Candidat" }}</span
                >&nbsp;?
              </h3>
              <button class="btn-create" @click.prevent="goToSignup">
                <i class="fas fa-user-plus"></i> Créer un compte
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <RegisterCandidat
      v-if="page === 'registerCandidat'"
      @registration-complete="handleRegistrationComplete"
    />
    <RegisterRecruteur
      v-if="page === 'registerRecruteur'"
      @registration-complete="handleRegistrationComplete"
    />
  </div>
</template>

<script setup>
// (Script identique au tien, inchangé)
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/axios";

import RegisterCandidat from "../components/RegisterCandidat.vue";
import RegisterRecruteur from "../components/RegisterRecruteur.vue";

const router = useRouter();

const email = ref("");
const password = ref("");
const isRecruteur = ref(false);
const page = ref("login");
const isLoading = ref(false);
const emailError = ref(false);
const passwordError = ref(false);
const showPassword = ref(false);
const rememberMe = ref(false);

const validateForm = () => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  emailError.value = !emailRegex.test(email.value);
  passwordError.value = password.value.length < 6;
  return !emailError.value && !passwordError.value;
};

const login = async () => {
  if (!validateForm()) return;
  isLoading.value = true;

  try {
    await api.get("/sanctum/csrf-cookie");

    const response = await api.post(
      "/api/login",
      {
        email: email.value,
        password: password.value,
      },
      { withCredentials: true }
    );

    if (
      (isRecruteur.value && response.data.type !== "recruteur") ||
      (!isRecruteur.value && response.data.type !== "candidat")
    ) {
      alert("❌ Vous avez sélectionné le mauvais type de compte.");
      isLoading.value = false;
      return;
    }

    const userData = response.data;
    const storage = rememberMe.value ? localStorage : sessionStorage;
    storage.setItem("userSession", JSON.stringify(userData));

    router.push(userData.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat");
  } catch (error) {
    alert("❌ Identifiants incorrects ou utilisateur non trouvé");
    console.error("Erreur lors de la connexion:", error);
  } finally {
    isLoading.value = false;
  }
};

const goToSignup = () => {
  page.value = isRecruteur.value ? "registerRecruteur" : "registerCandidat";
  router.push(
    page.value === "registerRecruteur" ? "/registerRecruteur" : "/registerCandidat"
  );
};

const handleRegistrationComplete = (userData) => {
  const storage = rememberMe.value ? localStorage : sessionStorage;
  storage.setItem("userSession", JSON.stringify(userData));
  router.push(userData.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat");
};

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

onMounted(() => {
  const session =
    localStorage.getItem("userSession") || sessionStorage.getItem("userSession");
  if (session && document.referrer === "") {
    const user = JSON.parse(session);
    email.value = user.email;
    isRecruteur.value = user.type === "recruteur";
    router.push(user.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat");
  }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");

/* --- Layout général --- */
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: linear-gradient(135deg, #e0eafc, #cfdef3);
  font-family: "Inter", sans-serif;
}

.login-layout {
  display: flex;
  width: 960px;
  max-width: 100%;
  background: #ffffff;
  border-radius: 1.5rem;
  overflow: hidden;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

.image-container {
  flex: 1;
  position: relative;
  background: linear-gradient(135deg, #1f3d7a 0%, #3c64c8 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.image-overlay {
  text-align: center;
  color: #ffffff;
}

.platform-name {
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.platform-slogan {
  font-size: clamp(1rem, 2.5vw, 1.25rem);
  opacity: 0.9;
  max-width: 20ch;
  margin-inline: auto;
}

.form-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2.5rem 2rem;
}

.auth-box {
  width: min(100%, 380px);
}

.logo-container {
  width: 64px;
  height: 64px;
  margin: 0 auto 1rem auto;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #ffffff;
}

.logo-icon {
  font-size: 1.75rem;
}

h2 {
  font-size: 1.5rem;
  text-align: center;
  margin-bottom: 1.5rem;
  font-weight: 600;
  color: #1e3a8a;
}

.toggle-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.toggle-container span {
  transition: 0.3s;
  user-select: none;
  color: #6b7280; /* gris par défaut */
}

.toggle-container span.active {
  color: #3d63ea;
  font-weight: 700;
}

.highlight {
  color: #3d63ea;
  font-weight: 700;
}

.switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  inset: 0;
  background: #d9d9d9;
  border-radius: 24px;
  cursor: pointer;
  transition: 0.4s;
}

.slider::before {
  content: "";
  position: absolute;
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background: #ffffff;
  border-radius: 50%;
  transition: 0.4s;
}

input:checked + .slider {
  background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);
}

input:checked + .slider::before {
  transform: translateX(22px);
}

/* Champs texte */
.input-group {
  margin-bottom: 1.25rem;
}

.input-group label {
  display: block;
  margin-bottom: 0.25rem;
  font-weight: 600;
}

.input-with-icon {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

.input-with-icon input {
  width: 100%;
  padding: 0.65rem 0.75rem 0.65rem 40px;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  outline: none;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.input-with-icon input:focus {
  border-color: #1f3d7a;
  box-shadow: 0 0 0 3px rgba(31, 61, 122, 0.15);
}

.input-with-icon input.error {
  border-color: #e11d48;
}

.error-message {
  margin-top: 0.3rem;
  color: #e11d48;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #9ca3af;
  padding: 0 4px;
}

.remember-forgot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.25rem;
  font-size: 0.875rem;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  user-select: none;
  position: relative;
}

.remember-me input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.checkmark {
  width: 16px;
  height: 16px;
  border: 2px solid #d1d5db;
  border-radius: 3px;
  background: #ffffff;
  display: inline-block;
}

.remember-me input:checked + .checkmark {
  background: #1f3d7a;
  border-color: #1f3d7a;
}

.remember-me input:checked + .checkmark::after {
  content: "";
  position: absolute;
  left: 4px;
  top: 0;
  width: 4px;
  height: 8px;
  border: solid #ffffff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.forgot-password {
  color: #1f3d7a;
  text-decoration: none;
}

.btn-submit {
  width: 100%;
  padding: 0.75rem;
  background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);
  color: #ffffff;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s, transform 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-submit:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

.btn-submit:hover:not(:disabled) {
  background: #0468bf;
  transform: translateY(-2px);
}

.loading-spinner {
  width: 18px;
  height: 18px;
  border: 3px solid #fff;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.or-divider {
  display: flex;
  align-items: center;
  margin: 1.5rem 0;
}

.or-divider::before,
.or-divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: #e5e7eb;
}

.or-divider span {
  margin: 0 0.75rem;
  color: #6b7280;
  font-weight: 600;
}

.signup-prompt {
  text-align: center;
}

.signup-prompt h3 {
  font-size: 1rem;
  margin-bottom: 0.75rem;
}

.highlight {
  color: #0468bf;
  font-weight: 700;
}

.btn-create {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 1.25rem;
  border: 2px solid #1f3d7a;
  background: transparent;
  color: #0468bf;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-create:hover {
  background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);
  color: #ffffff;
}

@media (max-width: 768px) {
  .login-layout {
    flex-direction: column;
  }

  .image-container {
    height: 200px;
  }
}
</style>
