<template>
  <div class="offers-section">
    <!-- Titre de la section des offres -->
    <div class="header-actions">
      <h2>Liste des offres</h2>
    </div>

    <!-- Gestion des états de chargement -->
    <div v-if="loading" class="loading-state">
      <p>Chargement des offres...</p>
    </div>

    <!-- Gestion des erreurs -->
    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="getOffres" class="btn-retry">Réessayer</button>
    </div>

    <!-- Affichage quand il n'y a pas d'offres -->
    <div v-else-if="offres.length === 0" class="empty-state">
      <p>Aucune offre disponible pour le moment</p>
    </div>

    <!-- Affichage des offres -->
    <div v-else class="offers-grid">
      <div class="offer-card" v-for="offer in offres" :key="offer.id">
        <div class="offer-card-header">
          <h3 class="title-offre">{{ offer.titre }}</h3>
        </div>

        <div class="offer-card-body">
          <p class="description">
            <strong>Description :</strong> {{ truncateText(offer.description, 100) }}
          </p>
          <div class="offer-details">
            <p class="salaire"><strong>Salaire :</strong> {{ offer.salaire }}</p>
          </div>
        </div>

        <div></div>

        <!-- Bouton Voir plus pour afficher le modal d'authentification -->
        <button class="btn-see-more" @click="checkAuthentication(offer.id)">
          Voir plus
        </button>
      </div>
    </div>
  </div>

  <!-- Modal pour l'authentification -->
  <Teleport to="body">
    <div
      v-if="authModalVisible"
      class="modal-overlay"
      :class="{ showing: modalShowing }"
      @click.self="closeAuthModal"
    >
      <div class="modal-content" :class="{ showing: modalShowing }">
        <button class="close-button" @click="closeAuthModal">×</button>

        <!-- Login Form -->
        <div class="auth-container">
          <div class="login-layout">
            <!-- VISUEL GAUCHE -->
            <div class="image-container">
              <!-- L'image est volontairement remplacée par un dégradé pour une première impression plus légère -->
              <div class="image-overlay">
                <h1 class="platform-name">Jobgo</h1>
                <p class="platform-slogan">Connectez talents et opportunités</p>
              </div>
            </div>
            <div class="form-container">
              <div v-if="page === 'login'" class="auth-box">
                <!-- Logo -->
                <div class="logo-container">
                  <i class="fas fa-briefcase logo-icon"></i>
                </div>

                <h2>Connexion à votre espace</h2>

                <div class="toggle-container">
                  <span :class="{ active: !isRecruteur }">Candidat</span>
                  <label class="switch">
                    <input
                      type="checkbox"
                      v-model="isRecruteur"
                      aria-label="Basculer le type de compte"
                    />
                    <span class="slider"></span>
                  </label>
                  <span :class="{ active: isRecruteur }">Recruteur</span>
                </div>
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
                          showPassword
                            ? 'Masquer le mot de passe'
                            : 'Afficher le mot de passe'
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
                      <i class="fas fa-exclamation-circle"></i> Mot de passe requis (min.
                      6 caractères)
                    </p>
                  </div>

                  <!-- Souvenir / lien mdp -->
                  <div class="remember-forgot">
                    <label class="remember-me">
                      <input type="checkbox" v-model="rememberMe" />
                      <span class="checkmark"></span>
                      Se souvenir de moi
                    </label>
                    <a href="#" class="forgot-password">Mot de passe oublié&nbsp;?</a>
                  </div>

                  <!-- Bouton connexion -->
                  <button :disabled="isLoading" type="submit" class="btn-submit">
                    <span v-if="isLoading" class="loading-spinner"></span>
                    <span v-else><i class="fas fa-sign-in-alt"></i> Se connecter</span>
                  </button>

                  <!-- Séparateur -->
                  <div class="or-divider"><span>OU</span></div>

                  <!-- Création compte -->
                  <div class="signup-prompt">
                    <h3>
                      Vous n'avez pas de compte
                      <span class="highlight">{{
                        isRecruteur ? "Recruteur" : "Candidat"
                      }}</span
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
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import recrutlogin from "../../assets/authentification.jpg";

const router = useRouter();
const offres = ref([]);
const loading = ref(true);
const error = ref(null);
const authModalVisible = ref(false);
const modalShowing = ref(false);
const selectedOfferId = ref(null);

// Auth variables
const email = ref("");
const password = ref("");
const isRecruteur = ref(false);
const page = ref("login");
const isLoading = ref(false);
const emailError = ref(false);
const passwordError = ref(false);
const showPassword = ref(false);
const rememberMe = ref(false);
const togglePassword = () => (showPassword.value = !showPassword.value);

// Fonction pour récupérer les offres depuis l'API
async function getOffres() {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get("/api/offres");
    offres.value = response.data;
  } catch (err) {
    error.value = "Erreur lors de la récupération des offres. Veuillez réessayer.";
    console.error("Erreur lors de la récupération des offres:", err);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  getOffres();
});

// Fonction pour tronquer le texte (pour la description)
const truncateText = (text, maxLength) => {
  if (!text) return "";
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
};

// // Fonction pour formater le salaire (si nécessaire)
// const formatSalaire = (salaire) => {
//   if (!salaire) return "Non précisé";
//   return salaire;
// };

const checkAuthentication = (offerId) => {
  const userSession =
    localStorage.getItem("userSession") || sessionStorage.getItem("userSession");

  if (!userSession) {
    // Ouvrir le modal d'authentification au lieu de rediriger
    openAuthModal(offerId);
  } else {
    // Si l'utilisateur est connecté, rediriger vers les détails de l'offre
    router.push(`/voirdetails/${offerId}`);
  }
};

// Fonction pour ouvrir le modal d'authentification avec animation
const openAuthModal = (offerId) => {
  selectedOfferId.value = offerId;
  console.log("ID de l'offre sélectionnée:", offerId);
  authModalVisible.value = true;
  setTimeout(() => {
    modalShowing.value = true;
  }, 10);
};

// Fonction pour fermer le modal d'authentification avec animation
const closeAuthModal = () => {
  modalShowing.value = false;
  setTimeout(() => {
    authModalVisible.value = false;
  }, 300);
};

// Fonction de validation du formulaire de connexion
const validateForm = () => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  emailError.value = !emailRegex.test(email.value);
  passwordError.value = password.value.length < 6;
  return !emailError.value && !passwordError.value;
};

// Fonction de connexion avec redirection forcée
const login = async () => {
  if (!validateForm()) return;
  isLoading.value = true;

  try {
    // ⚠️ CSRF-cookie obligatoire pour Sanctum
    await axios.get("/sanctum/csrf-cookie");

    // ✅ Appel login avec email & password uniquement
    const response = await axios.post(
      "/api/login",
      {
        email: email.value,
        password: password.value,
      },
      {
        withCredentials: true,
      }
    );

    // ✅ Vérification du type de compte
    if (
      (isRecruteur.value && response.data.type !== "recruteur") ||
      (!isRecruteur.value && response.data.type !== "candidat")
    ) {
      alert("❌ Vous avez sélectionné le mauvais type de compte.");
      isLoading.value = false;
      return;
    }

    // ✅ Enregistrement de la session (localStorage ou sessionStorage)
    const userData = response.data;
    const storage = rememberMe.value ? localStorage : sessionStorage;
    storage.setItem("userSession", JSON.stringify(userData));

    // ✅ Récupération de l'ID pour la redirection
    const targetId = selectedOfferId.value;

    // Fermeture du modal avant la redirection
    closeAuthModal();

    // Vérifiez si l'ID de l'offre est bien récupéré avant de rediriger
    if (targetId) {
      router.replace(`/voirdetails/${targetId}`);
    } else {
      alert("❌ Offre introuvable.");
    }
  } catch (error) {
    alert("❌ Identifiants incorrects ou utilisateur non trouvé");
    console.error("Erreur lors de la connexion:", error);
    isLoading.value = false;
  }
};

// Fonction pour aller à la page d'inscription
const goToSignup = () => {
  page.value = isRecruteur.value ? "registerRecruteur" : "registerCandidat";
  router.push(
    page.value === "registerRecruteur" ? "/registerRecruteur" : "/registerCandidat"
  );
};
</script>

<style scoped>
/* Styles pour la section des offres */
.offers-section {
  max-width: 1200px;
  margin: 50px auto;
  padding: 40px 20px;
  background-color: #f8f9fa;
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  position: relative;
}

.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #eaeaea;
}

.header-actions h2 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.6rem;
}

/* Grille d'offres */
.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.offer-card {
  background-color: #fff;
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
  min-height: 250px;
  position: relative;
  overflow: hidden;
  border: none;
  /* Remplacer le border-top par un design plus subtil */
  border-left: 4px solid #3498db;
}

.offer-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 35px rgba(52, 152, 219, 0.15);
}

/* Badge pour mettre en valeur des informations clés */
.offer-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  background-color: rgba(52, 152, 219, 0.1);
  color: #3498db;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

/* Style amélioré pour le titre de l'offre */
.offer-card-header h3 {
  font-size: 1.4rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 15px 0;
  line-height: 1.3;
  /* Ajout d'un dégradé subtil */
  background: linear-gradient(to right, #2c3e50, #4a6785);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
.offer-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.offer-card-header {
  margin-bottom: 15px;
}

.offer-card-header h3 {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 8px 0;
  line-height: 1.3;
}

.offer-card-body {
  flex-grow: 1;
}

.offer-card-body p {
  font-size: 0.95rem;
  color: #555;
}

.offer-details {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #eee;
}

.btn-see-more {
  padding: 12px 24px;
  background-color: #2980b9;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: background-color 0.3s ease;
  margin-top: 15px;
  width: 100%;
}

.btn-see-more:hover {
  background-color: #3498db;
}

/* États de chargement et d'erreur */
.loading-state,
.error-state,
.empty-state {
  padding: 40px;
  text-align: center;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.loading-state {
  position: relative;
}

.loading-state:after {
  content: "";
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #2980b9;
  border-radius: 50%;
  display: block;
  margin: 15px auto;
  animation: spin 1s linear infinite;
}

.btn-retry {
  margin-top: 15px;
  padding: 8px 20px;
  background-color: #2980b9;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-retry:hover {
  background-color: #3498db;
}

/* Styles pour le modal d'authentification */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.7);
  z-index: 1099;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.modal-overlay.showing {
  opacity: 1;
}

.modal-content {
  background-color: #fff;
  border-radius: 12px;
  width: 90%;
  max-width: 900px;
  max-height: 90vh;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  border: 1px solid #ddd;
  text-align: left;
  overflow-y: auto;
  position: relative;
  transform: scale(0.95);
  opacity: 0;
  transition: transform 0.3s ease, opacity 0.3s ease;
  padding: 0;
}

.modal-content.showing {
  transform: scale(1);
  opacity: 1;
}

.close-button {
  position: absolute;
  top: 15px;
  right: 15px;
  background: transparent;
  border: none;
  font-size: 1.8rem;
  color: #e74c3c;
  cursor: pointer;
  z-index: 2;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.8);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.close-button:hover {
  color: #c0392b;
  background-color: rgba(255, 255, 255, 1);
}

/* --- Layout général --- */
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f7f9fc;
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

/* --- Visuel gauche --- */
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

/* --- Formulaire --- */
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
  /* background: #0468BF; */
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
}

/* Bascule rôle */
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
}

.toggle-container span.active {
  color: #0468bf;
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
  background: #1f3d7a;
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

/* Souvenir & mdp oublié */
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

/* Boutons */
.btn-submit {
  width: 100%;
  padding: 0.75rem;
  background: #0468bf;
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

/* Séparateur */
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

/* Création compte */
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
  background: #0468bf;
  color: #ffffff;
}

/* Responsive */
@media (max-width: 768px) {
  .login-layout {
    flex-direction: column;
  }

  .image-container {
    height: 200px;
  }
}
</style>
