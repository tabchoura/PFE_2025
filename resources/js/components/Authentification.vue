<template>
  <div class="auth-container">
    <div class="login-layout">
      <div class="image-container">
        <img :src="recrutlogin" alt="Plateforme de recrutement" />
      </div>

      <div class="form-container">
        <!-- Boîte de connexion -------------------------------------------------->
        <div class="auth-box" v-if="page === 'login'">
          <h2>Connexion à votre espace</h2>

          <!-- Sélecteur Candidat / Recruteur ----------------------------------->
          <div class="toggle-container">
            <span :class="{ active: !isRecruteur }">Candidat</span>
            <label class="switch">
              <input type="checkbox" v-model="isRecruteur" />
              <span class="slider"></span>
            </label>
            <span :class="{ active: isRecruteur }">Recruteur</span>
          </div>

          <!-- Formulaire de connexion ----------------------------------------->
          <form @submit.prevent="login">
            <div class="input-group">
              <label for="email">Email</label>
              <input
                id="email"
                type="email"
                v-model.trim="email"
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
                  id="password"
                  :type="showPassword ? 'text' : 'password'"
                  v-model="password"
                  required
                  :class="{ error: passwordError }"
                  @input="passwordError = false"
                />
                <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                  <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'" />
                </button>
              </div>
              <p v-if="passwordError" class="error-message">Le mot de passe est requis (6 car. minimum)</p>
            </div>

            <div class="remember-forgot">
              <label class="remember-me">
                <input type="checkbox" v-model="rememberMe" />
                Se souvenir de moi
              </label>
              <a href="#" class="forgot-password">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn-submit" :disabled="isLoading">
              <span v-if="isLoading" class="loading-spinner" />
              <span v-else>Se connecter</span>
            </button>

            <!-- Lien vers page d'inscription ---------------------------------->
            <h2 class="inscrit-title">
              Vous voulez vous inscrire en tant que
              <span class="highlight">{{ isRecruteur ? 'Recruteur' : 'Candidat' }}</span> ?
            </h2>
            <button class="btn-create" @click.prevent="goToSignup">
              {{ isRecruteur ? 'Créer un compte recruteur' : 'Créer un compte candidat' }}
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Composants d'inscription ---------------------------------------------->
    <RegisterCandidat v-if="page === 'registerCandidat'" @registration-complete="handleRegistrationComplete" />
    <RegisterRecruteur v-if="page === 'registerRecruteur'" @registration-complete="handleRegistrationComplete" />
  </div>
</template>

<script>
import recrutlogin from "../../assets/authentification.jpg";
import RegisterCandidat from "@/components/RegisterCandidat.vue";
import RegisterRecruteur from "@/components/RegisterRecruteur.vue";
import api from "@/axios";

export default {
  name: "Authentification",
  components: { RegisterCandidat, RegisterRecruteur },
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
    /* ----------------------------------------------------------------------- */
    validateForm() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      this.emailError = !emailRegex.test(this.email);
      this.passwordError = this.password.length < 6;
      return !this.emailError && !this.passwordError;
    },

    /* ------------------------- Connexion utilisateur ----------------------- */
    async login() {
      if (!this.validateForm()) return;
      this.isLoading = true;

      try {
        /*  IMPORTANT : Mode TOKEN → inutile d'appeler /sanctum/csrf-cookie  */
        const { data } = await api.post("/api/login", {
          email: this.email,
          password: this.password,
          // On indique le type uniquement pour la redirection front
        });

        // data = { user, token, type }
        const storage = this.rememberMe ? localStorage : sessionStorage;
        storage.setItem("userSession", JSON.stringify(data));

        // Redirection selon le rôle
        this.$router.push(data.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat");
      } catch (err) {
        alert("❌ Identifiants incorrects ou utilisateur non trouvé");
        console.error("Erreur lors de la connexion:", err);
      } finally {
        this.isLoading = false;
      }
    },

    /* --------------- Aller sur la page d'inscription adéquate ------------- */
    goToSignup() {
      this.page = this.isRecruteur ? "registerRecruteur" : "registerCandidat";
      this.$router.push(this.page === "registerRecruteur" ? "/register-recruteur" : "/register-candidat");
    },

    /* -------- Quand l'inscription (enfant) est terminée, on se connecte ---- */
    handleRegistrationComplete(userData) {
      const storage = this.rememberMe ? localStorage : sessionStorage;
      storage.setItem("userSession", JSON.stringify(userData));
      this.$router.push(userData.type === "recruteur" ? "/CompteRecruteur" : "/CompteCandidat");
    },
  },

  /* --------------- Si une session existe, on réutilise l'utilisateur ------- */
  mounted() {
    const session = localStorage.getItem("userSession") || sessionStorage.getItem("userSession");
    if (session) {
      const userSession = JSON.parse(session);
      this.email = userSession.user?.email || "";
      this.isRecruteur = userSession.type === "recruteur";
      this.$router.push(this.isRecruteur ? "/CompteRecruteur" : "/CompteCandidat");
    }
  },
};
</script>

<style scoped>
/***** Conteneur principal ***************************************************/
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

.login-layout {
  display: flex;
  width: 100%;
  max-width: 900px;
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
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/***** Boîte de formulaire ***************************************************/
.auth-box {
  width: 100%;
  background: #fff;
  border-radius: 8px;
  padding: 25px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

.auth-box h2 {
  text-align: center;
  font-size: 1.6rem;
  margin-bottom: 15px;
  font-weight: 600;
  color: #333;
}

/***** Toggle candidat / recruteur *******************************************/
.toggle-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-bottom: 15px;
  background: #f8f9fa;
  border-radius: 25px;
  padding: 4px;
}

.switch { position: relative; width: 45px; height: 23px; }
.switch input { opacity: 0; width: 0; height: 0; }

.slider {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: #ccc;
  border-radius: 23px;
  transition: background 0.3s;
}
.slider:before {
  content: "";
  position: absolute;
  height: 17px; width: 17px;
  left: 3px; bottom: 3px;
  background: #fff;
  border-radius: 50%;
  transition: transform 0.3s;
}
input:checked + .slider { background: #2980b9; }
input:checked + .slider:before { transform: translateX(22px); }

.toggle-container span { font-size: 0.9rem; color: #777; font-weight: 500; }
.toggle-container span.active { color: #2980b9; font-weight: 600; }

/***** Champs de saisie ******************************************************/
.input-group { margin-bottom: 12px; }
.input-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #333; }

.input-with-icon { position: relative; }
.toggle-password { position: absolute; top: 50%; right: 8px; transform: translateY(-50%); background: none; border: none; cursor: pointer; }

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
input:focus { outline: none; border-color: #2980b9; }

.error-message { color: #e74c3c; font-size: 0.85rem; margin-top: 4px; }

/***** Boutons ***************************************************************/
.btn-submit,
.btn-create {
  width: 100%;
  padding: 12px;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.3s;
}
.btn-submit { background: #2980b9; }
.btn-submit:hover:not(:disabled) { background: #21618c; }
.btn-submit:disabled { background: #ccc; }

.btn-create { background: #29a4b9; margin-top: 10px; }
.btn-create:hover { background: #2491a4; }

/***** Autres ****************************************************************/
.remember-forgot { display: flex; justify-content: space-between; margin-bottom: 20px; }
.forgot-password { font-size: 0.85rem; color: #2980b9; }
.forgot-password:hover { text-decoration: underline; }

.inscrit-title { text-align: center; font-size: 1.1rem; margin: 12px 0; }
.highlight { color: #2980b9; font-weight: bold; }
</style>
