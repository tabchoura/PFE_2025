<template>
    <div class="confirm-container">
      <h2>Confirmation de candidature</h2>
      <p>Vous êtes sûr de postuler à l’offre <strong>#{{ offerId }}</strong> ?</p>
  
      <div class="buttons">
        <button class="btn-yes" @click="confirm">Oui, je postule</button>
        <button class="btn-no"  @click="cancel">Non, revenir</button>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { useRoute, useRouter } from 'vue-router'
  import axios from 'axios'
  import { useToast } from 'vue-toastification'
  
  const route = useRoute()
  const router = useRouter()
  const toast  = useToast()
  
  // Récupère l’ID de l’offre depuis l’URL
  const offerId = route.params.offerId as string
  
  async function confirm() {
    try {
      // Appel API pour postuler à l’offre
      await axios.post(`/api/offres/${offerId}/postuler`)
      toast.success("Votre candidature a bien été envoyée !")
      // Redirige vers la page mes candidatures ou tableau de bord
      router.push({ name: 'Candidature' })
    } catch (e) {
      toast.error("Échec de la candidature. Réessayez.")
      console.error(e)
    }
  }
  
  function cancel() {
    // Retourne simplement à la page précédente
    router.back()
  }
  </script>
  
  <style scoped>
  .confirm-container {
    max-width: 500px;
    margin: 4rem auto;
    text-align: center;
    font-family: "Segoe UI", sans-serif;
  }
  .buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
  }
  .btn-yes, .btn-no {
    padding: 0.8rem 1.6rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: transform 0.2s;
  }
  .btn-yes {
    background: #27ae60;
    color: white;
  }
  .btn-no {
    background: #e74c3c;
    color: white;
  }
  .btn-yes:hover, .btn-no:hover {
    transform: translateY(-2px);
  }
  </style>
  