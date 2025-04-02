<template>
  <div class="auth-container">
    <div class="auth-box login-box" v-if="!isSubmitted">
      <h2 class="auth-title">Inscription Recruteur</h2>
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
              :class="{ invalid: !nom && submitted }"
            />
            <span class="error-message" v-if="!nom && submitted">Nom est requis.</span>
          </div>
          <div class="input-group">
            <label for="prenom">Prénom</label>
            <input
              type="text"
              v-model="prenom"
              id="prenom"
              placeholder="Entrez votre prénom"
              required
              :class="{ invalid: !prenom && submitted }"
            />
            <span class="error-message" v-if="!prenom && submitted"
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
            :class="{ invalid: (!email && submitted) || !isEmailValid }"
            @blur="validateEmail"
          />
          <span class="error-message" v-if="!email && submitted">Email est requis.</span>
          <span class="error-message" v-if="!isEmailValid">Email invalide.</span>
        </div>

        <div class="input-group">
          <label for="password">Mot de passe</label>
          <input
            type="password"
            v-model="password"
            id="password"
            placeholder="Entrez votre mot de passe"
            required
            :class="{ invalid: (!password && submitted) || !isPasswordValid }"
            @blur="validatePassword"
          />
          <span class="error-message" v-if="!password && submitted"
            >Mot de passe est requis.</span
          >
          <span class="error-message" v-if="!isPasswordValid"
            >Mot de passe trop faible.</span
          >
        </div>

        <div class="input-group">
          <label for="confirmPassword">Confirmez votre Mot de passe</label>
          <input
            type="password"
            v-model="confirmPassword"
            id="confirmPassword"
            placeholder="Confirmez votre mot de passe"
            required
            :class="{
              invalid: (!confirmPassword && submitted) || password !== confirmPassword,
            }"
          />
          <span class="error-message" v-if="!confirmPassword && submitted"
            >Confirmation du mot de passe est requise.</span
          >
          <span class="error-message" v-if="password !== confirmPassword && submitted"
            >Les mots de passe ne correspondent pas.</span
          >
        </div>

        <div class="input-group">
          <label for="numtel">Numéro de téléphone</label>
          <input
            type="text"
            v-model="numtel"
            id="numtel"
            placeholder="Entrez votre numéro de téléphone"
            required
            :class="{ invalid: (!numtel && submitted) || !isNumTelValid }"
            @blur="validateNumTel"
          />
          <span class="error-message" v-if="!numtel && submitted"
            >Numéro de téléphone est requis.</span
          >
          <span class="error-message" v-if="!isNumTelValid"
            >Numéro de téléphone invalide.</span
          >
        </div>

        <div class="input-group">
          <label for="nomentreprise">Nom de l'entreprise</label>
          <input
            type="text"
            v-model="nomentrp"
            id="nomentrp"
            placeholder="Entrez le nom de la société"
            required
          />
          <span class="error-message" v-if="!nomentrp && submitted"
            >Nom de l'entreprise est requis.</span
          >
        </div>

        <div class="input-group">
          <label for="site">Site web</label>
          <input
            type="text"
            v-model="site"
            id="site"
            placeholder="Entrez votre site web"
            required
          />
        </div>

        <div class="input-group">
          <label for="lieu">Localisation</label>
          <input
            type="text"
            v-model="lieu"
            id="lieu"
            placeholder="Entrez votre localisation"
            required
          />
          <span class="error-message" v-if="!lieu && submitted"
            >Localisation est requise.</span
          >
        </div>

        <div class="input-group">
          <label for="description">Description de l'entreprise</label>
          <textarea
            v-model="description"
            id="description"
            placeholder="Entrez une brève description de votre entreprise"
            rows="4"
            required
          ></textarea>
          <span class="error-message" v-if="!description && submitted"
            >La description est requise.</span
          >
        </div>

        <button type="submit" class="btn-submit">S'inscrire</button>
      </form>
    </div>

    <div v-else class="auth-box login-box">
      <h2 class="auth-title">Inscription réussie !</h2>
      <div class="welcome-page">
        <h1>Bienvenue, {{ prenom }} ! 🎉</h1>
        <p>
          Votre compte a bien été créé. Vous pouvez maintenant accéder à votre espace
          recruteur.
        </p>
        <router-link to="/" class="btn-back">Retour à l'accueil</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "RegisterRecruteur",
  data() {
    return {
      email: "",
      password: "",
      confirmPassword: "",
      prenom: "",
      nom: "",
      numtel: "",
      nomentrp: "",
      site: "",
      lieu: "",
      description: "",
      isSubmitted: false,
      submitted: false,
      isEmailValid: true,
      isPasswordValid: true,
      isNumTelValid: true,
    };
  },
  methods: {
    async register() {
      this.submitted = true;
      if (
        !this.email ||
        !this.password ||
        !this.confirmPassword ||
        this.password !== this.confirmPassword ||
        !this.prenom ||
        !this.nom ||
        !this.numtel ||
        !this.nomentrp ||
        !this.site ||
        !this.lieu ||
        !this.description
      ) {
        alert("Veuillez remplir tous les champs et vérifier les mots de passe.");
        return;
      }
      try {
        await fetch("http://localhost:8000/sanctum/csrf-cookie", {
          credentials: "include",
        });
        const response = await fetch("http://localhost:8000/api/register", {
          method: "POST",
          headers: { "Content-Type": "application/json", Accept: "application/json" },
          credentials: "include",
          body: JSON.stringify({
            email: this.email,
            password: this.password,
            prenom: this.prenom,
            nom: this.nom,
            num_tel: this.numtel,
            nom_entreprise: this.nomentrp,
            site_web: this.site,
            description: this.description,
            role: "recruteur",
          }),
        });
        const data = await response.json();
        if (!response.ok) {
          alert(data.message || "Erreur lors de l'inscription.");
          return;
        }
        this.isSubmitted = true;
      } catch (error) {
        console.error("Erreur:", error);
        alert("Erreur réseau, veuillez réessayer.");
      }
    },
    validateEmail() {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      this.isEmailValid = regex.test(this.email);
    },
    validatePassword() {
      this.isPasswordValid = this.password.length >= 6;
    },
    validateNumTel() {
      const regex = /^[0-9]{10}$/;
      this.isNumTelValid = regex.test(this.numtel);
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
  background-color: #f4f6f9;
  padding: 20px;
  box-sizing: border-box;
}

.auth-box {
  background: #fff;
  border-radius: 12px;
  max-width: 650px; /* Augmentation de la largeur */
  width: 100%;
  padding: 40px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transition: 0.3s ease;
}

.auth-title {
  font-size: 2rem; /* Augmentation de la taille du titre */
  margin-bottom: 24px;
  color: #4a7eb2;
  font-weight: bold;
  text-align: center;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 30px;
  text-align: left;
}

.input-group input,
.input-group textarea {
  width: 80%;
  padding: 14px 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  color: #333;
  background-color: #fafafa;
  transition: all 0.3s ease;
}

.input-group input:focus,
.input-group textarea:focus {
  border-color: #3d63ea;
  outline: none;
  background-color: #fff;
  box-shadow: 0 0 5px rgba(61, 99, 234, 0.4);
}

/* ==== Styling for invalid fields ==== */
.input-group input.invalid,
.input-group textarea.invalid {
  border-color: #e74c3c; /* Bordure rouge pour les champs invalides */
  background-color: #fdecea; /* Fond légèrement rouge pour les champs invalides */
}

.error-message {
  color: #e74c3c;
  font-size: 0.875rem;
  margin-top: 5px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.btn-submit {
  width: 100%;
  padding: 14px;
  background: linear-gradient(90deg, #3d63ea, #506df5);
  border: none;
  color: white;
  font-size: 1.1rem;
  border-radius: 8px;
  cursor: pointer;
  margin-top: 16px;
}

.btn-submit:hover {
  background: #2f53d6;
}

.btn-back {
  margin-top: 1rem;
  display: inline-block;
  background-color: #3d63ea;
  color: #fff;
  padding: 10px 20px;
  border-radius: 8px;
  text-decoration: none;
}

.btn-back:hover {
  background-color: #2f53d6;
}

.welcome-page {
  text-align: center;
}

.auth-box h2 {
  font-size: 1.8rem;
  color: #333;
  margin-bottom: 1.5rem;
}

/* ===== Responsive Styles ===== */
@media (max-width: 768px) {
  .auth-box {
    padding: 30px;
  }

  .input-group input,
  .input-group textarea {
    padding: 12px 14px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .btn-submit {
    font-size: 1rem;
  }
}
</style>
