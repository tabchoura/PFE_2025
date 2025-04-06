<template>
  <div class="monprofil-container">
    <h2>👤 Modifier mon profil</h2>
    <form @submit.prevent="submitForm" class="profil-form">
      <!-- Infos personnelles -->
      <div class="form-group">
        <label for="infopersonnels">Infos personnelles *</label>
        <textarea v-model="formData.infopersonnels" id="infopersonnels" rows="2" />
        <span v-if="errors.infopersonnels" class="error">{{
          errors.infopersonnels
        }}</span>
      </div>

      <!-- Diplômes -->
      <div class="form-group">
        <label for="diplomes">Diplômes</label>
        <input v-model="formData.diplomes" type="text" id="diplomes" />
      </div>

      <!-- Expériences -->
      <div class="form-group">
        <label for="experiences">Expériences</label>
        <input v-model="formData.experiences" type="text" id="experiences" />
      </div>

      <!-- Compétences -->
      <div class="form-group">
        <label for="competences">Compétences</label>
        <input v-model="formData.competences" type="text" id="competences" />
      </div>

      <!-- Langues -->
      <div class="form-group">
        <label for="langues">Langues</label>
        <input v-model="formData.langues" type="text" id="langues" />
      </div>

      <!-- Upload de fichier -->
      <div class="form-group">
        <label for="fichier">Joindre un CV (PDF, Word)</label>
        <input type="file" id="fichier" @change="handleFileUpload" />
        <div v-if="formData.fichier" class="file-preview">
          <span>{{ formData.fichier.name }}</span>
          <button type="button" @click="removeFile">Supprimer</button>
        </div>
        <span v-if="errors.fichier" class="error">{{ errors.fichier }}</span>
      </div>

      <!-- Bouton de soumission -->
      <button type="submit" :disabled="isSubmitting">
        {{ isSubmitting ? "Envoi en cours..." : "Mettre à jour" }}
      </button>
    </form>
  </div>
</template>

<script>
const ALLOWED_FILE_TYPES = [
  "application/pdf",
  "application/msword",
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
];
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

export default {
  name: "Monprofile",
  data() {
    return {
      formData: {
        infopersonnels: "",
        diplomes: "",
        experiences: "",
        competences: "",
        langues: "",
        fichier: null,
      },
      errors: {},
      isSubmitting: false,
    };
  },
  mounted() {
    this.loadProfile();
  },
  methods: {
    loadProfile() {
      const existingData = {
        infopersonnels: "Étudiant en informatique",
        diplomes: "Master en Data Science",
        experiences: "Stage chez Capgemini",
        competences: "Python, SQL",
        langues: "Français, Anglais",
      };

      this.formData = { ...this.formData, ...existingData };
    },

    validateForm() {
      this.errors = {};

      if (!this.formData.infopersonnels.trim()) {
        this.errors.infopersonnels = "Ce champ est requis";
      }

      if (this.formData.fichier) {
        if (!ALLOWED_FILE_TYPES.includes(this.formData.fichier.type)) {
          this.errors.fichier = "Formats acceptés : PDF, Word";
        }
        if (this.formData.fichier.size > MAX_FILE_SIZE) {
          this.errors.fichier = "Fichier trop volumineux (max 5MB)";
        }
      }

      return Object.keys(this.errors).length === 0;
    },

    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.fichier = file;
        this.errors.fichier = "";
      }
    },

    removeFile() {
      this.formData.fichier = null;
      this.errors.fichier = "";
    },

    async submitForm() {
      if (!this.validateForm()) return;
      this.isSubmitting = true;

      try {
        await new Promise((resolve) => setTimeout(resolve, 1500));
        alert("Profil mis à jour avec succès !");
      } catch (error) {
        alert("Une erreur est survenue");
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>

<style scoped>
.monprofil-container {
  max-width: 600px;
  margin: auto;
}

.profil-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

textarea,
input[type="text"],
input[type="file"] {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}

label {
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.error {
  color: #dc2626;
  font-size: 0.875rem;
}

button[type="submit"] {
  background-color: #2563eb;
  color: white;
  font-weight: 600;
  padding: 0.75rem;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: background 0.2s ease;
}

button[type="submit"]:disabled {
  background-color: #93c5fd;
  cursor: not-allowed;
}

.file-preview {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f3f4f6;
  padding: 0.5rem;
  border-radius: 0.375rem;
  margin-top: 0.5rem;
}
</style>
