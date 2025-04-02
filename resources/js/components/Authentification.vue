<template>
  <div class="auth-container">
    <!-- Formulaire de Connexion -->
    <div class="auth-box login-box" v-if="page === 'login'">
      <h2 class="auth-title">Vous avez déjà un compte ?</h2>

      <!-- Toggle Candidat / Recruteur -->
      <div class="toggle-container">
        <span :class="{ active: !isRecruteur }">Candidat</span>
        <label class="switch">
          <input
            type="checkbox"
            v-model="isRecruteur"
            aria-label="Basculer entre Candidat et Recruteur"
          />
          <span class="slider round"></span>
        </label>
        <span :class="{ active: isRecruteur }">Recruteur</span>
      </div>

      <form @submit.prevent="login" class="auth-form">
        <div class="input-group">
          <label for="email">Email</label>
          <input
            type="email"
            v-model="email"
            id="email"
            placeholder="Entrez votre email"
            required
            aria-required="true"
          />
        </div>
        <div class="input-group">
          <label for="password">Mot de passe</label>
          <input
            type="password"
            v-model="password"
            id="password"
            placeholder="Entrez votre mot de passe"
            required
            aria-required="true"
          />
        </div>
        <button type="submit" class="btn-submit">Se connecter</button>

        <h2 class="inscrit-title">
          Vous voulez vous inscrire en tant que
          <span class="choix">{{ isRecruteur ? "Recruteur" : "Candidat" }}</span> ?
        </h2>
        <button class="btn-create" @click.prevent="goToSignup">
          {{ isRecruteur ? "Créer un compte recruteur" : "Créer un compte candidat" }}
        </button>
      </form>
    </div>

    <!-- Formulaires d'inscription -->
    <div v-if="page === 'registerCandidat'">
      <RegisterCandidat />
    </div>
    <div v-if="page === 'registerRecruteur'">
      <RegisterRecruteur />
    </div>
  </div>
</template>

<script>
import RegisterCandidat from "../components/RegisterCandidat.vue";
import RegisterRecruteur from "../components/RegisterRecruteur.vue";

export default {
  components: {
    RegisterCandidat,
    RegisterRecruteur,
  },
  data() {
    return {
      email: "",
      password: "",
      isRecruteur: false,
      page: "login", // Page par défaut, affiche le formulaire de connexion
    };
  },
  methods: {
    login() {
      console.log("Connexion en tant que", this.isRecruteur ? "Recruteur" : "Candidat");
    },
    goToSignup() {
      // Change la page pour afficher le formulaire d'inscription
      this.page = this.isRecruteur ? "registerRecruteur" : "registerCandidat";
    },
  },
};
</script>

<style scoped>
/* ===== Auth Container ===== */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: transparent;
  padding: 20px;
  box-sizing: border-box;
  flex-direction: column; /* Centrer verticalement */
}

/* ===== Auth Box Styles ===== */
.auth-box {
  background: #fff;
  border-radius: 12px;
  max-width: 480px;
  width: 100%;
  padding: 40px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid #ddd;
  text-align: center;
}

.auth-box:hover {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.auth-title {
  font-size: 1.8rem;
  margin-bottom: 15px;
  color: #2c3e50;
  font-weight: 600;
  letter-spacing: 1px;
}

/* ===== Toggle Candidat / Recruteur ===== */
.toggle-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 20px;
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
}

input:checked + .slider {
  background-color: #2980b9;
}

input:checked + .slider:before {
  transform: translateX(30px);
}

.toggle-container span {
  font-weight: 600;
  color: #34495e;
}

.toggle-container span.active {
  color: #2980b9;
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
}

.input-group input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
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

.btn-submit {
  width: 100%;
  padding: 14px;
  background-color: #2980b9;
  border: none;
  color: white;
  font-size: 1.1rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.btn-submit:hover {
  background-color: #1f6690;
}

.inscrit-title {
  font-size: 1.4rem;
  margin-bottom: 19px;
  font-weight: 600;
}

.btn-create {
  width: 100%;
  padding: 14px;
  background-color: #2980b9;
  border: none;
  color: white;
  font-size: 1.1rem;
  border-radius: 6px;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.3s ease;
}

.btn-create:hover {
  background-color: #1f6690;
}

/* ===== Responsive Styles ===== */
@media (max-width: 768px) {
  .auth-container {
    padding: 20px;
  }

  .auth-box {
    padding: 30px;
    width: 100%;
  }

  .btn-submit,
  .btn-create {
    font-size: 1rem;
    padding: 12px;
  }
}
</style>
