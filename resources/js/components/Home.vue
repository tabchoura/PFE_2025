<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import home from "../../assets/home.jpg"; // Image commune pour l'illustration générale
import candidat from "../../assets/candidat.jpg"; // Image spécifique pour "candidat"
import recruteur from "../../assets/recruteur.jpg";
const showPopup = ref(false);
const selectedRole = ref("");
const router = useRouter();

const togglePopup = () => {
  showPopup.value = true;
};

const closePopup = () => {
  showPopup.value = false;
  selectedRole.value = "";
};

const continueAs = () => {
  if (selectedRole.value === "candidat") {
    router.push("/RegisterCandidat");
  } else if (selectedRole.value === "recruteur") {
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
        <div class="popup-box">
          <button class="close-btn" @click="closePopup">×</button>
          <p class="popup-title">Vous êtes :</p>

          <!-- Option Candidat -->
          <label class="radio-option" :class="{ active: selectedRole === 'candidat' }">
            <img class="role-img" :src="candidat" alt="illustration candidat" />
            <input type="radio" value="candidat" v-model="selectedRole" />
            <span>Candidat</span>
          </label>

          <!-- Option Recruteur -->
          <label class="radio-option" :class="{ active: selectedRole === 'recruteur' }">
            <img class="role-img" :src="recruteur" alt="illustration recruteur" />
            <input type="radio" value="recruteur" v-model="selectedRole" />
            <span>Recruteur</span>
          </label>

          <button class="btn-continue" :disabled="!selectedRole" @click="continueAs">
            Continuer
          </button>
        </div>
      </div>
    </div>

    <div class="image-content">
      <img class="recrut-img" :src="home" alt="illustration recrutement" />
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
  padding: clamp(3rem, 6vw, 5rem);
  max-width: 1440px;
  margin: 0 auto;
}

h1 {
  color: #1a202c;
  font-size: clamp(2.5rem, 5vw, 3.75rem);
  line-height: 1.2;
  font-weight: 800;
  font-family: "Inter", sans-serif;
  margin-bottom: 4rem;
}
.recrut-img {
  width: 100%; /* Utiliser toute la largeur disponible */
  max-width: 500px; /* Agrandir à 700px max */
  height: auto;
  object-fit: contain;
  border-radius: 20px;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15); /* Ombre ajoutée */
  aspect-ratio: 4 / 3;
  transition: transform 0.3s ease;
}

.recrut-img:hover {
  transform: scale(1.05); /* Légère mise à l'échelle au survol */
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

/* ===== POPUP ===== */
.popup-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  animation: fadeIn 0.3s ease;
}

.popup-box {
  background: #fff;
  padding: 3rem 2.5rem; /* Augmenter le padding */
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  width: 400px;
  max-width: 90%;
  position: relative;
  animation: scaleIn 0.3s ease;
}

.popup-title {
  font-size: 24px; /* Agrandir le titre */
  font-weight: 700;
  margin-bottom: 2rem;
  color: #1a202c;
}

.radio-option {
  display: flex;
  align-items: center;
  gap: 15px; /* Augmenter l'espace entre l'image et le texte */
  margin-bottom: 1.5rem;
  font-size: 18px; /* Agrandir la taille du texte */
  color: #333;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 12px;
  cursor: pointer;
  transition: background 0.2s ease, border-color 0.2s ease;
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
  transform: scale(1.4); /* Agrandir le bouton radio */
  cursor: pointer;
}

.role-img {
  width: 100px; /* Agrandir les images */
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
}

.btn-continue {
  margin-top: 2rem; /* Augmenter l'espace entre le bouton et les options */
  background: #3d63ea;
  color: #fff;
  border: none;
  width: 100%;
  padding: 15px;
  border-radius: 999px;
  font-weight: bold;
  font-size: 16px; /* Agrandir le texte du bouton */
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
  font-size: 26px; /* Agrandir le bouton de fermeture */
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
</style>
