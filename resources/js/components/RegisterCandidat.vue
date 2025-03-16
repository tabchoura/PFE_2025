<template>
    <div class="registercontainer">
      <div class="registerbox" v-if="!isSubmitted">
        <h2 class="title">Création du compte</h2>
        
        <form @submit.prevent="register" class="registerform">
          <!-- Champ Email -->
          <div class="input-group">
            <label for="email">Adresse email</label>
            <input type="email" v-model="email" required placeholder="Entrez votre email" />
          </div>
  
          <!-- Civilité -->
          <div class="input-group">
            <label>Civilité</label>
            <div class="radio-group">
              <input type="radio" v-model="civilite" value="monsieur" id="civilite-m" />
              <label for="civilite-m">Monsieur</label>
              <input type="radio" v-model="civilite" value="madame" id="civilite-f" />
              <label for="civilite-f">Madame</label>
            </div>
          </div>
  
          <!-- Mot de passe -->
          <div class="input-group">
            <label for="password">Mot de passe</label>
            <input type="password" v-model="password" required placeholder="Entrez votre mot de passe" />
          </div>
  
          <!-- Confirmation du mot de passe -->
          <div class="input-group">
            <label for="confirm-password">Confirmer le mot de passe</label>
            <input type="password" v-model="confirmPassword" required placeholder="Confirmez votre mot de passe" />
          </div>
  
          <!-- Prénom -->
          <div class="input-group">
            <label for="prenom">Prénom</label>
            <input type="text" v-model="prenom" required placeholder="Entrez votre prénom" />
          </div>
  
          <!-- Nom -->
          <div class="input-group">
            <label for="nom">Nom</label>
            <input type="text" v-model="nom" required placeholder="Entrez votre nom" />
          </div>
  
          <!-- Vous êtes -->
          <div class="input-group">
            <label>Vous êtes :</label>
            <select v-model="statut" required>
              <option value="etudiant">Etudiant</option>
              <option value="jeune-diplome">Jeune diplômé</option>
              <option value="retraite">Retraité</option>
              <option value="autre">Autre</option>
            </select>
          </div>
  
          <!-- Ville -->
          <div class="input-group">
            <label>Ville</label>
            <select v-model="ville" required>
              <option value="Monastir">Monastir</option>
              <option value="Sousse">Sousse</option>
              <option value="Mahdia">Mahdia</option>
              <option value="Tunis">Tunis</option>
            </select>
          </div>
  
          <!-- Code postal -->
          <div class="input-group">
            <label for="codepostal">Code postal</label>
            <input type="number" v-model="codepostal" required placeholder="Entrez votre code postal" />
          </div>
  
          <!-- Handicap -->
          <div class="input-group">
            <label>Handicap :</label>
            <input type="checkbox" v-model="handicap" /> Je suis reconnu(e) en tant que travailleur handicapé(e)
          </div>
  
          <!-- Bouton de soumission -->
          <button type="submit" class="btn-submit">S'inscrire</button>
        </form>
      </div>
  
      <!-- Affichage du composant RegisterCandidat après la soumission -->
      <div v-if="isSubmitted">
        <RegisterCandidat />
      </div>
    </div>
  </template>
  
  <script>
  import RegisterCandidat from './RegisterCandidat.vue';
  
  export default {
    data() {
      return {
        email: '',
        civilite: '',
        password: '',
        confirmPassword: '',
        prenom: '',
        nom: '',
        statut: '',
        ville: '',
        codepostal: '',
        handicap: false,
        isSubmitted: false, // Variable pour savoir si le formulaire est soumis
      };
    },
    methods: {
      register() {
        // Affichage des données dans la console lorsque le formulaire est soumis
        console.log('Données soumises :', {
          email: this.email,
          civilite: this.civilite,
          password: this.password,
          confirmPassword: this.confirmPassword,
          prenom: this.prenom,
          nom: this.nom,
          statut: this.statut,
          ville: this.ville,
          codepostal: this.codepostal,
          handicap: this.handicap,
        });
  
        // Lorsque le formulaire est soumis, on affiche le composant RegisterCandidat
        this.isSubmitted = true;
      },
    },
    components: {
      RegisterCandidat,  // Assurez-vous que ce composant est bien importé
    }
  };
  </script>
  
  <style scoped>
  /* Styles basiques pour le formulaire */
  .registercontainer {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f4f7fa;
  }
  
  .registerbox {
    background: #ffffff;
    padding: 30px;
    border-radius: 8px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .title {
    font-size: 24px;
    margin-bottom: 20px;
    text-align: center;
    color: #333;
  }
  
  .input-group {
    margin-bottom: 20px;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #333;
  }
  
  input[type="email"],
  input[type="password"],
  input[type="text"],
  input[type="number"],
  select {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 5px;
  }
  
  input[type="radio"] {
    margin-left: 10px;
  }
  
  .radio-group {
    display: flex;
    gap: 10px;
    align-items: center;
  }
  
  button[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
    margin-top: 20px;
  }
  
  button[type="submit"]:hover {
    background-color: #45a049;
  }
  
  input[type="checkbox"] {
    margin-top: 10px;
  }
  </style>
  