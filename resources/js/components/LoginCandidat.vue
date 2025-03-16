<template>
    <div>
      <!-- Titre -->
      <h1>Vous avez déjà un compte ?</h1>
      <br />
  
      <!-- Toggle entre Candidat et Recruteur -->
      <div id="sourceToggle">
        <label for="source" id="source-toggle" class="slide-toggle-label">
          <span class="slide-toggle-content slide-toggle-content-left" :class="{ active: !isRecruteur }">
            Candidat
          </span>
          <div class="slide-toggle-bar">
            <input 
              type="checkbox" 
              id="source"
              v-model="isRecruteur" 
            />
            <div class="slide-toggle-thumb"></div>
          </div>
          <span class="slide-toggle-content slide-toggle-content-right" :class="{ active: isRecruteur }">
            Recruteur
          </span>
        </label>
      </div>
  
      <!-- Affichage dynamique en fonction de l'utilisateur -->
      <div>
        <!-- Formulaire de connexion -->
        <form @submit.prevent="submitForm">
          <label for="email">Email :</label>
          <input type="email" id="email" v-model="email" required />
  
          <label for="password">Mot de passe :</label>
          <input type="password" id="password" v-model="password" required />
  
          <button type="submit">Se connecter</button>
          <!-- Lien pour mot de passe oublié -->


        </form>
  
        <!-- Lien pour mot de passe oublié -->
        <div class="forgot-password">
          <a href="#" @click="resetPassword">Mot de passe oublié ?</a>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isRecruteur: false,  // False = Candidat, True = Recruteur
        email: "",
        password: "",
      };
    },
    computed: {
      userType() {
        return this.isRecruteur ? "Recruteur" : "Candidat";
      },
    },
    methods: {
      submitForm() {
        console.log(`Connexion en tant que ${this.userType}`);
        console.log(`Email: ${this.email}`);
        console.log(`Mot de passe: ${this.password}`);
      },
      resetPassword() {
        console.log("Redirection vers la page de réinitialisation du mot de passe");
        // Vous pouvez remplacer cette méthode pour rediriger vers une autre page de réinitialisation du mot de passe.
      },
    },
  };
  </script>
  
  <style scoped>
  h1 {
    text-align: center;
    color: #007bffbc;
    margin-bottom: 1.5rem;
    margin-top: 20px;
  }
  
  /* Style pour le toggle */
  #sourceToggle {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
  }
  
  .slide-toggle-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 16px;
  }
  
  .slide-toggle-bar {
    width: 50px;
    height: 25px;
    background: #ddd;
    border-radius: 15px;
    margin: 0 10px;
    position: relative;
  }
  
  .slide-toggle-thumb {
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 2.5px;
    left: 3px;
    transition: 0.3s;
  }
  
  input[type="checkbox"] {
    display: none;
  }
  
  input[type="checkbox"]:checked + .slide-toggle-thumb {
    left: 25px;
  }
  
  .slide-toggle-content {
    font-weight: bold;
  }
  
  .active {
    color: #3b8884;
  }
  
  /* Style du formulaire */
  form {
    max-width: 400px;
    margin: auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;  /* Coins arrondis */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);  /* Ombre portée */
    transition: all 0.3s ease; /* Transition fluide pour l'ombre */
  }
  
  form:hover {
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2); /* Ombre plus marquée au survol */
  }
  
  label {
    display: block;
    margin-top: 10px;
    font-size: 14px;
  }
  
  input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;  /* Assure que la taille du champ inclut les marges internes */
    transition: border-color 0.3s ease, box-shadow 0.3s ease;  /* Transition fluide */
  }
  
  input:focus {
    border-color: #007bff;  /* Couleur de bordure au focus */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);  /* Ombre au focus */
  }
  
  button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    display: flex;
    justify-content: center; 
    align-items: center;   
  }
  
  button:hover {
    transition: transform 0.4s ease;
    background-color: #0056b3;
  }
  
  form {
    border-radius: 3px;
  }
  
  /* Style pour le lien "Mot de passe oublié" */
  .forgot-password {
    text-align: center;
    margin-top: 10px;
    font-size: large;
  }
  
  .forgot-password a {
    font-size: 14px;
    color: #007bff;
    text-decoration: none;
  }
  
  .forgot-password a:hover {
    text-decoration: underline;
  }
  </style>
  