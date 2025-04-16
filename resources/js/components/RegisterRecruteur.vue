<template>
  <div class="auth-container">
    <div class="auth-content">
      <div class="image-section">
        <img :src="hiringImage" alt="Recrutement" class="hiring-image" />
      </div>

      <div class="form-section">
        <div v-if="!isSubmitted" class="auth-box login-box">
          <h2 class="auth-title">Inscription Recruteur</h2>
          <form @submit.prevent="register" class="auth-form" novalidate>
            <div class="form-grid">
              <div class="input-group">
                <label for="nom">Nom</label>
                <input
                  type="text"
                  v-model.trim="formData.nom"
                  id="nom"
                  placeholder="Entrez votre nom"
                  :class="{ 'input-error': errors.nom }"
                  required
                />
                <span class="error-message" v-if="errors.nom">{{ errors.nom }}</span>
              </div>
              <div class="input-group">
                <label for="prenom">Prénom</label>
                <input
                  type="text"
                  v-model.trim="formData.prenom"
                  id="prenom"
                  placeholder="Entrez votre prénom"
                  :class="{ 'input-error': errors.prenom }"
                  required
                />
                <span class="error-message" v-if="errors.prenom">{{ errors.prenom }}</span>
              </div>
            </div>

            <div class="input-group">
              <label for="email">Email</label>
              <input
                type="email"
                v-model.trim="formData.email"
                id="email"
                placeholder="Entrez votre email"
                :class="{ 'input-error': errors.email }"
                required
              />
              <span class="error-message" v-if="errors.email">{{ errors.email }}</span>
            </div>

            <div class="input-group">
              <label for="password">Mot de passe</label>
              <div class="password-input-container">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model.trim="formData.password"
                  id="password"
                  placeholder="Entrez votre mot de passe"
                  :class="{ 'input-error': errors.password }"
                  required
                  autocomplete="new-password"
                />
                <button
                  type="button"
                  class="toggle-password"
                  @click="togglePassword"
                  :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                >
                  <span aria-hidden="true">{{ showPassword ? '🔒' : '👁️' }}</span>
                </button>
              </div>
              <span class="error-message" v-if="errors.password">{{ errors.password }}</span>
              <div class="password-strength" v-if="formData.password">
                <div class="strength-bar" :class="passwordStrengthClass"></div>
                <span>{{ passwordStrengthText }}</span>
              </div>
              <p class="password-requirements" v-if="formData.password">
                Le mot de passe doit contenir au moins 8 caractères, incluant majuscules, minuscules,
                chiffres et caractères spéciaux.
              </p>
            </div>

            <div class="form-grid">
              <div class="input-group">
                <label for="cin">CIN</label>
                <input
                  type="text"
                  v-model.trim="formData.cin"
                  id="cin"
                  placeholder="Entrez votre CIN"
                  :class="{ 'input-error': errors.cin }"
                  required
                />
                <span class="error-message" v-if="errors.cin">{{ errors.cin }}</span>
              </div>
              <div class="input-group">
                <label for="numtel">Numéro de téléphone</label>
                <input
                  type="tel"
                  v-model.trim="formData.numtel"
                  id="numtel"
                  placeholder="Votre numéro de téléphone"
                  :class="{ 'input-error': errors.numtel }"
                  required
                />
                <span class="error-message" v-if="errors.numtel">{{ errors.numtel }}</span>
              </div>
            </div>

            <div class="form-grid">
              <div class="input-group">
                <label for="nomentreprise">Nom de l'entreprise</label>
                <input
                  type="text"
                  v-model="formData.nomentreprise"
                  id="nomentreprise"
                  :class="{ 'input-error': errors.nomentreprise }"
                  required
                />
                <span class="error-message" v-if="errors.nomentreprise">{{ errors.nomentreprise }}</span>
              </div>
              <div class="input-group">
                <label for="site">Site</label>
                <input
                  type="url"
                  v-model.trim="formData.site"
                  id="site"
                  placeholder="Écrire votre site web"
                  :class="{ 'input-error': errors.Site }"
                  required
                />
                <span class="error-message" v-if="errors.Site">{{ errors.Site }}</span>
              </div>
            </div>
            <div class="input-group">
  <label for="departement">Département</label>
  <input
    type="text"
    v-model.trim="formData.departement"
    id="departement"
    placeholder="Écrire votre département"
    :class="{ 'input-error': errors.departement }"
    required
  />
  <span class="error-message" v-if="errors.departement">{{ errors.departement }}</span>
</div>

            <div class="input-group">
              <label for="lieu">Localisation</label>
              <input
                type="text"
                v-model.trim="formData.lieu"
                id="lieu"
                placeholder="Écrire votre localisation"
                :class="{ 'input-error': errors.lieu }"
                required
              />
              <span class="error-message" v-if="errors.lieu">{{ errors.lieu }}</span>
            </div>

            <div class="input-group">
              <label for="decription">Description de l'entreprise</label>
              <textarea
                v-model.trim="formData.decription"
                id="decription"
                placeholder="Écrire votre description"
                :class="{ 'input-error': errors.decription }"
                required
              ></textarea>
              <span class="error-message" v-if="errors.decription">{{ errors.decription }}</span>
            </div>

            <button type="submit" class="btn-submit" :disabled="loading">
              <span v-if="loading" class="loading-spinner"></span>
              <span>{{ loading ? 'Traitement en cours...' : "S'inscrire" }}</span>
            </button>
          </form>
        </div>

        <div v-else class="auth-box success-box">
          <h2 class="auth-title">Inscription réussie !</h2>
          <p>Votre compte a été créé avec succès.</p>
          <button @click="goToCompteRecruteur" class="btn-submit">
            Accéder à votre compte
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed, reactive } from 'vue';
import { useRouter } from 'vue-router';
import hiringImage from "../../assets/recruteurhiring.png";

export default defineComponent({
  name: "RegisterRecruteur",
  setup() {
    const router = useRouter();
    const isSubmitted = ref(false);
    const loading = ref(false);
    const showPassword = ref(false);

    const formData = reactive({
      email: "",
      password: "",
      prenom: "",
      nom: "",
      nomentreprise: "",
      lieu: "",
      cin: "",
      numtel: "",
      decription: "",
      site: "",
      departement: "",
    });

    const errors = reactive({});

    const passwordStrength = computed(() => {
      const pwd = formData.password;
      let score = 0;
      if (pwd.length >= 8) score++;
      if (/[a-z]/.test(pwd)) score++;
      if (/[A-Z]/.test(pwd)) score++;
      if (/[0-9]/.test(pwd)) score++;
      if (/[^a-zA-Z0-9]/.test(pwd)) score++;
      return score;
    });

    const passwordStrengthClass = computed(() => {
      const s = passwordStrength.value;
      return s <= 2 ? "weak" : s <= 4 ? "medium" : "strong";
    });

    const passwordStrengthText = computed(() => {
      const s = passwordStrength.value;
      return s <= 2 ? "Faible" : s <= 4 ? "Moyen" : "Fort";
    });

    function togglePassword() {
      showPassword.value = !showPassword.value;
    }

    function validateForm() {
      Object.keys(errors).forEach(k => delete errors[k]);
      let isValid = true;

      const nameRegex = /^[a-zA-ZÀ-ÿ\s'-]+$/;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const urlRegex = /^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/\S*)?$/;

      if (!formData.nom || !nameRegex.test(formData.nom)) {
        errors.nom = "Le nom est requis et doit contenir uniquement des lettres.";
        isValid = false;
      }

      if (!formData.prenom || !nameRegex.test(formData.prenom)) {
        errors.prenom = "Le prénom est requis et doit contenir uniquement des lettres.";
        isValid = false;
      }

      if (!formData.email || !emailRegex.test(formData.email)) {
        errors.email = "Email invalide (il doit contenir '@' et un domaine).";
        isValid = false;
      }

      const pwd = formData.password;
      if (!pwd || pwd.length < 8 || !/[A-Z]/.test(pwd) || !/[a-z]/.test(pwd) || !/[0-9]/.test(pwd) || !/[^a-zA-Z0-9]/.test(pwd)) {
        errors.password = "Mot de passe : 8 caractères, majuscule, minuscule, chiffre, caractère spécial.";
        isValid = false;
      }

      if (!/^\d{8}$/.test(formData.cin)) {
        errors.cin = "Le CIN doit contenir exactement 8 chiffres.";
        isValid = false;
      }

      if (!/^\d{8}$/.test(formData.numtel)) {
        errors.numtel = "Le numéro de téléphone doit contenir exactement 8 chiffres.";
        isValid = false;
      }

      if (!formData.nomentreprise || !nameRegex.test(formData.nomentreprise)) {
        errors.nomentreprise = "Le nom de l'entreprise est requis (lettres uniquement).";
        isValid = false;
      }

      if (!formData.site || !urlRegex.test(formData.site)) {
        errors.Site = "Le site web est invalide. Exemple : https://monsite.com";
        isValid = false;
      }

      if (!formData.departement || !nameRegex.test(formData.departement)) {
        errors.departement = "Le département est requis et doit contenir uniquement des lettres.";
        isValid = false;
      }

      if (!formData.decription || !nameRegex.test(formData.decription)) {
        errors.decription = "La description doit contenir uniquement des lettres.";
        isValid = false;
      }

      if (!formData.lieu) {
        errors.lieu = "La localisation est requise.";
        isValid = false;
      }

      return isValid;
    }

    async function register() {
      if (!validateForm()) return;
      loading.value = true;

      try {
        await new Promise(resolve => setTimeout(resolve, 1500));
        sessionStorage.removeItem("userSession");

        const userData = {
          email: formData.email,
          type: "recruteur",
          lastLogin: new Date().toISOString(),
          nom: formData.nom,
          prenom: formData.prenom,
          cin: formData.cin,
          numtel: formData.numtel,
          nomsociete: formData.nomentreprise,
          numsociete: formData.numtel,
          siteweb: formData.site,
          lieu: formData.lieu,
          description: formData.decription,
          departement: formData.departement,
          password: formData.password
        };

        sessionStorage.setItem("userSession", JSON.stringify(userData));

        const allUsers = JSON.parse(localStorage.getItem("allRecruteurs") || "[]");
        allUsers.push(userData);
        localStorage.setItem("allRecruteurs", JSON.stringify(allUsers));

        loading.value = false;
        isSubmitted.value = true;
      } catch (error) {
        loading.value = false;
        console.error("Erreur d'inscription:", error);
      }
    }

    function goToCompteRecruteur() {
      const sessionData = sessionStorage.getItem("userSession");
      if (!sessionData) {
        const userData = {
          email: formData.email,
          type: "recruteur",
          lastLogin: new Date().toISOString(),
          nom: formData.nom,
          prenom: formData.prenom,
          cin: formData.cin,
          numtel: formData.numtel,
          nomsociete: formData.nomentreprise,
          numsociete: formData.numtel,
          siteweb: formData.site,
          lieu: formData.lieu,
          description: formData.decription,
          departement: formData.departement
        };
        sessionStorage.setItem("userSession", JSON.stringify(userData));
      }

      window.location.href = "/CompteRecruteur";
    }

    return {
      formData,
      errors,
      loading,
      register,
      showPassword,
      togglePassword,
      passwordStrengthClass,
      passwordStrengthText,
      isSubmitted,
      goToCompteRecruteur,
      hiringImage
    };
  }
});
</script>


<style>
/* ===== Variables et styles de base ===== */

/* ===== Auth Container ===== */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #e0ecff, #f4f7fc);
  padding: 1rem;
}.auth-content {
  display: flex;
  flex-direction: row;
  width: 90%;
  max-width: 1200px;
  background-color: #fff;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}
.image-section {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  align-items: flex-start;
  background: linear-gradient(135deg, #f0f8ff, #dceeff);
  border-right: 1px solid #e0e0e0;
}


.hiring-image {
  width: 100%;
  margin-top: 10rem;

  max-width: 500px;
  height: auto;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
  transition: transform 0.4s ease;
  max-width: 550px; /* Augmenté depuis 500px */
  max-height: 480px; 
}

.hiring-image:hover {
  transform: scale(1.05);
}

.auth-box {
  margin-top: 60px;
  background: #fff;
  border-radius: var(--border-radius);
  max-width: 520px;
  width: 100%;
  padding: 40px;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
  border: 1px solid var(--input-border);
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}


.auth-title {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 25px;
  color: #222;
  text-align: center;
}
.auth-title::after {
  content: '';
  display: block;
  width: 80px;
  height: 3px;
  background: var(--primary-color);
  margin: 10px auto 0;
  border-radius: 2px;
}

/* ===== Form Styles ===== */
.form-section {
  flex: 1;
  padding: 2rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}


.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}
.input-group {
  display: flex;
  flex-direction: column;
}

.input-group label {
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
}

.input-group input {
  padding: 12px;
  padding-right: 40px; /* ✅ Ajout pour éviter le chevauchement avec l’icône 👁️ */
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  transition: all 0.3s ease;
}

.input-group input:focus {
  border-color: #0056d2;
  box-shadow: 0 0 0 4px rgba(0, 86, 210, 0.15);
  outline: none;
}

.input-error {
  border-color: #dc3545 !important;
}

.input-error:focus {
  box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.2) !important;
}

.error-message {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
}

.password-input-container {
  position: relative;
  width: 100%; /* 🔥 Fait en sorte que le container ne soit pas plus petit */
  display: flex;
  align-items: center;
}

.password-input-container input {
  flex: 1; /* 🔥 Permet à l’input de prendre toute la largeur disponible */
  padding-right: 40px; /* Assure de la place pour l’icône */
}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: #666;
  padding: 0;
  height: 100%;
  display: flex;
  align-items: center;
}


.toggle-password:hover {
  color: var(--primary-color);
}

.password-strength {
  margin-top: 8px;
}

.strength-bar {
  height: 5px;
  border-radius: 3px;
  margin-bottom: 5px;
  transition: all 0.3s ease;
}

.weak {
  background-color: var(--danger-color);
  width: 30%;
}

.medium {
  background-color: var(--warning-color);
  width: 60%;
}

.strong {
  background-color: var(--success-color);
  width: 100%;
}

/* ===== Checkbox ===== */
.checkbox-group {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0;
}

.checkbox-group input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.checkbox-group label {
  margin-bottom: 0;
  font-weight: normal;
  font-size: 0.9rem;
  cursor: pointer;
}

.checkbox-group a {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
}

.checkbox-group a:hover {
  text-decoration: underline;
}
.btn-submit {
  margin-top: 20px;
  padding: 14px 20px;
  width: 100%;
  font-size: 16px;
  font-weight: bold;
  color: white;
  background-color: #007BFF;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px; /* ✅ pour espacer le spinner du texte */
}

.btn-submit:hover {
  background-color: #0056d2;
}

.btn-submit:disabled {
  background-color: #bbb;
  cursor: not-allowed;
}
.loading-spinner {
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  animation: spin 0.6s linear infinite;
  display: inline-block;
}


@keyframes spin {
  0% { transform: rotate(0); }
  100% { transform: rotate(360deg); }
}

/* Force du mot de passe */
.password-strength {
  margin-top: 10px;
  font-size: 13px;
  color: #444;
}

.strength-bar {
  height: 8px;
  border-radius: 5px;
  background: #e0e0e0;
  margin-top: 5px;
  transition: background-color 0.3s ease;
}

.strength-bar.weak {
  background-color: #dc3545;
}

.strength-bar.medium {
  background-color: #ffc107;
}

.strength-bar.strong {
  background-color: #28a745;
}

.password-requirements {
  font-size: 12px;
  color: #666;
  margin-top: 5px;
  line-height: 1.4;
}

/* Succès */
.success-box {
  text-align: center;
}

.success-icon {
  font-size: 48px;
  color: #28a745;
  background: rgba(40, 167, 69, 0.1);
  border-radius: 50%;
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Responsivité */
@media (max-width: 992px) {
  .auth-content {
    flex-direction: column;
    width: 100%;
  }

  .image-section {
    display: none;
  }
}

@media (max-width: 576px) {
  .form-section {
    padding: 1.5rem;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .btn-submit {
    font-size: 15px;
  }
}
</style>