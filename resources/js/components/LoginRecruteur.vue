<template>
    <div>
      <h1>Page de Connexion Recruteur</h1>
      <!-- Contenu de la page Recruteur -->
      <form>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required />
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required />
        <button type="submit">Se connecter</button>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    name: "LoginRecruteur"
  };
  </script>
  
  <style scoped>
  /* Styles pour la page de connexion Recruteur */
  h1 {
    text-align: center;
    margin-top: 20px;
  }
  form {
    max-width: 400px;
    margin: auto;
  }
  label {
    display: block;
    margin-top: 10px;
  }
  input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    margin-bottom: 15px;
  }
  button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
  }
  button:hover {
    background-color: #0056b3;
  }
  </style>
  