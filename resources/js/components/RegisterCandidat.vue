<template>
  <div class="auth-container">
    <!-- Image correctement importée -->

    <div class="image-container">
      <img :src="hiringImage" alt="Recrutement" class="img" />
    </div>

    <div class="auth-box login-box" v-if="!isSubmitted">
      <h2 class="auth-title">Inscription Candidat</h2>
      <form @submit.prevent="register" class="auth-form">
        <div class="form-grid">
          <div class="input-group">
            <label for="nom">Nom</label>
            <input
              type="text"
              v-model="nom"
              id="nom"
              placeholder="Entrez votre nom"
              required
            />
            <span class="error-message" v-if="submitted && !nom">Nom est requis.</span>
          </div>
          <div class="input-group">
            <label for="prenom">Prénom</label>
            <input
              type="text"
              v-model="prenom"
              id="prenom"
              placeholder="Entrez votre prénom"
              required
            />
            <span class="error-message" v-if="submitted && !prenom"
              >Prénom est requis.</span
            >
          </div>
        </div>

        <div class="input-group">
          <label for="email">Email</label>
          <input
            type="email"
            v-model="email"
            id="email"
            placeholder="Entrez votre email"
            required
          />
          <span class="error-message" v-if="submitted && !email">Email est requis.</span>
          <span class="error-message" v-if="submitted && email && !isValidEmail"
            >Format d'email invalide.</span
          >
        </div>

        <div class="input-group">
          <label for="password">Mot de passe</label>
          <div class="password-input-container">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              id="password"
              placeholder="Entrez votre mot de passe"
              required
            />
            <button
              type="button"
              class="toggle-password"
              @click="showPassword = !showPassword"
              aria-label="Afficher/masquer le mot de passe"
            >
              <span v-if="!showPassword">👁️</span>
              <span v-else>🔒</span>
            </button>
          </div>
          <span class="error-message" v-if="submitted && !password"
            >Mot de passe est requis.</span
          >
        </div>

        <div class="form-grid">
          <div class="input-group">
            <label for="cin">CIN</label>
            <input
              type="text"
              v-model="cin"
              id="cin"
              placeholder="Entrez votre CIN"
              required
            />
            <span class="error-message" v-if="submitted && !cin">CIN est requis.</span>
          </div>
          <div class="input-group">
            <label for="numtel">Numéro de téléphone</label>
            <input
              type="tel"
              v-model="numtel"
              id="numtel"
              placeholder="Votre numéro de téléphone"
              required
            />
            <span class="error-message" v-if="submitted && !numtel"
              >Numéro de téléphone est requis.</span
            >
          </div>
        </div>

        <div class="form-grid">
          <div class="input-group">
            <label for="date">Date de naissance</label>
            <input type="date" v-model="date" id="date" required />
            <span class="error-message" v-if="submitted && !date"
              >Date de naissance est requise.</span
            >
          </div>
          <div class="input-group">
            <label for="lieu">Lieu de naissance</label>
            <input
              type="text"
              v-model="lieu"
              id="lieu"
              placeholder="Ville / Commune"
              required
            />
            <span class="error-message" v-if="submitted && !lieu"
              >Lieu de naissance est requis.</span
            >
          </div>
        </div>

        <div v-if="apiError" class="error-message global-error">
          {{ apiError }}
        </div>

        <button type="submit" class="btn-submit" :disabled="loading">
          <span v-if="loading">Traitement en cours...</span>
          <span v-else>S'inscrire</span>
        </button>
      </form>
    </div>

    <div v-else class="auth-box login-box success-box">
      <div class="check-icon">✓</div>
      <h2 class="auth-title">Inscription réussie !</h2>
      <div class="welcome-page">
        <h1>Bienvenue, {{ prenom }} ! 🎉</h1>
        <p>
          Votre compte a bien été créé. Vous pouvez maintenant accéder à votre espace
          candidat.
        </p>
        <router-link to="/CompteCandidat" class="btn-back"></router-link>
      </div>
    </div>
  </div>
</template>

<script>
import hiringImage from "../../assets/hiring.jpg";

export default {
  name: "RegisterCandidat",
  data() {
    return {
      hiringImage,
      email: "",
      password: "",
      prenom: "",
      nom: "",
      date: "",
      lieu: "",
      cin: "",
      numtel: "",
      isSubmitted: false,
      submitted: false,
      loading: false,
      apiError: null,
      showPassword: false,
    };
  },
  computed: {
    isValidEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(this.email);
    },
  },
  methods: {
    validateForm() {
      this.submitted = true;
      this.apiError = null;

      if (
        !this.email ||
        !this.password ||
        !this.prenom ||
        !this.nom ||
        !this.cin ||
        !this.numtel ||
        !this.date ||
        !this.lieu
      ) {
        return false;
      }

      if (!this.isValidEmail) {
        return false;
      }

      return true;
    },

    register() {
      this.submitted = true;
      if (!this.validateForm()) return;

      this.loading = true;

      // Création de l'objet avec TOUTES les informations du formulaire
      const candidatData = {
        nom: this.nom,
        prenom: this.prenom,
        email: this.email,
        numtel: this.numtel,
        date: this.date,
        lieu: this.lieu,
        cin: this.cin,
        // Ajoutez d'autres champs si nécessaire
      };

      // Enregistrement dans localStorage
      localStorage.setItem("candidat", JSON.stringify(candidatData));

      // Simulation d'un délai (comme si c'était une requête API)
      setTimeout(() => {
        this.loading = false;
        this.isSubmitted = true;

        // La redirection doit se faire après l'affichage du message de succès
        // pour une meilleure expérience utilisateur
        setTimeout(() => {
          this.$router.push({ name: "CompteCandidat" });
        }, 2000); // Attendre 2 secondes après l'affichage du message de succès
      }, 1500);
    },
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
  padding: 33px;
  box-sizing: border-box;
  margin-left: 20px;
  flex-direction: row; /* Place image and form side by side */
  gap: 30px; /* Space between image and form */
}

.image-container {
  flex: 1;
  display: flex;
  justify-content: flex-end;
}

.img {
  max-width: 80%; /* Adjust the image size */
  height: auto;
  object-fit: contain;
  display: flex;
  flex-direction: row;
  justify-content: center;
  position: relative;
  border-radius: 15%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.img:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

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

.auth-title {
  font-size: 1.8rem;
  margin-bottom: 24px;
  color: #2c3e50;
  font-weight: bold;
  text-align: center;
}

/* Add responsive styles */
@media (max-width: 768px) {
  .auth-container {
    flex-direction: column; /* Stack the image and form vertically on smaller screens */
  }

  .image-container {
    margin-bottom: 20px; /* Space between image and form */
  }

  .img {
    max-width: 80%;
  }
}
</style>
