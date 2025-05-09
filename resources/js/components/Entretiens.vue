<template>
  <div class="entretiens">
    <div class="entretiens__header">
      <h2>🗓️ Planifier l'entretien</h2>
      <p>Choisissez la date puis cliquez sur « Envoyer ».</p>
    </div>

    <!-- Calendrier relié au state selectedDate -->
    <div class="calendrier-wrapper">
      <Calendrier v-model="selectedDate" />
    </div>

    <div class="selected-date-display" v-if="selectedDate">
      <div class="selected-date-icon">
        <i class="fas fa-calendar-check"></i>
      </div>
      <div class="selected-date-info">
        <span class="selected-date-label">Date sélectionnée</span>
        <span class="selected-date-value">{{ formatDateTime12h(selectedDate) }}</span>
      </div>
    </div>

    <div class="actions">
      <button class="primary-btn" @click="envoyer" :disabled="loading || !selectedDate">
        <i class="fas fa-paper-plane"></i>
        <span v-if="!loading">Envoyer</span>
        <span v-else class="loading-text">
          <span class="loading-dots"></span>
          Envoi en cours
        </span>
      </button>
    </div>

    <!-- Toast Notifications -->
    <transition name="slide-fade">
      <div v-if="successMessage" class="toast toast--success">
        <div class="toast__icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="toast__content">
          <strong>Entretien enregistré</strong>
          <div>{{ formatDateTime12h(entretienDate) }}</div>
        </div>
        <button class="toast__close" @click="successMessage = false">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </transition>

    <transition name="slide-fade">
      <div v-if="errorMessage" class="toast toast--error">
        <div class="toast__icon">
          <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="toast__content">
          <strong>Erreur</strong>
          <div>{{ errorMessage }}</div>
        </div>
        <button class="toast__close" @click="errorMessage = null">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import Calendrier from "./Calendrier.vue";

const route = useRoute();
const router = useRouter();
const candidatureId = Number(route.params.candidatureId);

const selectedDate = ref(null);
const entretienDate = ref(null);
const successMessage = ref(false);
const errorMessage = ref(null);
const loading = ref(false);

function formatDateTime12h(dateString) {
  if (!dateString) return "—";
  const date = new Date(dateString);
  const options = {
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "numeric",
    minute: "numeric",
    hour12: true,
  };
  return new Intl.DateTimeFormat("fr-FR", options).format(date);
}

async function envoyer() {
  if (!selectedDate.value) {
    errorMessage.value = "Veuillez choisir une date d'entretien.";
    return;
  }

  loading.value = true;
  errorMessage.value = null;
  successMessage.value = false;

  try {
    const { data } = await axios.post(`/api/candidatures/${candidatureId}/entretien`, {
      date_entretien: selectedDate.value,
    });

    entretienDate.value = data.date_entretien;
    successMessage.value = true;
    // Masquer après 5s
    setTimeout(() => (successMessage.value = false), 5000);

    // Optionnel : redirection
    setTimeout(() => {
      router.push({ name: "Candidaturesrecruteur" });
    }, 2000);
  } catch (err) {
    errorMessage.value =
      err.response?.data?.message || "Erreur lors de l'enregistrement de l'entretien.";
    // Masquer après 5s
    setTimeout(() => (errorMessage.value = null), 5000);
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
/* Import des polices */
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

/* Variables */
:root {
  --primary: #2563eb;
  --primary-dark: #1d4ed8;
  --primary-light: #dbeafe;
  --success: #10b981;
  --success-light: #d1fae5;
  --error: #ef4444;
  --error-light: #fee2e2;
  --gray-50: #f9fafb;
  --gray-100: #f3f4f6;
  --gray-200: #e5e7eb;
  --gray-300: #d1d5db;
  --gray-400: #9ca3af;
  --gray-500: #6b7280;
  --gray-600: #4b5563;
  --gray-700: #374151;
  --gray-800: #1f2937;
  --gray-900: #111827;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  --border-radius: 12px;
}

/* Container */
.entretiens {
  max-width: 650px;
  margin: 2.5rem auto;
  padding: 2.5rem;
  background: #fff;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-md);
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  font-family: "Inter", sans-serif;
  border: 1px solid var(--gray-200);
  position: relative;
  overflow: hidden;
}

.entretiens::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 5px;
  background: linear-gradient(90deg, var(--primary), #60a5fa);
  border-radius: 5px 5px 0 0;
}

.entretiens:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
}

/* Header */
.entretiens__header {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.entretiens h2 {
  font-size: 2rem;
  margin: 0;
  color: var(--gray-900);
  font-weight: 700;
  letter-spacing: -0.025em;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.entretiens p {
  margin: 0;
  color: var(--gray-600);
  line-height: 1.6;
  font-size: 1.05rem;
}

/* Calendrier */
.calendrier-wrapper {
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--gray-200);
  background: var(--gray-50);
}

/* Selected Date Display */
.selected-date-display {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: var(--primary-light);
  border-radius: var(--border-radius);
  border: 1px solid #bfdbfe;
  animation: fadeIn 0.3s ease;
}

.selected-date-icon {
  background: var(--primary);
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.selected-date-info {
  display: flex;
  flex-direction: column;
}

.selected-date-label {
  font-size: 0.875rem;
  color: var(--primary);
  font-weight: 500;
}

.selected-date-value {
  font-size: 1.125rem;
  color: var(--gray-800);
  font-weight: 600;
}

/* Actions */
.actions {
  display: flex;
  gap: 1rem;
  margin-top: 0.5rem;
}

/* Button */
.primary-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 0.9rem 2rem;
  font-size: 1.05rem;
  font-weight: 600;
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  color: #fff;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
  transition: all 0.3s ease;
  min-width: 180px;
}

.primary-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, var(--primary-dark), #1e40af);
  box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
  transform: translateY(-3px);
}

.primary-btn:disabled {
  background: linear-gradient(135deg, var(--gray-400), var(--gray-300));
  cursor: not-allowed;
  box-shadow: none;
  opacity: 0.8;
}

.primary-btn i {
  font-size: 1rem;
}

/* Loading */
.loading-text {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.loading-dots {
  position: relative;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: white;
  color: white;
  animation: dot-flashing 1s infinite linear alternate;
  animation-delay: 0.5s;
}

.loading-dots::before,
.loading-dots::after {
  content: "";
  display: inline-block;
  position: absolute;
  top: 0;
}

.loading-dots::before {
  left: -15px;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: white;
  color: white;
  animation: dot-flashing 1s infinite alternate;
  animation-delay: 0s;
}

.loading-dots::after {
  left: 15px;
  width: 10px;
  height: 10px;
  border-radius: 5px;
  background-color: white;
  color: white;
  animation: dot-flashing 1s infinite alternate;
  animation-delay: 1s;
}

@keyframes dot-flashing {
  0% {
    background-color: white;
  }
  50%,
  100% {
    background-color: rgba(255, 255, 255, 0.3);
  }
}

/* Toast */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.55, 0, 0.1, 1);
}

.slide-fade-enter-from {
  transform: translateX(30px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateX(30px);
  opacity: 0;
}

.toast {
  position: fixed;
  top: 1.5rem;
  right: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: #fff;
  border-radius: 12px;
  box-shadow: var(--shadow-md);
  overflow: hidden;
  z-index: 1000;
  min-width: 320px;
  max-width: 450px;
  padding: 0.75rem;
  animation: slideIn 0.3s ease;
}

.toast__icon {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-size: 1.25rem;
}

.toast--success .toast__icon {
  background: var(--success-light);
  color: var(--success);
}

.toast--error .toast__icon {
  background: var(--error-light);
  color: var(--error);
}

.toast__content {
  flex: 1;
  padding: 0.5rem 0;
}

.toast--success .toast__content {
  color: var(--gray-800);
}

.toast--success .toast__content strong {
  color: var(--success);
}

.toast--error .toast__content {
  color: var(--gray-800);
}

.toast--error .toast__content strong {
  color: var(--error);
}

.toast__close {
  background: transparent;
  border: none;
  font-size: 1rem;
  padding: 0.5rem;
  cursor: pointer;
  color: var(--gray-500);
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.toast__close:hover {
  background: var(--gray-100);
  color: var(--gray-800);
}

@keyframes slideIn {
  from {
    transform: translateX(100px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .entretiens {
    padding: 2rem;
    margin: 1.5rem;
    gap: 1.5rem;
  }

  .entretiens h2 {
    font-size: 1.75rem;
  }

  .toast {
    min-width: 280px;
    max-width: calc(100vw - 3rem);
  }
}

@media (max-width: 640px) {
  .entretiens {
    padding: 1.75rem;
    margin: 1rem;
    gap: 1.25rem;
  }

  .primary-btn {
    width: 100%;
    justify-content: center;
    padding: 0.8rem 1.5rem;
  }

  .toast {
    right: 0.5rem;
    left: 0.5rem;
    width: calc(100% - 1rem);
    max-width: none;
  }

  .selected-date-display {
    padding: 0.75rem;
  }

  .selected-date-icon {
    width: 36px;
    height: 36px;
    font-size: 1.125rem;
  }

  .selected-date-value {
    font-size: 1rem;
  }
}

/* Amélioration de l'accessibilité */
.primary-btn:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.5), 0 4px 15px rgba(37, 99, 235, 0.2);
}

.toast__close:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.3);
}
</style>
