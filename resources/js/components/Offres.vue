<template>
  <div class="offers-section">
    <!-- Titre de la section des offres -->
    <div class="header-actions">
      <h2>Liste des offres</h2>
      <div class="search">
        <select v-model="selectedTitle" class="filter-select">
          <option value="">Tous les titres</option>
          <option v-for="t in titles" :key="t" :value="t">{{ t }}</option>
        </select>
      </div>
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
    <div v-else-if="filteredOffers.length === 0" class="empty-state">
      <p>Aucune offre disponible pour le moment</p>
    </div>

    <!-- Affichage des offres -->
    <div v-else class="offers-grid">
      <div class="offer-card" v-for="offer in filteredOffers" :key="offer.id">
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

        <div class="auth-container">
          <div class="login-layout">
            <!-- VISUEL GAUCHE -->
            <div class="image-container">
              <div class="image-overlay">
                <h1 class="platform-name">Jobgo</h1>
                <p class="platform-slogan">Connectez talents et opportunités</p>
              </div>
            </div>
            <div class="form-container">
              <div v-if="page === 'login'" class="auth-box">
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

                  <div class="remember-forgot">
                    <label class="remember-me">
                      <input type="checkbox" v-model="rememberMe" />
                      <span class="checkmark"></span>
                      Se souvenir de moi
                    </label>
                    <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                  </div>

                  <button :disabled="isLoading" type="submit" class="btn-submit">
                    <span v-if="isLoading" class="loading-spinner"></span>
                    <span v-else><i class="fas fa-sign-in-alt"></i> Se connecter</span>
                  </button>

                  <div class="or-divider"><span>OU</span></div>

                  <div class="signup-prompt">
                    <h3>
                      Vous n'avez pas de compte
                      <span class="highlight">{{
                        isRecruteur ? "Recruteur" : "Candidat"
                      }}</span>
                       ?
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
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import search from "../../assets/search.png";

const router = useRouter();
const offres = ref([]);
const titles = ref([]);
const loading = ref(true);
const error = ref(null);
const selectedTitle = ref("");

// Auth modal state
const authModalVisible = ref(false);
const modalShowing = ref(false);
const selectedOfferId = ref(null);

// Auth form data
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

// Fetch offers and titles
const getOffres = async () => {
  loading.value = true;
  error.value = null;
  try {
    const [offersRes, titlesRes] = await Promise.all([
      axios.get("/api/offres"),
      axios.get("/api/offres/titres"),
    ]);
    offres.value = offersRes.data;
    titles.value = titlesRes.data;
  } catch (err) {
    error.value = "Erreur lors de la récupération des offres. Veuillez réessayer.";
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(getOffres);

// Computed filtered list
// Variante « one-liner »
const filteredOffers = computed(() => {
  if (!selectedTitle.value) {
    // pas de filtre → on affiche toutes les offres
    return offres.value;
  }
  // sinon on ne garde que celles qui ont le titre sélectionné
  return offres.value.filter((o) => o.titre === selectedTitle.value);
});

// Utility
const truncateText = (text, maxLength) =>
  text && text.length > maxLength ? text.substring(0, maxLength) + "..." : text;

// Authentication flow
const checkAuthentication = (offerId) => {
  const userSession =
    localStorage.getItem("userSession") || sessionStorage.getItem("userSession");
  if (!userSession) {
    openAuthModal(offerId);
  } else {
    router.push(`/voirdetails/${offerId}`);
  }
};
const openAuthModal = (offerId) => {
  selectedOfferId.value = offerId;
  authModalVisible.value = true;
  setTimeout(() => (modalShowing.value = true), 10);
};
const closeAuthModal = () => {
  modalShowing.value = false;
  setTimeout(() => (authModalVisible.value = false), 300);
};
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
    await axios.get("/sanctum/csrf-cookie");
    const response = await axios.post(
      "/api/login",
      { email: email.value, password: password.value },
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
    const storage = rememberMe.value ? localStorage : sessionStorage;
    storage.setItem("userSession", JSON.stringify(response.data));
    const targetId = selectedOfferId.value;
    closeAuthModal();
    if (targetId) router.replace(`/voirdetails/${targetId}`);
    else alert("❌ Offre introuvable.");
  } catch (err) {
    alert("❌ Identifiants incorrects ou utilisateur non trouvé");
    console.error(err);
    isLoading.value = false;
  }
};
const goToSignup = () => {
  page.value = isRecruteur.value ? "registerRecruteur" : "registerCandidat";
  router.push(
    page.value === "registerRecruteur" ? "/registerRecruteur" : "/registerCandidat"
  );
};
</script>
<style scoped>
/* Conteneur général */
body {
  background: linear-gradient(135deg, #e0eafc, #cfdef3); /* Dégradé léger */
  font-family: "Inter", sans-serif;
  margin: 0;
  padding: 0;
}
.offers-section {
  padding: 2rem;

  border-radius: 3%;
  background-color: #e7f1f8;
}
.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}
/* Effet d'ombre lors du survol de la section */
.offers-section:hover {
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
}

.header-actions h2 {
  font-size: 1.75rem;
  color: #3d63ea;

  margin: 0;
  font-weight: bold;
}

.search {
  display: flex;
  align-items: center;
}

.filter-select {
  appearance: none;
  padding: 0.75rem 1.5rem;
  border: 2px solid #0468bf;
  border-radius: 2rem;
  background: #fff url("../../assets/arrow-down.svg") no-repeat calc(100% - 1rem) center;
  background-size: 1rem;
  font-size: 1rem;
  color: #2c3e50;
  min-width: 200px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
  transition: border-color 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

.filter-select:focus {
  outline: none;
  border-color: #024a8c;
  box-shadow: 0 4px 16px rgba(2, 74, 140, 0.2);
}

/* Grille d'offres */
.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
  transition: grid-template-columns 0.3s ease;
}

/* Carte d'offre */
.offer-card {
  border-left: 3px solid #3b82f6;

  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 1.5rem;
  cursor: pointer;
}

/* Ajout d'un effet sur le survol de la carte d'offre */
.offer-card:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

/* En-tête de la carte */
.offer-card-header h3 {
  font-size: 1.6rem;
  color: #03315c;
  margin-bottom: 1rem;
  font-weight: 600;
}

/* Description et détails */
.description {
  font-size: 0.95rem;
  color: #555;
  line-height: 1.5;
  margin-bottom: 1rem;
}

.salaire {
  color: #1d4ed8;
  font-weight: 600;
  margin-top: 0.5rem;
}

/* Bouton Voir plus */
.btn-see-more {
  background: linear-gradient(135deg, #0468bf, #0284c7);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  text-align: center;
  box-shadow: 0 4px 12px rgba(2, 74, 140, 0.3);
  transition: background 0.3s, transform 0.2s;
  cursor: pointer;
  margin-top: 1rem;
  display: block;
  width: 100%;
}

.btn-see-more:hover {
  background: linear-gradient(135deg, #1d4d7a, #046297);
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(2, 74, 140, 0.4);
}

/* Modal Authentification avec effet visuel amélioré */
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
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.modal-overlay.showing {
  opacity: 1;
  transform: translateY(0); /* Effet d'animation pour l'ouverture */
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

/* Auth Modal Close Button */
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

/* Authentification - Transition modale améliorée */
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(
    135deg,
    #1f3d7a,
    #3c64c8
  ); /* Dégradé pour l'authentification */
  font-family: "Inter", sans-serif;
}

/* Responsive: Ajustement pour les petits écrans */
@media (max-width: 768px) {
  .offers-section {
    padding: 2rem;
  }

  .offers-grid {
    grid-template-columns: repeat(
      auto-fill,
      minmax(200px, 1fr)
    ); /* Grille plus compacte */
  }

  .offer-card {
    padding: 1rem;
  }

  .modal-content {
    width: 95%;
  }

  .login-layout {
    flex-direction: column;
  }

  .image-container {
    height: 200px;
  }
}
</style>
