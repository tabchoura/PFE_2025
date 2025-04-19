<template>
    <div class="applications-container">
      <h1 class="title">📄 Mes Candidatures</h1>
  
      <div v-if="applications.length" class="application-list">
        <div v-for="(application, index) in applications" :key="index" class="card">
          <div class="card-header">
            <h2 class="job-title">{{ application.titre }}</h2>
            <span class="badge">Candidature Recues</span>
          </div>
          <p class="company">🏢 {{ application.entreprise }}</p>
          <p class="date">📅 {{ application.date }}</p>
          <a :href="application.lien" target="_blank" class="btn-link">Voir l'offre</a>
        </div>
      </div>
  
      <p v-else class="no-applications">🚫 Aucune candidature en cours.</p>
    </div>
  </template>
  
  <script>
  export default {
    name: "candidaturesrecurteur",
    data() {
      return {
        applications: [
          {
            titre: "Ingénieur DevOps",
            entreprise: "Entreprise A",
            date: "2025-04-01",
            lien:
              "https://www.apec.fr/candidat/recherche-emploi.html/emploi/detail-offre/176124454W",
          },
          {
            titre: "Développeur COBOL",
            entreprise: "Entreprise B",
            date: "2025-03-28",
            lien:
              "https://www.apec.fr/candidat/recherche-emploi.html/emploi/detail-offre/176182835W",
          },
        ],
      };
    },
  };
  </script>
  
  <style scoped>
  .applications-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 1rem;
  }
  
  .title {
    text-align: center;
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 2rem;
  }
  
  .application-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .card {
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    padding: 1.25rem 1.5rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  
  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  }
  
  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .job-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
  }
  
  .company {
    margin-top: 0.5rem;
    font-weight: 500;
    color: #374151;
  }
  
  .date {
    font-size: 0.9rem;
    color: #6b7280;
    margin: 0.25rem 0 0.75rem;
  }
  
  .badge {
    background-color: #dbeafe;
    color: #2563eb;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.65rem;
    border-radius: 9999px;
    text-transform: uppercase;
  }
  
  .btn-link {
    display: inline-block;
    font-size: 0.95rem;
    font-weight: 500;
    color: #2563eb;
    text-decoration: none;
    margin-top: 0.5rem;
    transition: color 0.2s ease;
  }
  
  .btn-link:hover {
    color: #1d4ed8;
    text-decoration: underline;
  }
  
  .no-applications {
    text-align: center;
    color: #6b7280;
    font-style: italic;
    margin-top: 2rem;
  }
  </style>
  