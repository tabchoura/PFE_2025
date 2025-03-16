<template>
    <div class="qui-sommes-nous">
      <h1 class="titre">Qui sommes-nous ?</h1>
  
      <p class="description">
        JobGo est une plateforme innovante dédiée au recrutement en ligne.  
        Conçue pour simplifier et accélérer le processus d’embauche,  
        elle met en relation les recruteurs et les candidats de manière intuitive et efficace.  
        Grâce à des outils avancés et une interface fluide,  
        JobGo optimise la gestion des candidatures et facilite l’accès aux opportunités professionnelles.
      </p>
  
      <p class="inscription">
        ➡️ <strong>Pour plus de détails, <a href="#" @click="page = 'register'">inscrivez-vous dès maintenant</a>.</strong>
      </p>
    </div>
  </template>
  
  <script>
  export default {
    name: "QuiSommesNous",
  };
  </script>
  
  <style scoped>
  /* ✅ Conteneur principal */
  .qui-sommes-nous {
    max-width: 900px;
    margin: auto;
    padding: 40px;
    text-align: center;
  }
  
  /* ✅ Titre principal */
  .titre {
    font-size: 32px;
    font-weight: bold;
    color: #007bff;
  }
  
  /* ✅ Description */
  .description {
    font-size: 18px;
    color: #444;
    margin-bottom: 20px;
    line-height: 1.6;
  }
  
  /* ✅ Texte d'inscription */
  .inscription {
    font-size: 18px;
    color: #007bff;
    font-weight: bold;
  }
  
  /* ✅ Lien vers l'inscription */
  .inscription a {
    text-decoration: none;
    color: #0056b3;
    transition: 0.3s;
  }
  
  .inscription a:hover {
    text-decoration: underline;
  }
  </style>
  