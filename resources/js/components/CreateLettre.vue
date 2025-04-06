<template>
  <div class="lettre-container">
    <h2>📨 Créer une lettre de motivation</h2>

    <form @submit.prevent="submitLettre" class="lettre-form">
      <div class="form-group">
        <label for="nom">Nom complet</label>
        <input type="text" v-model="lettre.nom" id="nom" />
      </div>

      <div class="form-group">
        <label for="poste">Poste ciblé</label>
        <input type="text" v-model="lettre.poste" id="poste" />
      </div>

      <div class="form-group">
        <label for="entreprise">Entreprise</label>
        <input type="text" v-model="lettre.entreprise" id="entreprise" />
      </div>

      <div class="form-group">
        <label for="contenu">Lettre de motivation</label>
        <textarea
          v-model="lettre.contenu"
          id="contenu"
          rows="10"
          placeholder="Exprimez votre motivation ici..."
        />
      </div>

      <button type="submit" class="btn-submit">💾 Enregistrer</button>
    </form>

    <div v-if="isSubmitted" class="success-message">
      ✅ Lettre enregistrée avec succès !
    </div>
  </div>
</template>

<script>
export default {
  name: "CreateLettre",
  data() {
    return {
      lettre: {
        nom: "",
        poste: "",
        entreprise: "",
        contenu: "",
      },
      isSubmitted: false,
    };
  },
  methods: {
    submitLettre() {
      this.isSubmitted = true;

      // Tu peux aussi l’enregistrer dans localStorage :
      localStorage.setItem("lettre", JSON.stringify(this.lettre));

      // Tu pourrais aussi appeler une API ici ou générer un PDF
    },
  },
};
</script>

<style scoped>
.lettre-container {
  max-width: 700px;
  margin: 0 auto;
  padding: 30px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
  font-family: Arial, sans-serif;
}

h2 {
  color: #2563eb;
  margin-bottom: 20px;
}

.lettre-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

input,
textarea {
  padding: 10px;
  border: 1px solid #d1d5db;
  border-radius: 5px;
  font-size: 1rem;
}

.btn-submit {
  background-color: #2563eb;
  color: white;
  font-weight: bold;
  padding: 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn-submit:hover {
  background-color: #1e40af;
}

.success-message {
  margin-top: 20px;
  color: #16a34a;
  font-weight: bold;
}
</style>
