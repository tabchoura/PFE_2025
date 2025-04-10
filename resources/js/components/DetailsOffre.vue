<template>
    <div class="detail-page">
      <h2 class="detail-title">Détail de l'offre</h2>
      <div v-if="offre">
        <h3>{{ offre.titre }}</h3>
        <p>{{ offre.description }}</p>
        <router-link to="/" class="back-link">Retour aux offres</router-link>
      </div>
      <div v-else>
        <p>Offre introuvable.</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { useRoute } from 'vue-router';
  import { computed } from 'vue';
  
  const route = useRoute();
  const id = parseInt(route.params.id);
  
  const offres = [
    {
      titre: 'Website Design for SCFC Canada',
      description:
        'Born out of a vision, a single-minded objective that puts service before anything else, Swift Clearance and Forwarding Corp. surging forth to deliver the best services in the shipping and logistics scenario.',
    },
    {
      titre: 'Website Optimization for SCFC',
      description:
        'Enhance the site’s performance, improve loading times and accessibility, and improve SEO rankings for SCFC Canada’s web presence.',
    },
    {
      titre: 'E-Commerce Integration for SCFC Canada',
      description:
        'Integration of a modern e-commerce system to allow SCFC Canada to manage online bookings and payments more efficiently.',
    },
  ];
  
  const offre = computed(() => offres[id]);
  </script>
  
  <style scoped>
  .detail-page {
    padding: 40px;
    max-width: 800px;
    margin: auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    text-align: left;
  }
  .detail-title {
    font-size: 2.2rem;
    font-weight: bold;
    margin-bottom: 20px;
    color: #2c3e50;
  }
  .back-link {
    display: inline-block;
    margin-top: 20px;
    color: #2980b9;
    text-decoration: underline;
    font-weight: 600;
  }
  </style>
  