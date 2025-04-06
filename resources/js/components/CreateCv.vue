<template>
  <div class="create-cv-container">
    <h2>📝 Créer un CV</h2>
    <form @submit.prevent="submitCv" class="cv-form">
      <!-- Infos personnelles -->
      <div class="form-group">
        <label for="infopersonnels">Infos personnelles *</label>
        <textarea v-model="form.infopersonnels" id="infopersonnels" rows="2" />
        <span v-if="errors.infopersonnels" class="error">{{
          errors.infopersonnels
        }}</span>
      </div>

      <!-- Diplômes -->
      <div class="form-group">
        <label for="diplomes">Diplômes</label>
        <input v-model="form.diplomes" type="text" id="diplomes" />
      </div>

      <!-- Expériences -->
      <div class="form-group">
        <label for="experiences">Expériences</label>
        <input v-model="form.experiences" type="text" id="experiences" />
      </div>

      <!-- Compétences -->
      <div class="form-group">
        <label for="competences">Compétences</label>
        <input v-model="form.competences" type="text" id="competences" />
      </div>

      <!-- Langues -->
      <div class="form-group">
        <label for="langues">Langues</label>
        <input v-model="form.langues" type="text" id="langues" />
      </div>

      <!-- Fichier -->
      <div class="form-group">
        <label for="cvFile">Téléverser un CV (PDF ou Word)</label>
        <input type="file" id="cvFile" @change="handleFile" />
        <div v-if="form.fichier" class="file-preview">
          <span>{{ form.fichier.name }}</span>
          <button type="button" @click="removeFile">Supprimer</button>
        </div>
        <span v-if="errors.fichier" class="error">{{ errors.fichier }}</span>
      </div>

      <!-- Soumettre -->
      <button type="submit" :disabled="loading">
        {{ loading ? "Création en cours..." : "Créer mon CV" }}
      </button>
    </form>
  </div>
</template>

<script>
const ALLOWED_TYPES = [
  "application/pdf",
  "application/msword",
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
];
const MAX_SIZE = 5 * 1024 * 1024;

export default {
  name: "CreateCv",
  data() {
    return {
      form: {
        infopersonnels: "",
        diplomes: "",
        experiences: "",
        competences: "",
        langues: "",
        fichier: null,
      },
      errors: {},
      loading: false,
    };
  },
  methods: {
    handleFile(e) {
      const file = e.target.files[0];
      if (file) {
        this.form.fichier = file;
        this.errors.fichier = "";
      }
    },
    removeFile() {
      this.form.fichier = null;
    },
    validate() {
      this.errors = {};
      if (!this.form.infopersonnels.trim()) {
        this.errors.infopersonnels = "Ce champ est requis.";
      }
      if (this.form.fichier) {
        if (!ALLOWED_TYPES.includes(this.form.fichier.type)) {
          this.errors.fichier = "Format non autorisé. (PDF, DOC)";
        } else if (this.form.fichier.size > MAX_SIZE) {
          this.errors.fichier = "Fichier trop volumineux (max 5MB)";
        }
      }
      return Object.keys(this.errors).length === 0;
    },
    async submitCv() {
      if (!this.validate()) return;
      this.loading = true;

      try {
        await new Promise((res) => setTimeout(res, 1500));
        alert("CV créé avec succès !");
        // Tu peux ici faire un appel API pour enregistrer le CV
      } catch (err) {
        alert("Erreur lors de la création");
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.create-cv-container {
  max-width: 600px;
  margin: auto;
  padding: 20px;
}

.cv-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
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
  border-radius: 5px;
}

label {
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.error {
  color: #e11d48;
  font-size: 0.875rem;
  margin-top: 5px;
}

.file-preview {
  background: #f3f4f6;
  padding: 8px;
  margin-top: 5px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 5px;
}

button[type="submit"] {
  background-color: #2563eb;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}

button[type="submit"]:disabled {
  background-color: #93c5fd;
  cursor: not-allowed;
}
</style>
