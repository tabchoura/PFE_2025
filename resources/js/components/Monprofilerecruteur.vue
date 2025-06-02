<template>
  <div class="page-wrapper">
    <!-- Vérifie si profile existe avant de rendre l'élément -->
    <div class="profil-container" v-if="profile">
      <h2 class="title">
        👤 Bienvenue {{ profile.prenom ? profile.prenom : "Prénom non disponible" }}
        {{ profile.nom ? profile.nom : "Nom non disponible" }}
      </h2>

      <!-- Affichage des détails du profil -->
      <ul class="profil-details" v-if="!editMode">
        <li><strong>Nom de la Société :</strong> {{ profile.nomsociete }}</li>
        <li><strong>Email :</strong> {{ profile.email }}</li>
        <li><strong>CIN :</strong> {{ profile.cin }}</li>
        <li><strong>Numéro de téléphone :</strong> {{ profile.phone }}</li>
        <li><strong>Localisation de la Société :</strong> {{ profile.localisation }}</li>
        <li><strong>Site Web :</strong> {{ profile.siteweb }}</li>
      </ul>

      <button
        v-if="!editMode"
        class="btn-edit"
        @click="toggleEditMode"
        aria-label="Modifier le profil"
      >
        ✏️ Modifier le profil
      </button>

      <!-- Formulaire d'édition -->
      <form v-if="editMode" @submit.prevent="updateProfile" class="edit-form">
        <div class="form-grid">
          <!-- Champ Nom -->
          <div
            class="input-group"
            @mouseenter="showHint('nom')"
            @mouseleave="hideHint('nom')"
          >
            <label for="nom">Nom</label>
            <input
              type="text"
              v-model.trim="formData.nom"
              id="nom"
              placeholder="Entrez votre nom"
              :class="{ 'input-error': errors.nom, 'input-valid': validFields.nom }"
              autocomplete="family-name"
              @input="validateField('nom')"
            />
            <span class="error-message" v-if="errors.nom">{{ errors.nom }}</span>
            <span class="valid-icon" v-if="formData.nom && validFields.nom">✓</span>
          </div>

          <!-- Champ Prénom -->
          <div
            class="input-group"
            @mouseenter="showHint('prenom')"
            @mouseleave="hideHint('prenom')"
          >
            <label for="prenom">Prénom</label>
            <input
              type="text"
              v-model.trim="formData.prenom"
              id="prenom"
              placeholder="Entrez votre prénom"
              :class="{ 'input-error': errors.prenom, 'input-valid': validFields.prenom }"
              autocomplete="given-name"
              @input="validateField('prenom')"
            />
            <span class="error-message" v-if="errors.prenom">{{ errors.prenom }}</span>
            <span class="valid-icon" v-if="formData.prenom && validFields.prenom">✓</span>
          </div>
        </div>

        <!-- Champ Email -->
        <div
          class="input-group"
          @mouseenter="showHint('email')"
          @mouseleave="hideHint('email')"
        >
          <label for="email">Email</label>
          <input
            type="email"
            v-model.trim="formData.email"
            id="email"
            placeholder="Entrez votre email"
            :class="{ 'input-error': errors.email, 'input-valid': validFields.email }"
            autocomplete="email"
            @input="validateField('email')"
          />
          <span class="error-message" v-if="errors.email">{{ errors.email }}</span>
          <span class="valid-icon" v-if="formData.email && validFields.email">✓</span>
        </div>

        <!-- Champ CIN -->
        <div
          class="input-group"
          @mouseenter="showHint('cin')"
          @mouseleave="hideHint('cin')"
        >
          <label for="cin">CIN</label>
          <input
            type="text"
            v-model.trim="formData.cin"
            id="cin"
            placeholder="Entrez votre CIN"
            maxlength="8"
            inputmode="numeric"
            :class="{ 'input-error': errors.cin, 'input-valid': validFields.cin }"
            @input="validateField('cin')"
          />
          <span class="error-message" v-if="errors.cin">{{ errors.cin }}</span>
          <span class="valid-icon" v-if="formData.cin && validFields.cin">✓</span>
        </div>

        <!-- Champ Téléphone -->
        <div
          class="input-group"
          @mouseenter="showHint('phone')"
          @mouseleave="hideHint('phone')"
        >
          <label for="phone">Numéro de téléphone</label>
          <input
            type="tel"
            v-model.trim="formData.phone"
            id="phone"
            placeholder="Votre numéro de téléphone"
            maxlength="8"
            inputmode="tel"
            :class="{ 'input-error': errors.phone, 'input-valid': validFields.phone }"
            @input="validateField('phone')"
          />
          <span class="error-message" v-if="errors.phone">{{ errors.phone }}</span>
          <span class="valid-icon" v-if="formData.phone && validFields.phone">✓</span>
        </div>

        <!-- Champ Nom de l'entreprise -->
        <div
          class="input-group"
          @mouseenter="showHint('nomsociete')"
          @mouseleave="hideHint('nomsociete')"
        >
          <label for="nomsociete">Nom de l'entreprise</label>
          <input
            type="text"
            v-model="formData.nomsociete"
            id="nomsociete"
            placeholder="Écrire votre nom de l'entreprise"
            :class="{
              'input-error': errors.nomsociete,
              'input-valid': validFields.nomsociete,
            }"
            @input="validateField('nomsociete')"
          />
          <span class="error-message" v-if="errors.nomsociete">{{
            errors.nomsociete
          }}</span>
          <span class="valid-icon" v-if="formData.nomsociete && validFields.nomsociete"
            >✓</span
          >
        </div>
        <div
          class="input-group"
          @mouseenter="showHint('Localisation')"
          @mouseleave="hideHint('Localisation')"
        >
          <label for="localisation">Localisation</label>
          <input
            type="text"
            v-model="formData.localisation"
            id="localisation"
            placeholder="Écrire votre localisation"
            :class="{
              'input-error': errors.localisation,
              'input-valid': validFields.localisation,
            }"
            @input="validateField('localisation')"
          />
          <span class="error-message" v-if="errors.localisation">{{
            errors.localisation
          }}</span>
          <span
            class="valid-icon"
            v-if="formData.localisation && validFields.localisation"
            >✓</span
          >
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">💾 Enregistrer</button>
          <button type="button" class="btn-cancel" @click="toggleEditMode">
            ❌ Annuler
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
const router = useRouter();
const toast = useToast();
// Profil utilisateur
const profile = ref({
  nom: "", // Assurez-vous que 'nom' est bien initialisé
  prenom: "",
  email: "",
  date_naissance: "",
  lieudenaissance: "",
  cin: "",
  phone: "",
  nomsociete: "",
  localisation: "",
  siteweb: "",
});

// Formulaire d'édition
const formData = ref({});
const editMode = ref(false);
const errors = ref({});
const validFields = ref({});

// Toggle mode édition
function toggleEditMode() {
  if (!editMode.value) {
    formData.value = { ...profile.value };
  }
  editMode.value = !editMode.value;
}

// Charger profil API
async function loadProfileData() {
  try {
    await axios.get("/sanctum/csrf-cookie");
    const { data: user } = await axios.get("/api/me");

    if (user) {
      profile.value = {
        nom: user.nom || "",
        prenom: user.prenom || "",
        nomsociete: user.nomsociete || "",
        email: user.email || "",
        cin: user.cin || "",
        phone: user.phone || "",
        localisation: user.localisation || "",
        siteweb: user.siteweb || "",
      };
      formData.value = { ...profile.value };
    } else {
      throw new Error("Aucune donnée utilisateur reçue");
    }
  } catch (err) {
    console.error("Erreur chargement profil:", err);
    alert("Erreur lors du chargement du profil.");
    if (err.response?.status === 401) {
      alert("Session expirée. Connectez-vous à nouveau.");
      router.push("/login");
    }
  }
}

// Mettre à jour profil
async function updateProfile() {
  if (!formData.value.nom || !formData.value.prenom || !formData.value.email) {
    alert("❌ Nom, prénom et email obligatoires.");
    return;
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(formData.value.email)) {
    alert("❌ Email invalide.");
    return;
  }
  try {
    await axios.put("/api/update-profil", formData.value);
    profile.value = { ...formData.value };
    const key = sessionStorage.getItem("userSession")
      ? "userSession"
      : localStorage.getItem("userSession")
      ? "userSession"
      : null;
    if (key) {
      const store = key === "userSession" ? sessionStorage : localStorage;
      const stored = JSON.parse(store.getItem(key) || "{}");
      store.setItem(key, JSON.stringify({ ...stored, ...formData.value }));
    }
    editMode.value = false;
    toast.success("profile mis à jour ");
  } catch (err) {
    console.error("Erreur mise à jour:", err);
    toast.error("Erreur lors du mis a jour");
  }
}

onMounted(loadProfileData);
</script>

<style scoped>
.page-wrapper {
  background: linear-gradient(135deg, #f0f7ff, #e6f0ff);
  min-height: 100vh;
  padding: 2rem;
}

.profil-container:hover {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 20px 40px rgba(0, 0, 0, 0.15);
}
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

/* ===== Détails du profil ===== */
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
.profil-details li::before {
  content: "•";
  position: absolute;
  left: 0;
  color: #2563eb;
  font-size: 1.2rem;
  line-height: 1;
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

/* ===== Bouton Modifier ===== */
/* === BUTTONS === */
.btn-edit {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem;
  width: 100%;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  transition: filter 0.2s ease;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 1.5rem;
  box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
}

.btn-edit:hover {
  filter: brightness(0.8f);
}

/* ===== Formulaire d’édition ===== */
.edit-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
}

/* ===== Input groups ===== */
.input-group {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
label {
  font-weight: 500;
  color: #1f2937;
}
input {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.3s, box-shadow 0.3s;
}
input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
}
.input-valid {
  border-color: #16a34a !important;
}
.input-error {
  border-color: #ef4444 !important;
  background-color: rgba(239, 68, 68, 0.05);
}

/* ===== Icônes de validation & messages d’erreur ===== */
.valid-icon {
  position: absolute;
  top: 50%;
  right: 1rem;
  transform: translateY(-50%);
  color: #16a34a;
}
.error-message {
  color: #ef4444;
  font-size: 0.85rem;
}

/* ===== Toggle mot de passe ===== */
.password-input-container {
  position: relative;
}
.toggle-password {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
}

/* ===== Actions du formulaire ===== */
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
  filter: brightness(0.8);
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
}

/* ===== Notification de succès ===== */
.success-notification {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #16a34a;
  color: #fff;
  padding: 1rem 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.3s, transform 0.3s;
  z-index: 1000;
}
.success-notification.show {
  opacity: 1;
  transform: translateY(0);
}

/* ===== Animations ===== */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* ===== Responsive ===== */
@media (max-width: 640px) {
  .profil-container {
    padding: 2rem;
    margin: 2rem 1rem;
  }
  .form-grid {
    grid-template-columns: 1fr;
  }
  .form-actions {
    flex-direction: column;
  }
}
</style>
