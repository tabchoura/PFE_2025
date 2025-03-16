<template>
    <div class="offres-section">
      <h2>Nos Offres de Recrutement</h2>
      <div class="offres-list">
        <div class="offre" v-for="offre in offres" :key="offre.id">
          <h3>{{ offre.titre }}</h3>
          <p>{{ offre.description }}</p>
          <button @click="applyForJob(offre.id)">Postuler</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "Offres",
    data() {
      return {
        offres: [
          { id: 1, titre: "Développeur Front-End", description: "Rejoignez notre équipe pour développer des interfaces utilisateurs modernes." },
          { id: 2, titre: "Chef de projet IT", description: "Nous cherchons un chef de projet expérimenté pour piloter des projets d'envergure." },
          { id: 3, titre: "Designer UX/UI", description: "Vous êtes créatif et passionné par le design, ce poste est pour vous." }
          // Ajoutez plus d'offres ici
        ]
      };
    },
    methods: {
      applyForJob(id) {
        console.log("Candidature envoyée pour l'offre ID:", id);
        // Vous pouvez ajouter une logique pour traiter les candidatures ici
      }
    }
  };
  </script>
  
  <style scoped>
  .offres-section {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .offres-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
  }
  
  .offre {
    background: #f4f4f4;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .offre h3 {
    color: #007bff;
  }
  
  button {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  button:hover {
    background-color: #0056b3;
  }
  </style>
  