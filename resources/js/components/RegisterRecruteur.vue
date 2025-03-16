<template>
    <div class="auth-container">
      <!-- Formulaire de Connexion -->
      <div class="auth-box login-box">
        <h2 class="auth-title">Vous avez déjà un compte ?</h2>
        <div class="toggle-container">
          <span :class="{ 'active': !isRecruteur }">Candidat</span>
          <label class="switch">
            <input type="checkbox" v-model="isRecruteur" />
            <span class="slider round"></span>
          </label>
          <span :class="{ 'active': isRecruteur }">Recruteur</span>
        </div>
        <form @submit.prevent="login" class="auth-form">
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" v-model="email" id="email" placeholder="Entrez votre email" required />
          </div>
          <div class="input-group">
            <label for="password">Mot de passe</label>
            <input type="password" v-model="password" id="password" placeholder="Entrez votre mot de passe" required />
          </div>
          <button type="submit" class="btn-submit">Se connecter</button>
          <h2 class="inscrit-title">Vous voulez vous inscrire en tant que <span class="choix">{{ isRecruteur ? 'Recruteur' : 'Candidat' }}</span> ?</h2>
          <!-- Appel au clic pour afficher la page RegisterCandidat.vue -->
          <a href="#" @click="page = 'RegisterCandidat'" class="btn-create">
            {{ isRecruteur ? 'Créer un compte recruteur' : 'Créer un compte candidat' }}
          </a>
        </form>
      </div>
  
      <!-- Affichage du composant RegisterCandidat.vue si la variable page est définie sur 'RegisterCandidat' -->
      <RegisterCandidat v-if="page === 'RegisterCandidat'" />
    </div>
  </template>
  
  <script>
  // Assurez-vous d'importer RegisterCandidat.vue si vous ne l'avez pas encore fait
  import RegisterCandidat from './RegisterCandidat.vue';
  
  export default {
    data() {
      return {
        email: '',
        password: '',
        isRecruteur: false,
        page: '', // Variable qui contrôlera quelle page afficher
      };
    },
    methods: {
      login() {
        console.log('Connexion en tant que', this.isRecruteur ? 'Recruteur' : 'Candidat');
      }
    },
    components: {
      RegisterCandidat // Ajout du composant RegisterCandidat.vue ici
    }
  };
  </script>
  
  <style scoped>
  /* Styles pour le formulaire et la page */
  .auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 90vh;
    background-color: #f4f6f9;
    gap: 60px;
    padding: 0 20px;
    box-sizing: border-box;
  }
  
  .auth-box {
    background: white;
    border-radius: 12px;
    max-width: 480px;
    padding: 40px;
    height: 550px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid #ddd;
  }
  
  .auth-box:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  }
  
  .auth-title {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #2c3e50;
    font-weight: 600;
  }
  
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
  
  .choix {
    color: #2a6865;
    font-size: larger;
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
  }
  
  </style>
  