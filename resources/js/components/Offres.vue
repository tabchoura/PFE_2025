<template>
  <div class="offers-section">

    <!-- Premier offre -->
    <div class="offer-card">
      <h3 class="title-offre">Website Design for SCFC Canada</h3>
      <p class="description">
        Born out of a vision, a single-minded objective that puts service before anything
        else, Swift Clearance and Forwarding Corp. surging forth to deliver the best
        services in the shipping and logistics scenario.
      </p>
      <div>
        <!-- Button to toggle modal visibility -->
        <button class="btn-see-more" @click="openPopup(0)">Voir plus</button>

        <!-- Modal Display -->
        <Teleport to="body">
          <div v-if="activePopup === 0" class="modal-overlay" @click.self="closePopup">
            <div class="modal-content">
              <button class="close-button" @click="closePopup">×</button>
              <Authentification />
            </div>
          </div>
        </Teleport>
      </div>
    </div>

    <!-- Deuxième offre -->
    <div class="offer-card">
      <h3 class="title-offre">Website Design for SCFC Canada</h3>
      <p class="description">
        Born out of a vision, a single-minded objective that puts service before anything
        else, Swift Clearance and Forwarding Corp. surging forth to deliver the best
        services in the shipping and logistics scenario.
      </p>
      <div>
        <!-- Button to toggle modal visibility -->
        <button class="btn-see-more" @click="openPopup(1)">Voir plus</button>

        <!-- Modal Display -->
        <Teleport to="body">
          <div v-if="activePopup === 1" class="modal-overlay" @click.self="closePopup">
            <div class="modal-content">
              <button class="close-button" @click="closePopup">×</button>
              <Authentification />
            </div>
          </div>
        </Teleport>
      </div>
    </div>

    <!-- Troisième offre -->
    <div class="offer-card">
      <h3 class="title-offre">Website Design for SCFC Canada</h3>
      <p class="description">
        Born out of a vision, a single-minded objective that puts service before anything
        else, Swift Clearance and Forwarding Corp. surging forth to deliver the best
        services in the shipping and logistics scenario.
      </p>
      <div>
        <!-- Button to toggle modal visibility -->
        <button class="btn-see-more" @click="openPopup(2)">Voir plus</button>

        <!-- Modal Display -->
        <Teleport to="body">
          <div v-if="activePopup === 2" class="modal-overlay" @click.self="closePopup">
            <div class="modal-content">
              <button class="close-button" @click="closePopup">×</button>
              <Authentification :isInModal="true" />
            </div>
          </div>
        </Teleport>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import Authentification from "./Authentification.vue";

// Variable pour suivre quel popup est actif (-1 signifie aucun)
const activePopup = ref(-1);

// Fonction pour ouvrir un popup spécifique
function openPopup(index: number) {
  activePopup.value = index;
  // Pour empêcher le défilement du corps lorsque le modal est ouvert
  document.body.style.overflow = "hidden";
}

// Fonction pour fermer le popup
function closePopup() {
  activePopup.value = -1;
  // Restaurer le défilement
  document.body.style.overflow = "";
}
</script>

<style scoped>
/* General Styling */
body {
  font-family: 'Arial', sans-serif;
  line-height: 1.6;
  color: #333;
}

/* ================= OFFERS SECTION ================= */
.offers-section {

  display: flex;
  flex-wrap: wrap;
  justify-content: center; /* Center cards horizontally */
  gap: 20px;
  padding: 40px 20px;
  max-width: 1200px;
  margin: 120px auto; /* Add some top/bottom margin */
  text-align: center;
  background-color: #f8f9fa;
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1); /* Reduced shadow intensity */
}

/* Title Styling */

/* Offer Card Styles */
.offer-card {

  background-color: #fff;
  border-radius: 12px;
  padding: 25px; /* Slightly reduced padding */
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Reduced shadow intensity */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  width: calc(33.33% - 20px);
  margin-bottom: 30px; /* Adjusted spacing */
  position: relative;
  animation: slideUp 0.5s ease-in-out;
}

.offer-card:hover {
  transform: translateY(-5px); /* Reduced lift effect */
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); /* Adjusted shadow on hover */
}

/* Offer Title Styling */
.title-offre {
  font-size: 1.6rem; /* Adjusted size */
  font-weight: 600; /* Slightly reduced weight */
  color: #2c3e50;
  margin-bottom: 15px; /* Adjusted spacing */
  letter-spacing: 0.5px; /* Reduced letter spacing */
}

/* Description Styling */
.description {
  font-size: 1rem; /* Adjusted size */
  color: #7f8c8d;
  line-height: 1.5; /* Adjusted line height */
  margin-bottom: 15px; /* Adjusted spacing */
  text-align: justify;
  letter-spacing: 0.3px; /* Reduced letter spacing */
}

/* Button Styling */
.btn-see-more {
  padding: 12px 24px; /* Adjusted padding */
  background-color: #2980b9;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem; /* Adjusted size */
  font-weight: 500; /* Slightly reduced weight */
  letter-spacing: 0.5px; /* Reduced letter spacing */
  transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-see-more:hover {
  background-color: #3498db;
  box-shadow: 0 4px 12px rgba(41, 128, 185, 0.2); /* Adjusted shadow on hover */
  transform: translateY(-2px); /* Subtle lift on hover */
}

.btn-see-more:focus {
  outline: none; /* Remove default focus outline */
  box-shadow: 0 0 6px rgba(41, 128, 185, 0.5); /* Adjusted focus shadow */
}

/* ================= MODAL OVERLAY & CONTENT ================= */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.7);
  z-index: 1099;
  overflow: auto; /* Allow scrolling if content overflows */
}

.modal-content {
  background-color: #fff;
  border-radius: 12px;
  max-height: 90vh; /* Increased max height */
  padding: 20px;
  width: 90%;
  max-width: 800px; /* Adjusted max width */
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); /* Adjusted shadow */
  border: 1px solid #ddd;
  text-align: left; /* Adjusted text alignment */
  overflow-y: auto;
  position: relative;
}

/* Close Button */
.close-button {
  position: absolute;
  top: 10px;
  right: 10px;
  background: transparent;
  border: none;
  font-size: 1.5rem; /* Adjusted size */
  color: #e74c3c;
  cursor: pointer;
  z-index: 2;
}

.close-button:hover {
  color: #c0392b;
}

/* ================= AUTHENTICATION STYLES ================= */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f4f6f9;
  padding: 20px;
  box-sizing: border-box;
  text-align: left;
  flex-direction: column;
}

.login-layout {
  display: flex;
  flex-direction: row;
  justify-content: center;
  gap: 30px;
  max-width: 1200px;
  margin: 0 auto;
}

.image-container {
  flex: 1;
  display: flex;
  justify-content: flex-end;
}

.form-container {
  flex: 1;
}

/* ===== Auth Box Styles ===== */
.auth-box {
  background: #fff;
  border-radius: 12px;
  max-width: 480px;
  width: 100%;
  padding: 40px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid #ddd;
  text-align: center;
}

.auth-title {
  font-size: 1.8rem;
  margin-bottom: 15px;
  color: #2c3e50;
  font-weight: 600;
  letter-spacing: 1px;
}

/* Toggle Candidat / Recruteur */
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

/* ===== Form Styles ===== */
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
  box-shadow: 0 0 10px rgba(41, 128, 185, 0.2);
}

.error-input {
  border-color: #e74c3c !important;
}

.error-message {
  color: #e74c3c;
  font-size: 0.9rem;
  margin-top: 5px;
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

.inscrit-title {
  font-size: 1.4rem;
  margin: 24px 0 19px 0;
  font-weight: 600;
  white-space: normal;
  overflow-wrap: break-word;
}

.choix {
  color: #2980b9;
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
  text-decoration: none;
  transition: background 0.3s ease;
}

.btn-create:hover {
  background-color: #1f6690;
}

.img {
  max-width: 100%;
  height: auto;
  object-fit: contain;
  border-radius: 15%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.img:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

/* ================= RESPONSIVE DESIGN ================= */
/* Small screens */
@media (max-width: 768px) {
  .offers-section {
    padding: 30px 10px;
  }

  .title {
    font-size: 1.75rem;
    margin-bottom: 25px;
  }

  .offer-card {
    width: calc(50% - 20px); /* Two cards per row on smaller screens */
    padding: 20px;
    margin-bottom: 25px;
  }

  .title-offre {
    font-size: 1.4rem;
  }

  .description {
    font-size: 0.9rem;
  }

  .btn-see-more {
    font-size: 0.9rem;
    padding: 10px 20px;
  }

  /* Styles for Authentication */
  .login-layout {
    flex-direction: column;
    gap: 20px;
  }

  .auth-container {
    padding: 15px;
  }

  .auth-box {
    padding: 25px;
    width: 100%;
    max-width: 100%;
  }

  .auth-title,
  .inscrit-title {
    font-size: 1.5rem;
  }

  .btn-submit,
  .btn-create {
    font-size: 1rem;
    padding: 12px;
  }
}

/* Extra small screens */
@media (max-width: 576px) {
  .offer-card {
    width: 100%; /* One card per row on very small screens */
  }

  .auth-container {
    padding: 10px;
  }

  .auth-box {
    padding: 20px;
  }
}

</style>
