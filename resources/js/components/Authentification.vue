<template>
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

    <!-- Composants d'inscription -->
    <div v-if="page === 'registerCandidat'">
      <RegisterCandidat @registration-complete="handleRegistrationComplete" />
    </div>
    <div v-if="page === 'registerRecruteur'">
      <RegisterRecruteur @registration-complete="handleRegistrationComplete" />
    </div>
  </div>
</template>

<script>
import recrutlogin from "../../assets/authentification.jpg";
import RegisterCandidat from "../components/RegisterCandidat.vue";
import RegisterRecruteur from "../components/RegisterRecruteur.vue";
import api from '@/axios';

export default {
  components: {
    RegisterCandidat,
    RegisterRecruteur,
  },
  data() {
    return {
      recrutlogin,
      email: "",
      password: "",
      isRecruteur: false,
      page: "login",
      isLoading: false,
      emailError: false,
      passwordError: false,
      showPassword: false,
      rememberMe: false,
    };
  },
  methods: {
    validateForm() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      this.emailError = !emailRegex.test(this.email);
      this.passwordError = this.password.length < 6;
      return !this.emailError && !this.passwordError;
    },
    async login() {
  if (!this.validateForm()) return;
  this.isLoading = true;

  try {
    // ⚠️ CSRF-cookie obligatoire pour Sanctum
    await api.get("/sanctum/csrf-cookie");

    // ✅ Appel login avec email & password uniquement
    const response = await api.post("/api/login", {
      email: this.email,
      password: this.password
    }, {
      withCredentials: true
    });

    // ✅ Enregistrement de la session (localStorage ou sessionStorage)
    const userData = response.data;
    const storage = this.rememberMe ? localStorage : sessionStorage;
    storage.setItem("userSession", JSON.stringify(userData));

    // ✅ Redirection selon le rôle
    this.$router.push(
      userData.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat"
    );
  } catch (error) {
    alert("❌ Identifiants incorrects ou utilisateur non trouvé");
    console.error("Erreur lors de la connexion:", error);
  } finally {
    this.isLoading = false;
  }
}
,
    goToSignup() {
      this.page = this.isRecruteur ? "registerRecruteur" : "registerCandidat";
      this.$router.push(this.page === "registerRecruteur" ? "/registerRecruteur" : "/registerCandidat");
    },
    handleRegistrationComplete(userData) {
      const storage = this.rememberMe ? localStorage : sessionStorage;
      storage.setItem("userSession", JSON.stringify(userData));
      this.$router.push(userData.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat");
    }
  },
  mounted() {
    const session = localStorage.getItem("userSession") || sessionStorage.getItem("userSession");
    if (session && document.referrer === "") {
      const user = JSON.parse(session);
      this.email = user.email;
      this.isRecruteur = user.type === "recruteur";
      this.$router.push(user.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat");
    }
  }
};
</script>


<style scoped>
/* Styles de base */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f4f6f9;
  padding: 20px;
  margin-top: 60px;
  text-align: left;
}

/* Layout principal */
.login-layout {
  display: flex;
  max-width: 900px;
  width: 100%;
  margin: 20px auto;
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
  margin-bottom: 15px;
  background-color: #f8f9fa;
  border-radius: 25px;
  padding: 4px;
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
}

.toggle-container span.active {
  color: #2980b9;
  font-weight: 600;
}

/* Formulaire */
.input-group {
  margin-bottom: 12px;
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

.input-with-icon i {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  left: 10px;
  color: #ccc;
}

input[type="email"],
input[type="password"],
input[type="text"] {
  width: calc(100% - 40px);
  padding: 10px 12px 10px 30px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

input[type="email"]:focus,
input[type="password"]:focus,
input[type="text"]:focus {
  outline: none;
  border-color: #2980b9;
}

.error-message {
  margin-top: 4px;
  color: #e74c3c;
  font-size: 0.85rem;
}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 8px;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
}

.remember-forgot {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.forgot-password {
  font-size: 0.85rem;
  color: #2980b9;
  cursor: pointer;
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
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-submit:disabled {
  background-color: #ccc;
}

.btn-submit:hover:not(:disabled) {
  background-color: #21618c;
}

.btn-create {
  width: 100%;
  padding: 12px;
  background-color:#29a4b9;
  color: #fff;
  font-size: 1.1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-create:hover {
  background-color: #2491a4;
}

.inscrit-title {
  text-align: center;
  font-size: 1.1rem;
  margin: 12px 0;
}

.highlight {
  font-weight: bold;
  color: #2980b9;
}
</style>
