<template>
  <div class="page-wrapper">
    <div class="offers-container">
      <div class="offers-section">
        <div class="header-actions">
          <h2>Liste des offres</h2>
          <button
            @click="ajouterOffre"
            class="btn-ajouter"
            aria-label="Ajouter une nouvelle offre"
          >
            <span class="btn-icon">＋</span> Ajouter une offre
          </button>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Chargement des offres...</p>
        </div>
        <div v-else-if="error" class="error-state">
          <div class="error-icon">⚠️</div>
          <p>{{ error }}</p>
          <button @click="getOffres" class="btn-retry">Réessayer</button>
        </div>
        <div v-else-if="offres.length === 0" class="empty-state">
          <div class="empty-icon">📋</div>
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
                <div class="detail-item">
                  <span class="detail-label">💰 Salaire :</span>
                  <span class="detail-value">{{ formatSalaire(offer.salaire) }}</span>
                </div>
              </div>
              <div class="offer-details">
                <div class="detail-item">
                  <span class="detail-label">📋 Détails :</span>
                  <span class="detail-value">{{ offer.details || "Non précisé" }}</span>
                </div>
              </div>
            </div>
            <div class="offer-actions">
              <button class="btn-modifier" @click="modifierOffre(offer.id)">
                <span class="btn-icon">✏️</span> Modifier
              </button>
              <button class="btn-supprimer" @click="confirmDelete(offer)">
                <span class="btn-icon">🗑️</span> Supprimer
              </button>
            </div>
          </div>
        </div>

        <div v-if="showModal" class="modal-overlay">
          <div class="modal-card">
            <h2>Confirmer la suppression</h2>
            <p>
              Êtes-vous sûr de vouloir supprimer l’offre
              <strong>"{{ offerToDelete.titre }}"</strong> ?
            </p>
            <div class="modal-actions">
              <button class="btn-supprimer" :disabled="deleting" @click="supprimerOffre">
                <span v-if="deleting">Suppression…</span>
                <span v-else>Confirmer</span>
              </button>
              <button type="button" class="btn btn-cancel" @click="cancelDelete">
                ❌ Annuler
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";

const offres = ref([]);
const loading = ref(true);
const error = ref(null);
const router = useRouter();
const toast = useToast();

const showModal = ref(false);
const offerToDelete = ref(null);
const deleting = ref(false);

const getOffres = async () => {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get("/api/offres");
    offres.value = data;
  } catch (err) {
    error.value = "Erreur lors de la récupération des offres.";
    toast.error(error.value);
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(getOffres);

const ajouterOffre = () => router.push("/ajouteroffre");
const modifierOffre = (id) => router.push(`/modifieroffre/${id}`);

const confirmDelete = (offer) => {
  offerToDelete.value = offer;
  showModal.value = true;
};

const cancelDelete = () => {
  showModal.value = false;
  offerToDelete.value = null;
};

const supprimerOffre = async () => {
  deleting.value = true;
  try {
    await axios.delete(`/api/offres/${offerToDelete.value.id}`);
    offres.value = offres.value.filter((o) => o.id !== offerToDelete.value.id);
    toast.success("Offre supprimée avec succès !");
    cancelDelete();
  } catch (err) {
    console.error("Erreur suppression offre :", err);
    toast.error("La suppression a échoué.");
  } finally {
    deleting.value = false;
  }
};

const truncateText = (text, max) =>
  text && text.length > max ? text.slice(0, max) + "…" : text || "";
const formatSalaire = (s) => s || "Non précisé";
</script>

<style scoped>
.page-wrapper {
  background: linear-gradient(135deg, #f0f7ff, #e6f0ff);
  min-height: 100vh;
  padding: 2rem;
}
.offers-container {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 10px 15px -3px rgba(0, 0, 0, 0.08);
}
.offers-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 10px 15px -3px rgba(0, 0, 0, 0.08);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
  gap: 1.5rem;
}
.header-actions h2 {
  color: #1e3a8a;
  font-size: 2rem;
  font-weight: 700;
  position: relative;
  padding-bottom: 0.5rem;
}
.header-actions h2::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 3rem;
  height: 4px;
  background-color: #1e3a8a;
  border-radius: 4px;
}

.btn-ajouter {
  display: inline-flex;
  align-items: center;
  background: linear-gradient(90deg, #20c599 0%, #2dd9b5 100%);
  color: #fff;
  border: none;
  padding: 0.6rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.3s ease;
}
.btn-ajouter:hover {
  background: linear-gradient(90deg, #1ba47d 0%, #1fd0a5 100%);
}
.btn-icon {
  margin-right: 0.5rem;
  font-size: 1.1rem;
}

.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 2rem;
  color: #4b5563;
}
.loading-spinner {
  width: 3rem;
  height: 3rem;
  border: 4px solid #e5e7eb;
  border-top-color: #4f46e5;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.error-icon,
.empty-icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}
.btn-retry {
  margin-top: 1rem;
  background-color: #4f46e5;
  color: #fff;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  cursor: pointer;
}
.btn-retry:hover {
  background-color: #4338ca;
}

.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.5rem;
}

.btn-cancel {
  padding: 0.875rem 1.5rem;
  background: #f1f5f9;
  color: #475569;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-cancel:hover {
  background: #e2e8f0;
}
.offer-card {
  display: flex;
  flex-direction: column;
  background-color: #fff;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 10px 15px -3px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border-left: 3px solid #3b82f6;
}
.offer-card:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}
.offer-card-header {
  border-bottom: 1px solid #e5e7eb;
  padding: 1rem;
}
.title-offre {
  font-size: 1.25rem;
  color: #031334;
  margin: 0;
}
.offer-card-body {
  flex-grow: 1;
  padding: 1rem;
  color: #4b5563;
  font-size: 0.95rem;
}
.description {
  margin-bottom: 1rem;
}
.offer-details {
  margin-bottom: 0.75rem;
}
.detail-item {
  display: flex;
}
.detail-label {
  font-weight: 500;
  margin-right: 0.5rem;
}
.detail-value {
  flex-grow: 1;
}

.offer-actions {
  display: flex;
  gap: 0.5rem;
  padding: 1rem;
  background-color: #f9fafb;
}
.btn-modifier,
.btn-supprimer {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  padding: 0.6rem 0;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;

  transition: background-color 0.2s;
}
.btn-modifier {
  background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);

  color: #fff;
}
.btn-modifier:hover {
  background-color: #417aa5;
  filter: brightness(0.8);
}
.btn-supprimer {
  background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
  color: #fff;
}
.btn-supprimer:hover {
  filter: brightness(0.8);

  background-color: #dc2626;
}

/* Popup overlay */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-card {
  background: #fff;
  border-radius: 0.75rem;
  padding: 2rem;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  text-align: center;
}
.modal-card h2 {
  margin-bottom: 1rem;
  color: #2c3e50;
}
.modal-card p {
  margin-bottom: 1.5rem;
  color: #4b5563;
}
.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}
.btn-annuler {
  background-color: #95a5a6;
  color: #fff;
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  flex: 1;
  transition: background 0.2s;
}
.btn-annuler:hover {
  background: #7f8c8d;
}
</style>
