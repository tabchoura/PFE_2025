<template>
  <div class="page-wrapper">
    <div class="profil-container">
      <h2 class="title">👤 Bienvenue {{ profile.prenom }} {{ profile.nom }}</h2>

      <ul class="profil-details" v-if="!editMode">
        <li><strong>Email :</strong> {{ profile.email }}</li>
        <li><strong>CIN :</strong> {{ profile.cin }}</li>

        <li>
          <strong>Date de naissance :</strong> {{ formatDate(profile.date_naissance) }}
        </li>
        <li><strong>Lieu de naissance :</strong> {{ profile.lieudenaissance }}</li>
        <li><strong>Numéro de téléphone :</strong> {{ profile.phone }}</li>
      </ul>

      <button
        v-if="!editMode"
        class="btn-edit"
        @click="toggleEditMode"
        aria-label="Modifier le profil"
      >
        ✏️ Modifier le profil
      </button>

      <form v-if="editMode" @submit.prevent="updateProfile" class="edit-form">
        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" v-model="form.nom" required />
        </div>
        <div class="form-group">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" v-model="form.prenom" required />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" v-model="form.email" required />
        </div>
        <div class="form-group">
          <label for="phone">Téléphone</label>
          <input
            type="tel"
            id="phone"
            v-model="form.phone"
            required
            pattern="[0-9]{8,}"
            title="Numéro de téléphone valide (minimum 8 chiffres)"
          />
        </div>
        <div class="form-group">
          <label for="date_naissance">Date de naissance</label>
          <input type="date" id="date_naissance" v-model="form.date_naissance" required />
        </div>
        <div class="form-group">
          <label for="lieudenaissance">Lieu de naissance</label>
          <input
            type="text"
            id="lieudenaissance"
            v-model="form.lieudenaissance"
            required
          />
        </div>
        <div class="form-group">
          <label for="cin">CIN</label>
          <input
            type="text"
            id="cin"
            v-model="form.cin"
            required
            pattern="[0-9]+"
            title="CIN doit contenir uniquement des chiffres"
          />
        </div>

        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="toggleEditMode">
            ❌ Annuler
          </button>
          <button type="submit" class="btn-save">💾 Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useToast } from "vue-toastification";
const toast = useToast();
// Déclarations des variables réactives
const profile = ref({
  nom: "",
  prenom: "",
  email: "",
  date_naissance: "",
  lieudenaissance: "",
  cin: "",
  phone: "",
});
const form = ref({});
const editMode = ref(false);

// Fonction pour basculer entre mode édition et affichage
function toggleEditMode() {
  if (!editMode.value) {
    // Si nous entrons en mode édition, copier les données du profil dans le formulaire
    form.value = { ...profile.value };
  }
  editMode.value = !editMode.value;
}

// Fonction pour charger les données du profil utilisateur
async function loadProfileData() {
  try {
    await axios.get("/sanctum/csrf-cookie"); // Assurez-vous d'obtenir le cookie CSRF
    const me = await axios.get("/api/me"); // Requête pour charger les données du profil utilisateur

    const user = me.data;
    profile.value = {
      nom: user.nom || "",
      prenom: user.prenom || "",
      email: user.email || "",
      date_naissance: user.date_naissance || "",
      lieudenaissance: user.lieudenaissance || "",
      cin: user.cin || "",
      phone: user.phone || "",
    };

    form.value = { ...profile.value };
  } catch (error) {
    console.error("Erreur lors du chargement des données du profil:", error);

    if (error.response && error.response.status === 401) {
      alert("Votre session a expiré. Veuillez vous reconnecter.");
    } else {
      alert("Une erreur est survenue lors du chargement des données du profil.");
    }
  }
}

// Fonction pour mettre à jour le profil de l'utilisateur
async function updateProfile() {
  if (!form.value.email || !form.value.nom || !form.value.prenom) {
    alert("❌ Veuillez remplir tous les champs obligatoires");
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(form.value.email)) {
    alert("❌ Veuillez entrer un email valide");
    return;
  }

  try {
    profile.value = { ...form.value };

    const userSession = JSON.parse(
      sessionStorage.getItem("userSession") || localStorage.getItem("userSession") || "{}"
    );

    const updatedSession = {
      ...userSession,
      ...form.value,
    };

    if (sessionStorage.getItem("userSession")) {
      sessionStorage.setItem("userSession", JSON.stringify(updatedSession));
    }
    if (localStorage.getItem("userSession")) {
      localStorage.setItem("userSession", JSON.stringify(updatedSession));
    }

    editMode.value = false;
    toast.success("Profil mis à jour avec succès !");
  } catch (error) {
    console.error("Erreur lors de la sauvegarde du profil:", error);
    toast.error("❌ Une erreur est survenue lors de la sauvegarde du profil.");
  }
}

// Fonction pour formater la date
function formatDate(dateString) {
  if (!dateString) return "Non renseigné";
  try {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("fr-FR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    }).format(date);
  } catch (e) {
    return dateString;
  }
}

// Chargement des données du profil au montage du composant
onMounted(() => {
  loadProfileData();
});
</script>
<style scoped>
/* === BASE STYLES === */
.page-wrapper {
  background: linear-gradient(135deg, #f0f7ff, #e6f0ff);
  min-height: 100vh;
  padding: 2rem;
}

.profil-container {
  background: white;
  border-radius: 16px;
  padding: 2.5rem;
  max-width: 1200px;
  margin: 0 auto;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profil-container:hover {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* === TYPOGRAPHY === */
.title {
  color: #1e3a8a; /* bleu foncé proche de la capture */
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  position: relative;
  padding-bottom: 0.5rem;
  letter-spacing: -0.5px;
}

.title::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 3rem; /* largeur de la barre */
  height: 4px; /* épaisseur */
  background-color: #1e3a8a; /* même bleu foncé */
  border-radius: 4px; /* arrondi */
}

/* === PROFILE DETAILS === */
.profil-details {
  list-style: none;
  padding: 0;
  margin: 2rem 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.profil-details li {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border-left: 3px solid #3b82f6;
}

.profil-details strong {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 500;
}

.profil-details span {
  font-size: 1.1rem;
  color: #1e293b;
  font-weight: 500;
}

/* === BUTTONS === */
.btn-edit {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem;
  width: 100%;
  background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 1.5rem;
  box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
}

.btn-edit:hover {
  background: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
}

/* === EDIT FORM === */
.edit-form {
  margin-top: 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.95rem;
  color: #475569;
  font-weight: 500;
}

.form-group input {
  padding: 0.875rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s ease;
  background: #f8fafc;
}

.form-group input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
  background: white;
}

.form-actions {
  grid-column: 1 / -1;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
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

.btn-save {
  padding: 0.875rem 1.5rem;
  background: linear-gradient(135deg, #20c599, #1fae8d, #178467);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3);
}

.btn-save:hover {
  background: #059669;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
}

/* === NOTIFICATION === */
.success-notification {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: white;
  color: #065f46;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 10px 15px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  z-index: 1000;
  border-left: 4px solid #10b981;
  transform: translateY(100px);
  opacity: 0;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.success-notification.show {
  transform: translateY(0);
  opacity: 1;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .profil-container {
    padding: 2rem;
    margin: 0 1rem;
  }

  .profil-details {
    grid-template-columns: 1fr;
  }

  .edit-form {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-cancel,
  .btn-save {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .page-wrapper {
    padding: 1rem;
  }

  .profil-container {
    padding: 1.5rem;
  }

  .title {
    font-size: 1.5rem;
  }
}
</style>
