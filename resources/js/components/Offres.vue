<template>
  <div class="offers-section">
    <h2 class="title">Nos offres</h2>

    <div class="offer-card">
      <h3 class="title-offre">Website Design for SCFC Canada</h3>
      <p class="description">
        Born out of a vision, a single-minded objective that puts service before anything
        else, Swift Clearance and Forwarding Corp. surging forth to deliver the best
        services in the shipping and logistics scenario.
      </p>
      <div>
        <!-- Voir plus button to open the modal -->
        <button class="btn-see-more" @click="showModal = true">Voir plus</button>
      </div>
    </div>

    <!-- Modal with authentication form -->
    <div v-if="showModal" class="modal">
      <div class="modal-content">
        <!-- Close button "×" -->
        <!-- Close button "×" -->
        <span class="close" @click="closeModal">×</span>
        <!-- Authentification Form -->
        <Authentification @close="closeModal" />
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import Authentification from "./Authentification.vue";

// Variable pour contrôler l'affichage du modal
const showModal = ref(false);

// Fonction pour fermer la modal
function closeModal() {
  showModal.value = false;
}
</script>
<style scoped>
/* ===== Section des Offres ===== */
.offers-section {
  padding: 40px 20px;
  max-width: 1200px;
  margin: 0 auto;
  text-align: center;
  background-color: #f8f9fa;
  border-radius: 12px;
}

.title {
  font-size: 2.2rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 40px;
  text-transform: uppercase;
}

.offer-card {
  background-color: #fff;
  border-radius: 10px;
  padding: 30px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  margin-bottom: 30px;
  position: relative;
}

.offer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.title-offre {
  font-size: 1.8rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 20px;
}

.description {
  font-size: 1rem;
  color: #7f8c8d;
  line-height: 1.6;
  margin-bottom: 20px;
  text-align: justify;
}

.btn-see-more {
  padding: 12px 25px;
  background-color: #2980b9;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1.1rem;
  transition: background-color 0.3s ease, transform 0.3s ease;
  font-weight: 600;
  letter-spacing: 1px;
}

.btn-see-more:hover {
  background-color: #3498db;
  transform: scale(1.05);
}

.btn-see-more:focus {
  outline: none;
  box-shadow: 0 0 5px rgba(41, 128, 185, 0.4);
}

/* ===== Modal Styles ===== */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 0px;
}

.modal-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 12px;
  width: 80%;
  max-width: 650px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.close {
  font-size: 2rem;
  color: #e74c3c;
  cursor: pointer;
  position: absolute;
  top: 10px;
  right: 400px;
}

.close:hover {
  color: #c0392b;
}

/* Auth Form Styles */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: transparent;

  padding: 20px;
  box-sizing: border-box;
  flex-direction: column;
}

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

.auth-box:hover {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
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

/* Form Styles */
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
  padding: 14px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  color: #333;
  background-color: #fafafa;
  transition: border-color 0.3s ease;
}

.input-group input:focus {
  border-color: #2980b9;
  outline: none;
  box-shadow: 0 0 10px rgba(41, 128, 185, 0.2);
}

.btn-submit {
  width: 100%;
  padding: 14px;
  background-color: #2980b9;
  color: white;
  font-size: 1.1rem;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  transition: background 0.3s ease;
}

.btn-submit:hover {
  background-color: #1f6690;
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
  text-decoration: none;
  transition: background 0.3s ease;
}

.btn-create:hover {
  background-color: #1f6690;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .offers-section {
    padding: 20px;
  }

  .offer-card {
    padding: 15px;
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

  .modal-content {
    padding: 20px;
    width: 90%;
  }

  .auth-title {
    font-size: 1.6rem;
  }

  .input-group input {
    padding: 12px;
  }

  .btn-submit {
    padding: 12px;
  }

  .btn-create {
    padding: 12px;
  }
}
</style>
