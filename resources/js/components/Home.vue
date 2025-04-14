<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import home from "../../assets/recrutementapp.png";
import candidat from "../../assets/candidat.jpg";
import recruteur from "../../assets/recruteur.jpg";

const showPopup = ref(false);
const isRecruteur = ref("");
const router = useRouter();

function togglePopup() {
  showPopup.value = true;
}

const closePopup = () => {
  showPopup.value = false;
  isRecruteur.value = "";
};

const continueAs = () => {
  if (isRecruteur.value === "candidat") {
    router.push("/RegisterCandidat");
  } else if (isRecruteur.value === "recruteur") {
    router.push("/RegisterRecruteur");
  }
};
</script>

<template>
  <div class="hero-section">
    <div class="text-content">
      <h1>
        Trouvez votre future <br />
        <span class="emploi">emploi</span>
      </h1>
      <p class="description">
        Explorez nos offres d’emploi, créez votre CV ou déposez-le directement pour
        commencer votre carrière.
      </p>
      <button @click="togglePopup" class="btn">Commencer par ici</button>

      <!-- ✅ POPUP -->
      <div v-if="showPopup" class="popup-overlay">
        <div class="popup-box" role="dialog" aria-modal="true">
          <button class="close-btn" @click="closePopup" aria-label="Fermer la fenêtre">×</button>
          <p class="popup-title">Vous êtes :</p>

          <div class="radio-group-horizontal" role="radiogroup" aria-label="Choix de type d'utilisateur">
            <!-- Option Candidat -->
            <label
              class="radio-option"
              :class="{ active: isRecruteur === 'candidat' }"
              :aria-checked="isRecruteur === 'candidat'"
              role="radio"
              tabindex="0"
            >
              <img class="role-img" :src="candidat" alt="Icône représentant un candidat" />
              <input type="radio" value="candidat" v-model="isRecruteur" />
              <span>Candidat</span>
            </label>

            <!-- Option Recruteur -->
            <label
              class="radio-option"
              :class="{ active: isRecruteur === 'recruteur' }"
              :aria-checked="isRecruteur === 'recruteur'"
              role="radio"
              tabindex="0"
            >
              <img class="role-img" :src="recruteur" alt="Icône représentant un recruteur" />
              <input type="radio" value="recruteur" v-model="isRecruteur" />
              <span>Recruteur</span>
            </label>
          </div>

          <button class="btn-continue" :disabled="!isRecruteur" @click="continueAs">
            Continuer
          </button>
        </div>
      </div>
    </div>

    <div class="image-content">
      <img class="recrut-img" :src="home" alt="Illustration de recrutement" />
    </div>
  </div>
</template>

<style scoped>
/* ===== Hero Section ===== */
.hero-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  align-items: center;
  gap: 3rem;
  max-width: 1440px;
  margin: 0 auto;
  padding: 0;
}

.text-content {
  padding: clamp(2rem, 5vw, 4rem);
}

h1 {
  color: #1a202c;
  font-size: clamp(2.5rem, 5vw, 3.75rem);
  line-height: 1.2;
  font-weight: 800;
  font-family: "Inter", sans-serif;
  margin-bottom: 4rem;
  margin-top: 2rem;
}

.emploi {
  color: #3d63ea;
}

.description {
  font-size: 1.125rem;
  color: #4a5568;
  margin-bottom: 3rem;
  line-height: 1.7;
  max-width: 600px;
}

.btn {
  background: linear-gradient(90deg, #3d63ea, #506df5);
  color: white;
  padding: 16px 40px;
  border: none;
  border-radius: 999px;
  font-size: 1.2rem;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s ease;
  box-shadow: 0 8px 20px rgba(61, 99, 234, 0.2);
}

.btn:hover {
  background: linear-gradient(90deg, #506df5, #6582eb);
  transform: scale(1.05);
}

.recrut-img {
  width: 100%;
  height: 100%;
  max-width: 600px;
  max-height: 400px;
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
  transition: transform 0.3s ease;
}

.recrut-img:hover {
  transform: scale(1.05);
}

/* ===== POPUP ===== */
.popup-overlay {
  position: fixed;
  inset: 0;

  background-color: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  animation: fadeIn 0.3s ease;
}

.popup-box {
  background-color: #f8faff;
  padding: 3rem 2.5rem;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  width: 500px;
  position: relative;
  animation: scaleIn 0.3s ease;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.popup-title {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 2rem;
  color: #14326f;
  text-align: center;
}

.radio-group-horizontal {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.radio-option {
  flex: 1 1 45%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  font-size: 18px;
  color: #333;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s ease, border-color 0.2s ease;
  text-align: center;
}

.radio-option:hover {
  background: #f8faff;
}

.radio-option.active {
  background-color: #e7edff;
  border-color: #3d63ea;
}

.radio-option input[type="radio"] {
  accent-color: #3d63ea;
  transform: scale(1.4);
  cursor: pointer;
}

.radio-option:focus-within {
  outline: 2px solid #3d63ea;
  outline-offset: 4px;
}

.role-img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
}

.btn-continue {
  margin-top: 2rem;
  background: #3d63ea;
  color: #fff;
  border: none;
  width: 100%;
  padding: 15px;
  border-radius: 999px;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.btn-continue:disabled {
  background: #cbd5e0;
  cursor: not-allowed;
}

.btn-continue:hover:not(:disabled) {
  background: #2f53d6;
}

.close-btn {
  position: absolute;
  top: 12px;
  right: 14px;
  font-size: 26px;
  font-weight: bold;
  color: #555;
  background: none;
  border: none;
  cursor: pointer;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #000;
}

/* Animations */
@keyframes fadeIn {
  from {
    background-color: rgba(0, 0, 0, 0);
  }
  to {
    background-color: rgba(0, 0, 0, 0.45);
  }
}

@keyframes scaleIn {
  from {
    transform: scale(0.95);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

/* Responsive */
@media (max-width: 480px) {
  .radio-group-horizontal {
    flex-direction: column;
    align-items: center;
  }

  .radio-option {
    width: 100%;
  }
}
</style>
