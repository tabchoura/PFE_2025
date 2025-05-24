<template>
  <div class="page-wrapper">
    <div class="profil-container">
      <h2 class="title">👤 Bienvenue {{ profile.prenom }} {{ profile.nom }}</h2>

      <ul class="profil-details" v-if="!editMode">
        <li><strong>Nom de la Société :</strong> {{ profile.nomsociete }}</li>
        <li><strong>Département de la Société :</strong> {{ profile.departement }}</li>
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
      <form v-if="editMode" @submit.prevent="updateProfile" class="edit-form">
        <div class="form-grid">
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

        <div
          class="input-group"
          @mouseenter="showHint('password')"
          @mouseleave="hideHint('password')"
        >
          <label for="password">Mot de passe</label>
          <div class="password-input-container">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model.trim="formData.password"
              id="password"
              placeholder="Entrez votre mot de passe"
              :class="{
                'input-error': errors.password,
                'input-valid': validFields.password,
              }"
              autocomplete="new-password"
              @input="validateField('password')"
            />
            <button
              type="button"
              class="toggle-password"
              @click="togglePassword"
              :aria-label="
                showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'
              "
            >
              <span aria-hidden="true">{{ showPassword ? "🔒" : "👁️" }}</span>
            </button>
          </div>
          <span class="error-message" v-if="errors.password">{{ errors.password }}</span>
        </div>

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
          @mouseenter="showHint('departement')"
          @mouseleave="hideHint('departement')"
        >
          <label for="departement">Département de la Société</label>
          <input
            type="text"
            v-model="formData.departement"
            id="departement"
            placeholder="Département de la société"
            :class="{
              'input-error': errors.departement,
              'input-valid': validFields.departement,
            }"
            @input="validateField('departement')"
          />
          <span class="error-message" v-if="errors.departement">{{
            errors.departement
          }}</span>
          <span class="valid-icon" v-if="formData.departement && validFields.departement"
            >✓</span
          >
        </div>

        <div
          class="input-group"
          @mouseenter="showHint('localisation')"
          @mouseleave="hideHint('localisation')"
        >
          <label for="localisation">Localisation de la Société</label>
          <input
            type="text"
            v-model="formData.localisation"
            id="localisation"
            placeholder="Localisation de la société"
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

        <div
          class="input-group"
          @mouseenter="showHint('siteweb')"
          @mouseleave="hideHint('siteweb')"
        >
          <label for="siteweb">Site web</label>
          <input
            type="text"
            v-model="formData.siteweb"
            id="siteweb"
            placeholder="Écrire votre site web"
            :class="{ 'input-error': errors.siteweb, 'input-valid': validFields.siteweb }"
            @input="validateField('siteweb')"
          />
          <span class="error-message" v-if="errors.siteweb">{{ errors.siteweb }}</span>
          <span class="valid-icon" v-if="formData.siteweb && validFields.siteweb">✓</span>
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
import { useRouter } from "vue-router";

const router = useRouter();
// Profil utilisateur
const profile = ref({
  nom: "",
  prenom: "",
  email: "",
  date_naissance: "",
  lieudenaissance: "",
  cin: "",
  phone: "",
});
// Formulaire d'édition
const form = ref({});
const editMode = ref(false);

// Toggle mode édition
function toggleEditMode() {
  if (!editMode.value) {
    form.value = { ...profile.value };
  }
  editMode.value = !editMode.value;
}

// Charger profil API
async function loadProfileData() {
  try {
    await axios.get("/sanctum/csrf-cookie");
    const { data: user } = await axios.get("/api/me");
    profile.value = {
      nomsociete: user.nomsociete || "",
      departement: user.departement || "",
      email: user.email || "",
      cin: user.cin || "",
      phone: user.phone || "",
      localisation: user.localisation || "",
      siteweb: user.siteweb || "",
    };
    form.value = { ...profile.value };
  } catch (err) {
    console.error("Erreur chargement profil:", err);
    if (err.response?.status === 401) {
      alert("Session expirée. Connectez-vous à nouveau.");
      router.push("/login");
    } else {
      alert("Erreur lors du chargement du profil.");
    }
  }
}

// Mettre à jour profil
async function updateProfile() {
  if (!form.value.nom || !form.value.prenom || !form.value.email) {
    alert("❌ Nom, prénom et email obligatoires.");
    return;
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(form.value.email)) {
    alert("❌ Email invalide.");
    return;
  }
  try {
    await axios.put("/api/update-profil", form.value);
    profile.value = { ...form.value };
    // Mise à jour session
    const key = sessionStorage.getItem("userSession")
      ? "userSession"
      : localStorage.getItem("userSession")
      ? "userSession"
      : null;
    if (key) {
      const store = key === "userSession" ? sessionStorage : localStorage;
      const stored = JSON.parse(store.getItem(key) || "{}");
      store.setItem(key, JSON.stringify({ ...stored, ...form.value }));
    }
    editMode.value = false;
    showSuccessMessage("Profil mis à jour avec succès !");
  } catch (err) {
    console.error("Erreur mise à jour:", err);
    alert("❌ Échec de la mise à jour.");
  }
}

// Format date FR
function formatDate(dateStr) {
  if (!dateStr) return "Non renseigné";
  try {
    return new Intl.DateTimeFormat("fr-FR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    }).format(new Date(dateStr));
  } catch {
    return dateStr;
  }
}

// Notification succès
function showSuccessMessage(msg) {
  const el = document.createElement("div");
  el.className = "success-notification";
  el.textContent = "✅ " + msg;
  document.body.appendChild(el);
  setTimeout(() => el.classList.add("show"), 100);
  setTimeout(() => {
    el.classList.remove("show");
    setTimeout(() => document.body.removeChild(el), 500);
  }, 3000);
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
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}
.btn-cancel,
.btn-save {
  flex: 1;
  padding: 0.75rem;
  font-weight: 600;
  border-radius: 0.75rem;
  transition: background 0.3s, transform 0.2s;
}
.btn-cancel {
  background: #f3f4f6;
  color: #4b5563;
  border: 1px solid #d1d5db;
}
.btn-cancel:hover {
  background: #e5e7eb;
  transform: translateY(-2px);
}
.btn-save {
  background: #16a34a;
  color: #fff;
  border: none;
}
.btn-save:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
}
.btn-save:not(:disabled):hover {
  background: #15803d;
  transform: translateY(-2px);
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
