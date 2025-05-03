<template>
  <div class="offers-section">
    <div class="header-actions">
      <h2>Liste des offres</h2>
      <button
        @click="ajouterOffre"
        class="btn-ajouter"
        aria-label="Ajouter une nouvelle offre"
      >
        + Ajouter une offre
      </button>
    </div>

    <div v-if="loading" class="loading-state">
      <p>Chargement des offres...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
      <button @click="getOffres" class="btn-retry">Réessayer</button>
    </div>

    <div v-else-if="offres.length === 0" class="empty-state">
      <p>Aucune offre disponible pour le moment</p>
    </div>

    <div v-else class="offers-grid">
      <div class="offer-card" v-for="offer in offres" :key="offer.id">
        <div class="offer-card-header">
          <h3 class="title-offre">{{ offer.titre }}</h3>
        </div>

        <div class="offer-card-body">
          <p class="description">
            <strong>Description :</strong> {{ truncateText(offer.description, 100) }}
          </p>
          <div class="offer-details">
            <p class="salaire">
              <strong>Salaire :</strong> {{ formatSalaire(offer.salaire) }}
            </p>
          </div>
          <div class="offer-details">
            <p class="salaire">
              <strong>Détails :</strong> {{ offer.details || "Non précisé" }}
            </p>
          </div>
        </div>

        <div class="offer-actions">
          <button class="btn-modifier" @click="modifierOffre(offer.id)">
            <span class="icon">✏️</span> Modifier
          </button>
          <button
            class="btn-supprimer"
            @click="supprimerOffre(offer.id)"
            aria-label="Supprimer cette offre"
          >
            <span class="icon">🗑️</span> Supprimer
          </button>
          <!-- <button class="btn-details" @click="ajouterdetails(offer.id)" aria-label="Ajouter les détails de l'offre">
            <span class="icon">🔍</span> Voir les détails
          </button> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const offres = ref([]);
const loading = ref(true);
const error = ref(null);
const router = useRouter();

const getOffres = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await axios.get("/api/offres");
    offres.value = response.data;
  } catch (err) {
    error.value = "Erreur lors de la récupération des offres. Veuillez réessayer.";
    console.error("Erreur lors de la récupération des offres:", err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  getOffres();
});

const ajouterOffre = () => {
  router.push("/ajouteroffre");
};

const modifierOffre = (id) => {
  router.push(`/modifieroffre/${id}`);
};

const supprimerOffre = (id) => {
  router.push(`/supprimeroffre/${id}`);
};
// const ajouterdetails = (id) => {
//   router.push(`/ajouterdetails/${id}`);
// };

// Fonctions utilitaires
const truncateText = (text, maxLength) => {
  if (!text) return "";
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
};

const formatSalaire = (salaire) => {
  if (!salaire) return "Non précisé";
  return salaire;
};
</script>
<style scoped>
:root {
  --primary: #4361ee;
  --primary-light: #4895ef;
  --secondary: #3f37c9;
  --success: #4cc9f0;
  --danger: #f72585;
  --warning: #f8961e;
  --text-dark: #2b2d42;
  --text-light: #555b6e;
  --background: #f9fafb;
  --card-bg: #ffffff;
  --border-radius: 12px;
  --transition-speed: 0.3s;
  --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  --box-shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.offers-section {
  max-width: 1600px;
  margin: 60px auto;
  padding: 40px 30px;
  background-color: var(--background);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  position: relative;
  transition: box-shadow var(--transition-speed) ease;
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
    Ubuntu, sans-serif;
}

.offers-section:hover {
  box-shadow: var(--box-shadow-hover);
}

.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.header-actions h2 {
  margin: 0;
  color: #1a365d;
  font-size: 1.8rem;
  font-weight: 700;
  letter-spacing: -0.5px;
}

.btn-ajouter {
  padding: 10px 18px;
  background-color: #4361ee;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all var(--transition-speed) ease;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
}

.btn-ajouter:hover {
  background-color: #4895ef;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(67, 97, 238, 0.25);
}

.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 60px 40px;
  color: var(--text-light);
  font-size: 1.1rem;
  background-color: rgba(255, 255, 255, 0.8);
  border-radius: var(--border-radius);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
}

.error-state {
  color: var(--danger);
  border-left: 4px solid var(--danger);
}

.btn-retry {
  margin-top: 20px;
  padding: 8px 20px;
  background-color: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all var(--transition-speed) ease;
}

.btn-retry:hover {
  background-color: var(--primary-light);
  transform: translateY(-2px);
}

.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
  justify-content: center;
}

.offer-card {
  background-color: var(--card-bg);
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.04);
  transition: transform var(--transition-speed) ease,
    box-shadow var(--transition-speed) ease;
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.03);
  position: relative;
}

.offer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  border-color: rgba(67, 97, 238, 0.1);
}

.offer-card-header {
  margin-bottom: 15px;
  position: relative;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.04);
}

.title-offre {
  font-size: 1.35rem;
  font-weight: 700;
  color: #1a585d;
  margin: 0;
  letter-spacing: -0.3px;
  line-height: 1.3;
}

.offer-card-body {
  flex-grow: 1;
}

.description {
  font-size: 0.95rem;
  color: var(--text-light);
  margin-bottom: 20px;
  line-height: 1.6;
}

.description strong,
.salaire strong {
  color: var(--text-dark);
  font-weight: 600;
}

.offer-details {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 15px;
  background-color: rgba(0, 0, 0, 0.01);
  padding: 12px;
  border-radius: 8px;
  border-left: 3px solid rgba(67, 97, 238, 0.2);
}

.salaire {
  font-size: 0.95rem;
  color: var(--text-light);
  margin: 0;
  line-height: 1.5;
}

.offer-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
  gap: 12px;
  border-top: 1px solid rgba(0, 0, 0, 0.04);
  padding-top: 15px;
}

.btn-modifier,
.btn-supprimer,
.btn-details {
  padding: 8px 16px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
  border-radius: 8px;
  flex: 1;
  border: none;
}

.btn-modifier {
  background-color: var(--warning);
  color: #fff;
  box-shadow: 0 4px 12px rgba(248, 150, 30, 0.15);
}

.btn-modifier:hover {
  background-color: #f9844a;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(248, 150, 30, 0.25);
}

.btn-supprimer {
  background-color: var(--danger);
  color: white;
  box-shadow: 0 4px 12px rgba(247, 37, 133, 0.15);
}

.btn-supprimer:hover {
  background-color: #ff0a78;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(247, 37, 133, 0.25);
}

.btn-details {
  background-color: var(--success);
  color: white;
  box-shadow: 0 4px 12px rgba(76, 201, 240, 0.15);
}

.btn-details:hover {
  background-color: #25b0d8;
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(76, 201, 240, 0.25);
}

.icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}

/* Modal de confirmation */
.confirmation-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(5px);
}

.modal-content {
  background-color: white;
  padding: 35px;
  border-radius: 16px;
  max-width: 450px;
  width: 90%;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
  transform: translateY(0);
  animation: modalFadeIn 0.3s ease forwards;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 25px;
}

.btn-confirm {
  background-color: var(--danger);
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.25s ease;
}

.btn-confirm:hover {
  background-color: #ff0a78;
  transform: translateY(-2px);
}

.btn-cancel {
  background-color: #94a3b8;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.25s ease;
}

.btn-cancel:hover {
  background-color: #64748b;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .header-actions {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }

  .btn-ajouter {
    width: 100%;
    justify-content: center;
  }

  .offers-grid {
    grid-template-columns: 1fr;
  }

  .offer-card {
    width: 100%;
  }

  .offer-actions {
    flex-direction: column;
  }
}

/* Animation pour l'état de chargement */
.loading-state p {
  position: relative;
  display: inline-block;
}

.loading-state p:after {
  content: "...";
  position: absolute;
  overflow: hidden;
  display: inline-block;
  vertical-align: bottom;
  animation: dots-animation 1.5s infinite;
  width: 0;
}

@keyframes dots-animation {
  0% {
    width: 0;
  }
  33% {
    width: 1em;
  }
  66% {
    width: 2em;
  }
  100% {
    width: 3em;
  }
}

/* Effet de survol supplémentaire pour les cartes */
.offer-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(to right, var(--primary), var(--primary-light));
  transform: scaleX(0);
  transform-origin: left;
  transition: transform var(--transition-speed) ease;
}

.offer-card:hover::before {
  transform: scaleX(1);
}
</style>
