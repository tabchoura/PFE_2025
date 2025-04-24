<template>
  <!-- Formulaire avec meilleur affichage -->
  <div class="login-container">
    <form @submit.prevent="login">
      <div class="form-header">
        <h1>Connexion Candidat</h1>
        <p class="form-subtitle">Accédez à votre espace personnel</p>
      </div>

      <div class="form-group">
        <label for="email">Email :</label>
        <div class="input-wrapper">
          <span class="input-icon">📧</span>
          <input 
            type="email" 
            id="email" 
            v-model="email" 
            required 
            placeholder="votre@email.com"
            :class="{ 'input-error': errors.email }"
          />
        </div>
        <p v-if="errors.email" class="error-message">{{ errors.email }}</p>
      </div>

      <div class="form-group">
        <label for="password">Mot de passe :</label>
        <div class="input-wrapper password-input-container">
          <span class="input-icon">🔑</span>
          <input
            :type="showPassword ? 'text' : 'password'"
            v-model="password"
            id="password"
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
            <span aria-hidden="true">{{ showPassword ? '🔒' : '👁️' }}</span>
          </button>
        </div>
        <p v-if="errors.password" class="error-message">{{ errors.password }}</p>
      </div>

      <div class="remember-me">
        <label class="checkbox-container">
          <input type="checkbox" v-model="rememberMe">
          <span class="checkmark"></span>
          Se souvenir de moi
        </label>
      </div>

      <button type="submit" :disabled="isLoading" class="login-button">
        <span v-if="isLoading" class="loading-spinner"></span>
        <span v-else>Se connecter</span>
      </button>
    </form>

    <div class="form-footer">
      <a href="#" @click.prevent="resetPassword" class="forgot-password">Mot de passe oublié ?</a>
      <p class="no-account">Pas encore de compte ? <a href="#" @click.prevent="goToRegister">S'inscrire</a></p>
    </div>

    <div v-if="error" class="error-notification">
      <span class="error-icon">⚠️</span>
      {{ error }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: "",
      password: "",
      rememberMe: false,
      error: "",
      errors: {
        email: "",
        password: ""
      },
      isLoading: false,
      showPassword: false,
    };
  },
  
  methods: {
    validateForm() {
      let isValid = true;
      this.errors = {
        email: "",
        password: ""
      };
      
      // Validation email
      if (!this.email) {
        this.errors.email = "L'email est requis";
        isValid = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
        this.errors.email = "Format d'email invalide";
        isValid = false;
      }
      
      // Validation mot de passe
      if (!this.password) {
        this.errors.password = "Le mot de passe est requis";
        isValid = false;
      }
      
      return isValid;
    },

    async login() {
      if (!this.validateForm()) return;
      
      this.isLoading = true;
      this.error = "";

      try {
        // CSRF-cookie pour Sanctum
        await axios.get("/sanctum/csrf-cookie");

        // Appel API pour la connexion
        const response = await axios.post("/api/login", {
          email: this.email,
          password: this.password,
          remember: this.rememberMe
        }, {
          withCredentials: true,
        });

        // Enregistrer la session
        const userData = response.data;
        const storage = this.rememberMe ? localStorage : sessionStorage;
        storage.setItem("userSession", JSON.stringify(userData));

        // Afficher succès
        this.showSuccessMessage();
        
        // Redirection après un court délai pour voir le message de succès
        setTimeout(() => {
          this.$router.push('/monprofile');
        }, 1000);
        
      } catch (error) {
        if (error.response && error.response.status === 401) {
          this.error = "Identifiants incorrects ou utilisateur non trouvé";
        } else if (error.response && error.response.data.errors) {
          // Erreurs de validation du backend
          const serverErrors = error.response.data.errors;
          if (serverErrors.email) this.errors.email = serverErrors.email[0];
          if (serverErrors.password) this.errors.password = serverErrors.password[0];
        } else {
          this.error = "Une erreur est survenue. Veuillez réessayer.";
        }
        console.error("Erreur lors de la connexion:", error);
      } finally {
        this.isLoading = false;
      }
    },

    resetPassword() {
      this.$router.push('/reset-password');
    },
    
    goToRegister() {
      this.$router.push('/register');
    },

    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    
    showSuccessMessage() {
      // Cette méthode pourrait être utilisée pour afficher un message de succès
      // Vous pourriez implémenter un système de notifications toast ici
    }
  },
};
</script>

<style scoped>
/* Styles généraux */
.login-container {
  max-width: 450px;
  margin: 40px auto;
  padding: 20px;
}

.form-header {
  text-align: center;
  margin-bottom: 30px;
}

h1 {
  text-align: center;
  color: #1E2A4B;
  margin-bottom: 0.5rem;
  font-size: 2rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.form-subtitle {
  color: #666;
  font-size: 1rem;
  margin-top: 0;
}

form {
  background: #fff;
  border-radius: 12px;
  width: 100%;
  padding: 30px 25px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease-in-out;
}

/* Groupes de formulaire */
.form-group {
  margin-bottom: 20px;
}

/* Labels et inputs */
label {
  display: block;
  margin-bottom: 8px;
  font-size: 15px;
  font-weight: 600;
  color: #333;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  font-size: 16px;
  color: #666;
}

input[type="email"],
input[type="password"],
input[type="text"] {
  width: 100%;
  padding: 12px 16px 12px 40px; /* Plus d'espace à gauche pour l'icône */
  margin-top: 5px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
  box-sizing: border-box;
  background-color: #f9f9f9;
  transition: all 0.3s ease;
  outline: none;
}

input:focus {
  border-color: #007bff;
  box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
  background-color: #ffffff;
}

.input-error {
  border-color: #dc3545 !important;
}

.input-error:focus {
  box-shadow: 0 0 8px rgba(220, 53, 69, 0.25) !important;
}

.error-message {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
  margin-bottom: 0;
}

/* Style pour checkbox "Se souvenir de moi" */
.remember-me {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.checkbox-container {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 14px;
  font-weight: normal;
  color: #555;
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
  background-color: #f1f1f1;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-right: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.checkbox-container:hover input ~ .checkmark {
  background-color: #e9e9e9;
}

.checkbox-container input:checked ~ .checkmark {
  background-color: #007bff;
  border-color: #007bff;
}

.checkmark:after {
  content: "✓";
  color: white;
  display: none;
}

.checkbox-container input:checked ~ .checkmark:after {
  display: block;
}

/* Bouton de connexion */
.login-button {
  width: 100%;
  padding: 14px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 50px;
}

.login-button:hover {
  background-color: #0056b3;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 123, 255, 0.25);
}

.login-button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3);
}

.login-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Animation de chargement */
.loading-spinner {
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
  display: inline-block;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Basculement du mot de passe */
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
  font-size: 18px;
  cursor: pointer;
  color: #666;
  padding: 5px;
  width: auto;
  height: auto;
}

.toggle-password:hover {
  color: #007bff;
}

/* Pied de formulaire */
.form-footer {
  text-align: center;
  margin-top: 20px;
  padding: 0 25px;
}

.forgot-password {
  color: #007bff;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s;
}

.forgot-password:hover {
  color: #0056b3;
  text-decoration: underline;
}

.no-account {
  margin-top: 15px;
  color: #555;
  font-size: 14px;
}

.no-account a {
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s;
}

.no-account a:hover {
  color: #0056b3;
  text-decoration: underline;
}

/* Notification d'erreur */
.error-notification {
  background-color: #f8d7da;
  color: #721c24;
  padding: 12px 15px;
  margin-top: 20px;
  border-radius: 8px;
  border-left: 4px solid #dc3545;
  display: flex;
  align-items: center;
  font-size: 14px;
}

.error-icon {
  margin-right: 10px;
  font-size: 18px;
}

/* Responsive design */
@media (max-width: 576px) {
  .login-container {
    padding: 10px;
    margin: 20px auto;
  }
  
  form {
    padding: 20px 15px;
  }
  
  h1 {
    font-size: 1.6rem;
  }
  
  .form-subtitle {
    font-size: 0.9rem;
  }
}
</style>