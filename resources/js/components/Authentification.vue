<template>
  <div class="auth-container">
    <div class="login-layout">
      <div class="image-container">
        <img :src="recrutlogin" alt="Plateforme de recrutement" class="img" />
      </div>

      <!-- Formulaire à droite -->
      <div class="form-container">
        <div class="auth-box login-box" v-if="page === 'login'">
          <h2 class="auth-title">Connexion à votre espace</h2>

          <!-- Toggle Candidat / Recruteur -->
          <div class="toggle-container">
            <span :class="{ active: !isRecruteur }">Candidat</span>
            <label class="switch">
              <input type="checkbox" v-model="isRecruteur" />
              <span class="slider round"></span>
            </label>
            <span :class="{ active: isRecruteur }">Recruteur</span>
          </div>

          <form @submit.prevent="login" class="auth-form">
            <div class="input-group">
              <label for="email">Email</label>
              <div class="input-with-icon">
                <i class="fa fa-envelope input-icon"></i>
                <input
                  type="email"
                  v-model="email"
                  id="email"
                  required
                  :class="{ 'error-input': emailError }"
                  @input="emailError = false"
                />
              </div>
              <p v-if="emailError" class="error-message">L'email est invalide</p>
            </div>

            <div class="input-group">
              <label for="password">Mot de passe</label>
              <div class="input-with-icon password-input-container">
                <i class="fa fa-lock input-icon"></i>
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model="password"
                  id="password"
                  required
                  :class="{ 'error-input': passwordError }"
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
              <span class="choix">{{ isRecruteur ? "Recruteur" : "Candidat" }}</span> ?
            </h2>
            <button class="btn-create" @click.prevent="goToSignup">
              {{ isRecruteur ? "Créer un compte recruteur" : "Créer un compte candidat" }}
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Formulaires d'inscription -->
    <div v-if="page === 'registerCandidat'">
      <RegisterCandidat @registration-complete="handleRegistrationComplete" />
    </div>
    <div v-if="page === 'registerRecruteur'">
      <RegisterRecruteur @registration-complete="handleRegistrationComplete" />
    </div>
  </div>
</template>

<script>
import recrutlogin from "../../assets/recrutlogin.jpg";
import RegisterCandidat from "../components/RegisterCandidat.vue";
import RegisterRecruteur from "../components/RegisterRecruteur.vue";

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
    login() {
      if (!this.validateForm()) return;

      this.isLoading = true;

      setTimeout(() => {
        const userType = this.isRecruteur ? "recruteur" : "candidat";
        const userData = {
          email: this.email,
          type: userType,
          lastLogin: new Date().toISOString(),
        };

        const storage = this.rememberMe ? localStorage : sessionStorage;
        storage.setItem("userSession", JSON.stringify(userData));

        this.isLoading = false;

        // Redirection vers la nouvelle URL
        if (this.isRecruteur) {
          this.$router.push("/CompteRecruteur");
        } else {
          this.$router.push("/CompteCandidat");
        }
      }, 1500);
    },
    goToSignup() {
      this.page = this.isRecruteur ? "registerRecruteur" : "registerCandidat";
    },
    handleRegistrationComplete(userData) {
      console.log("Inscription terminée :", userData);
      if (userData.type === "recruteur") {
        this.$router.push("/CompteRecruteur");
      } else {
        this.$router.push("/CompteCandidat");
      }
    },
  },
  mounted() {
    const session =
      localStorage.getItem("userSession") || sessionStorage.getItem("userSession");

    if (session) {
      const user = JSON.parse(session);
      this.email = user.email;
      this.isRecruteur = user.type === "recruteur";
      if (user.type === "recruteur") {
        this.$router.push("/CompteRecruteur");
      } else {
        this.$router.push("/CompteCandidat");
      }
    }
  },
};
</script>

<style>
/* ===== Auth Container ===== */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f4f6f9;
  padding: 20px;
  margin-top: 80px;
  box-sizing: border-box;
  text-align: left;

  flex-direction: column;
}

/* ===== Login Layout (Horizontal) ===== */
.login-layout {
  display: flex;
  flex-direction: row;
  justify-content: center;
  gap: 30px;
  max-width: 1200px;
  margin: 20px auto;
  width: 100%;
  padding: 20px;
}

.image-container {
  flex: 1;
  display: flex;
  justify-content: flex-end;
  align-items: center;
}

.form-container {
  flex: 1;
  max-width: 500px;
}

/* ===== Auth Box Styles ===== */
.auth-box {
  background: #fff;
  border-radius: 12px;
  width: 100%;
  padding: 40px;
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
  border: 1px solid #eaedf3;
  text-align: center;
  transition: all 0.3s ease;
}

.auth-box:hover {
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
  transform: translateY(-5px);
}

.auth-title {
  font-size: 2rem;
  margin-bottom: 25px;
  color: #333;
  font-weight: 600;
}

/* ===== Toggle Candidat / Recruteur ===== */
.toggle-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 25px;
  padding: 8px;
  background-color: #f8f9fa;
  border-radius: 30px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 30px;
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
  transition: 0.4s;
  border-radius: 25px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 22px;
  width: 22px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

input:checked + .slider {
  background-color: #2980b9;
}

input:checked + .slider:before {
  transform: translateX(30px);
}

.toggle-container span {
  font-weight: 600;
  color: #7a8599;
  transition: color 0.3s ease;
}

.toggle-container span.active {
  color: #2980b9;
  font-weight: 700;
}

/* ===== Form Styles ===== */
.input-group {
  margin-bottom: 20px;
  text-align: left;
}

.input-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 8px;
  color: #34495e;
  font-size: 0.95rem;
}

.input-with-icon {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #a0aec0;
}

.input-group input {
  width: 100%;
  padding: 12px 12px 12px 40px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  transition: 0.3s;
  color: #333;
  background-color: #fafafa;
}

.input-group input:focus {
  border-color: #2980b9;
  outline: none;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(41, 128, 185, 0.2);
}

.password-input-container {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #a0aec0;
  padding: 5px;
}

.toggle-password:hover {
  color: #2980b9;
}

.error-input {
  border-color: #e74c3c !important;
}

.error-message {
  color: #e74c3c;
  font-size: 0.9rem;
  margin-top: 5px;
}

/* ===== Remember me and forgot password ===== */
.remember-forgot {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.remember-me {
  display: flex;
  align-items: center;
  font-size: 0.9rem;
  color: #4a5568;
  cursor: pointer;
}

.remember-me input {
  margin-right: 8px;
}

.forgot-password {
  font-size: 0.9rem;
  color: #2980b9;
  text-decoration: none;
}

.forgot-password:hover {
  text-decoration: underline;
}

/* Button styles */
.btn-submit {
  width: 100%;
  padding: 14px;
  background-color: #2980b9;
  border: none;
  color: white;
  font-size: 1.1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  font-weight: 600;
}

.btn-submit:hover {
  background-color: #1f6690;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(33, 107, 165, 0.3);
}

.btn-submit:active {
  transform: translateY(0);
}

.loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.inscrit-title {
  font-size: 1.3rem;
  margin: 24px 0 15px 0;
  font-weight: 600;
  white-space: normal;
  overflow-wrap: break-word;
  color: #4a5568;
}

.choix {
  color: #2980b9;
  font-weight: 700;
}

.btn-create {
  width: 100%;
  padding: 14px;
  background-color: transparent;
  border: 2px solid #2980b9;
  color: #2980b9;
  font-size: 1.1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
}

.btn-create:hover {
  background-color: #f0f7fc;
  color: #1f6690;
  border-color: #1f6690;
}

.img {
  max-width: 100%;
  height: auto;
  object-fit: contain;
  border-radius: 15px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.img:hover {
  transform: scale(1.03);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

/* ===== Responsive Styles ===== */
@media (max-width: 768px) {
  .login-layout {
    flex-direction: column;
    gap: 20px;
  }

  .auth-container {
    padding: 15px;
  }

  .auth-box {
    padding: 25px;
    width: 100%;
    max-width: 100%;
  }

  .auth-title,
  .inscrit-title {
    font-size: 1.5rem;
  }

  .btn-submit,
  .btn-create {
    font-size: 1rem;
    padding: 12px;
  }

  .image-container {
    justify-content: center;
    margin-bottom: 20px;
  }

  .img {
    max-width: 250px;
  }
}

@media (max-width: 480px) {
  .remember-forgot {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}
</style>
