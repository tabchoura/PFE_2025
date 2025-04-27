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
          <p class="description"><strong>Description :</strong> {{ truncateText(offer.description, 100) }}</p>
          <div class="offer-details">
            <p class="salaire"><strong>Salaire :</strong> {{ formatSalaire(offer.salaire) }}</p>
               
          </div>
        </div>


        <div>
     </div>

        <!-- Bouton Voir plus pour afficher le modal d'authentification -->
        <button class="btn-see-more" @click="checkAuthentication(offer.id)">Voir plus</button>
      </div>
    </div>
  </div>

  <!-- Modal pour l'authentification -->
  <Teleport to="body">
    <div v-if="authModalVisible" class="modal-overlay" :class="{ 'showing': modalShowing }" @click.self="closeAuthModal">
      <div class="modal-content" :class="{ 'showing': modalShowing }">
        <button class="close-button" @click="closeAuthModal">×</button>
        
        <!-- Login Form -->
        <div class="auth-container">
          <div class="login-layout">
            <div class="image-container">
              <img :src="recrutlogin" alt="Plateforme de recrutement" />
            </div>

            <div class="form-container">
              <div class="auth-box" v-if="page === 'login'">
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
                    <input
                      type="email"
                      v-model="email"
                      id="email"
                      required
                      :class="{ error: emailError }"
                      @input="emailError = false"
                    />
                    <p v-if="emailError" class="error-message">L'email est invalide</p>
                  </div>

                  <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-with-icon">
                      <input
                        :type="showPassword ? 'text' : 'password'"
                        v-model="password"
                        id="password"
                        required
                        :class="{ error: passwordError }"
                        @input="passwordError = false"
                      />
                      <button
                        type="button"
                        class="toggle-password"
                        @click="showPassword = !showPassword"
                      >
                        <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                      </button>
                    </div>
                    <p v-if="passwordError" class="error-message">Le mot de passe est requis</p>
                  </div>

                  <div class="remember-forgot">
                    <label class="remember-me">
                      <input type="checkbox" v-model="rememberMe" />
                      Se souvenir de moi
                    </label>
                    <a href="#" class="forgot-password">Mot de passe oublié?</a>
                  </div>

                  <button type="submit" class="btn-submit" :disabled="isLoading">
                    <span v-if="isLoading" class="loading-spinner"></span>
                    <span v-else>Se connecter</span>
                  </button>

                  <h2 class="inscrit-title">
                    Vous voulez vous inscrire en tant que
                    <span class="highlight">{{ isRecruteur ? "Recruteur" : "Candidat" }}</span> ?
                  </h2>
                  <button class="btn-create" @click.prevent="goToSignup">
                    {{ isRecruteur ? "Créer un compte recruteur" : "Créer un compte candidat" }}
                  </button>
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import recrutlogin from "../../assets/authentification.jpg";

const router = useRouter();
const offres = ref([]);
const loading = ref(true);
const error = ref(null);
const authModalVisible = ref(false);
const modalShowing = ref(false);
const selectedOfferId = ref(null);

// Auth variables
const email = ref('');
const password = ref('');
const isRecruteur = ref(false);
const page = ref('login');
const isLoading = ref(false);
const emailError = ref(false);
const passwordError = ref(false);
const showPassword = ref(false);
const rememberMe = ref(false);

// Fonction pour récupérer les offres depuis l'API
const getOffres = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get('/api/offres');
    offres.value = response.data;
  } catch (err) {
    error.value = 'Erreur lors de la récupération des offres. Veuillez réessayer.';
    console.error('Erreur lors de la récupération des offres:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  getOffres();
});

// Fonction pour tronquer le texte (pour la description)
const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

// Fonction pour formater le salaire (si nécessaire)
const formatSalaire = (salaire) => {
  if (!salaire) return 'Non précisé';
  return salaire;
};

const checkAuthentication = (offerId) => {
  const userSession = localStorage.getItem('userSession') || sessionStorage.getItem('userSession');
  
  if (!userSession) {
    // Ouvrir le modal d'authentification au lieu de rediriger
    openAuthModal(offerId);
  } else {
    // Si l'utilisateur est connecté, rediriger vers les détails de l'offre
    router.push(`/voirdetails/${offerId}`);
  }
}

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
    const response = await axios.post("/api/login", {
      email: email.value,
      password: password.value
    }, {
      withCredentials: true
    });

    // ✅ Vérification du type de compte
    if ((isRecruteur.value && response.data.type !== 'recruteur') ||
        (!isRecruteur.value && response.data.type !== 'candidat')) {
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
  router.push(page.value === "registerRecruteur" ? "/registerRecruteur" : "/registerCandidat");
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

/* Cartes d'offres */
.offer-card {
  background-color: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
  min-height: 220px;
}

.offer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.offer-card-header h3 {
  font-size: 1.3rem;
  font-weight: bold;
  color: #2c3e50;
  margin-top: 0;
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
.loading-state, .error-state, .empty-state {
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

/* Styles de base pour l'authentification */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  margin-top: 0;
  text-align: left;
  min-height: unset;
}

/* Layout principal */
.login-layout {
  display: flex;
  max-width: 900px;
  width: 100%;
  margin: 0 auto;
  gap: 20px;
}

.image-container,
.form-container {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.image-container img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Boîte de formulaire */
.auth-box {
  background: #fff;
  border-radius: 8px;
  width: 100%;
  padding: 25px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

.auth-box h2 {
  font-size: 1.6rem;
  margin-bottom: 15px;
  color: #333;
  font-weight: 600;
  text-align: center;
}

/* Toggle Candidat / Recruteur */
.toggle-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 20px;
  background-color: #f8f9fa;
  border-radius: 25px;
  padding: 6px 10px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 45px;
  height: 23px;
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
  background-color: #ccc;
  transition: 0.3s;
  border-radius: 23px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 17px;
  width: 17px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.3s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #2980b9;
}

input:checked + .slider:before {
  transform: translateX(22px);
}

.toggle-container span {
  font-size: 0.9rem;
  font-weight: 500;
  color: #777;
  transition: color 0.3s;
  padding: 4px 8px;
}

.toggle-container span.active {
  color: #2980b9;
  font-weight: 600;
}

/* Formulaire */
.input-group {
  margin-bottom: 15px;
  text-align: left;
}

.input-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 6px;
  color: #333;
}

.input-with-icon {
  position: relative;
}

input[type="email"],
input[type="password"],
input[type="text"] {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s, box-shadow 0.3s;
  box-sizing: border-box;
}

input[type="email"]:focus,
input[type="password"]:focus,
input[type="text"]:focus {
  outline: none;
  border-color: #2980b9;
  box-shadow: 0 0 0 2px rgba(41, 128, 185, 0.2);
}

.input-with-icon input {
  padding-right: 40px;
}

.error-message {
  margin-top: 4px;
  color: #e74c3c;
  font-size: 0.85rem;
}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #777;
}

.toggle-password:hover {
  color: #333;
}

.remember-forgot {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 15px 0 20px;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
  color: #555;
}

.forgot-password {
  font-size: 0.9rem;
  color: #2980b9;
  text-decoration: none;
}

.forgot-password:hover {
  text-decoration: underline;
}

.btn-submit {
  width: 100%;
  padding: 12px;  
  background-color: #2980b9;
  color: #fff;
  font-size: 1.1rem;
  font-weight: 500;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
  position: relative;
}

.btn-submit:disabled {
  background-color: #b3b3b3;
  cursor: not-allowed;
}

.btn-submit:hover:not(:disabled) {
  background-color: #21618c;
}

.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
  vertical-align: middle;
}

.btn-create {
  width: 100%;
  padding: 12px;
  background-color: #29a4b9;
  color: #fff;
  font-size: 1.1rem;
  font-weight: 500;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 10px;
}

.btn-create:hover {
  background-color: #2491a4;
}

.inscrit-title {
  text-align: center;
  font-size: 1.1rem;
  margin: 20px 0 15px;
  color: #555;
}

.highlight {
  font-weight: bold;
  color: #2980b9;
}

/* Animation */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Media Queries pour le responsive */
@media (max-width: 768px) {
  .login-layout {
    flex-direction: column;
  }
  
  .image-container {
    display: none; /* Masquer l'image sur mobile */
  }
  
  .modal-content {
    width: 95%;
  }
  
  .offers-grid {
    grid-template-columns: 1fr;
  }
  
  .header-actions {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .auth-box {
    padding: 20px 15px;
  }
  
  .remember-forgot {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .forgot-password {
    margin-left: 0;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .offers-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style> 
