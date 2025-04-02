<template>
  <div class="auth-container">
    <img class="logo-img" :src="télécharger" alt="Logo img" />

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
        </div>

        <div class="input-group">
          <label for="password">Mot de passe</label>
          <input
            type="password"
            v-model="password"
            id="password"
            placeholder="Entrez votre mot de passe"
            required
          />
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
              type="text"
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

        <button type="submit" class="btn-submit">S'inscrire</button>
      </form>
    </div>

    <div v-else class="auth-box login-box">
      <h2 class="auth-title">Inscription réussie !</h2>
      <div class="welcome-page">
        <h1>Bienvenue, {{ prenom }} ! 🎉</h1>
        <p>
          Votre compte a bien été créé. Vous pouvez maintenant accéder à votre espace
          candidat.
        </p>
        <router-link to="/" class="btn-back">Retour à l'accueil</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import télécharger from "../../assets/télécharger.jpg";

export default {
  name: "RegisterCandidat",
  data() {
    return {
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
    };
  },
  methods: {
    async register() {
      this.submitted = true;
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
        alert("Veuillez remplir tous les champs.");
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
            date_naissance: this.date,
            lieu_naissance: this.lieu,
            cin: this.cin,
            num_tel: this.numtel,
            role: "candidat",
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
  max-width: 600px;
  width: 100%;
  padding: 40px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  transition: 0.3s ease;
}

.auth-title {
  font-size: 1.8rem;
  margin-bottom: 24px;
  color: #2c3e50;
  font-weight: bold;
  text-align: center;
}

/* Form Fields Styles */
.input-group {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 30px;
  text-align: left;
}

.input-group input {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  color: #333;
  background-color: #fafafa;
  transition: all 0.3s ease;
}

.input-group input:focus {
  border-color: #3d63ea;
  outline: none;
  background-color: #fff;
  box-shadow: 0 0 5px rgba(61, 99, 234, 0.4);
}

/* Error message style */
.error-message {
  color: #e74c3c;
  font-size: 0.875rem;
  margin-top: 5px;
  margin-bottom: 10px;
}

/* Grid Styles for Form */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

/* Submit Button Styles */
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
  transition: background-color 0.3s ease;
}

.btn-submit:hover {
  background: #2f53d6;
}

/* Success and Back Buttons */
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
.logo-img {
  height: 50px;
  width: auto;
  transition: transform 0.3s ease;
}

/* ===== Responsive Styles ===== */
@media (max-width: 768px) {
  .auth-box {
    padding: 30px;
    width: 100%;
  }

  .input-group input {
    padding: 12px 14px;
    margin-right: 20px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .btn-submit {
    font-size: 1rem;
  }
}
</style>
