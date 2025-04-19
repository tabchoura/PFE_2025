<template>
    <div class="offers-section">
      <!-- Bouton ajouter -->
      <div class="add-offer-button">
        <button class="btn-action" @click="openAddForm">+ Ajouter une offre</button>
      </div>
  
      <!-- Formulaire ajout/modif -->
      <div v-if="showForm" class="offer-form">
        <h3>{{ isEditing ? 'Modifier l\'offre' : 'Ajouter une offre' }}</h3>
        <input v-model="form.title" placeholder="Titre de l'offre" />
        <textarea v-model="form.description" placeholder="Description"></textarea>
        <div class="form-actions">
          <button class="btn-submit" @click="saveOffer">Enregistrer</button>
          <button class="btn-cancel" @click="cancelForm">Annuler</button>
        </div>
      </div>
  
      <!-- Liste des offres -->
      <div
        class="offer-card"
        v-for="(offer, index) in offers"
        :key="index"
      >
        <h3 class="title-offre">{{ offer.title }}</h3>
        <p class="description">{{ offer.description }}</p>
  
        <div class="card-actions">
          <button class="btn-see-more" @click="openPopup(index)">Voir plus</button>
          <button class="btn-action" @click="editOffer(index)">Modifier</button>
          <button class="btn-action btn-delete" @click="deleteOffer(index)">Supprimer</button>
        </div>
  
        <!-- Modal -->
        <Teleport to="body">
          <div v-if="activePopup === index" class="modal-overlay" @click.self="closePopup">
            <div class="modal-content">
              <button class="close-button" @click="closePopup">×</button>
              <Authentification :isInModal="true" />
            </div>
          </div>
        </Teleport>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import Authentification from './Authentification.vue';
  
  const activePopup = ref(-1);
  const showForm = ref(false);
  const isEditing = ref(false);
  const editIndex = ref(-1);
  
  const offers = ref([
    { title: 'Website Design for SCFC Canada', description: 'Born out of a vision...' },
    { title: 'Shipping Platform Redesign', description: 'We aim to simplify logistics...' },
  ]);
  
  const form = ref({ title: '', description: '' });
  
  function openPopup(index) {
    activePopup.value = index;
    document.body.style.overflow = 'hidden';
  }
  
  function closePopup() {
    activePopup.value = -1;
    document.body.style.overflow = '';
  }
  
  function openAddForm() {
    showForm.value = true;
    isEditing.value = false;
    form.value = { title: '', description: '' };
  }
  
  function editOffer(index) {
    showForm.value = true;
    isEditing.value = true;
    editIndex.value = index;
    form.value = { ...offers.value[index] };
  }
  
  function saveOffer() {
    if (!form.value.title || !form.value.description) return;
    if (isEditing.value) {
      offers.value[editIndex.value] = { ...form.value };
    } else {
      offers.value.push({ ...form.value });
    }
    cancelForm();
  }
  
  function deleteOffer(index) {
    if (confirm('Voulez-vous vraiment supprimer cette offre ?')) {
      offers.value.splice(index, 1);
    }
  }
  
  function cancelForm() {
    showForm.value = false;
    form.value = { title: '', description: '' };
    isEditing.value = false;
    editIndex.value = -1;
  }
  </script>
  
  <style scoped>
  .add-offer-button {
    text-align: center;
    margin-bottom: 20px;
  }
  
  .offer-form {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .offer-form input,
  .offer-form textarea {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 1rem;
  }
  
  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }
  
  .btn-cancel {
    background: #eee;
    color: #333;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
  }
  </style>