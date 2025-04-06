<template>
  <div class="profil-container">
    <h2>👤 Bienvenue {{ profile.prenom }} {{ profile.nom }}</h2>

    <ul class="profil-details" v-if="!editMode">
      <li><strong>Date de naissance :</strong> {{ profile.date }}</li>
      <li><strong>Numéro de téléphone :</strong> {{ profile.numtel }}</li>
      <li><strong>CIN :</strong> {{ profile.cin }}</li>
      <li><strong>Lieu de naissance :</strong> {{ profile.lieu }}</li>
      <li><strong>Email :</strong> {{ profile.email }}</li>
    </ul>

    <button class="btn-edit" @click="editMode = !editMode">
      {{ editMode ? "❌ Annuler" : "✏️ Modifier le profil" }}
    </button>

    <form v-if="editMode" @submit.prevent="updateProfile" class="edit-form">
      <div class="form-group">
        <label>Nom</label>
        <input type="text" v-model="form.nom" />
      </div>
      <div class="form-group">
        <label>Prénom</label>
        <input type="text" v-model="form.prenom" />
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" v-model="form.email" />
      </div>
      <div class="form-group">
        <label>Téléphone</label>
        <input type="tel" v-model="form.numtel" />
      </div>
      <div class="form-group">
        <label>Date de naissance</label>
        <input type="date" v-model="form.date" />
      </div>
      <div class="form-group">
        <label>Lieu de naissance</label>
        <input type="text" v-model="form.lieu" />
      </div>
      <div class="form-group">
        <label>CIN</label>
        <input type="text" v-model="form.cin" />
      </div>

      <button type="submit" class="btn-save">💾 Enregistrer</button>
    </form>
  </div>
</template>

<script>
export default {
  name: "Monprofile",
  data() {
    return {
      profile: {
        nom: "",
        prenom: "",
        email: "",
        date: "",
        lieu: "",
        cin: "",
        numtel: "",
      },
      form: {}, // Pour le formulaire d'édition
      editMode: false,
    };
  },
  mounted() {
    const candidat = JSON.parse(localStorage.getItem("candidat"));
    if (candidat) {
      this.profile = { ...this.profile, ...candidat };
      this.form = { ...this.profile }; // Initialiser le formulaire avec les mêmes données
    }
  },
  methods: {
    updateProfile() {
      this.profile = { ...this.form };
      localStorage.setItem("candidat", JSON.stringify(this.profile));
      this.editMode = false;
      alert("✅ Profil mis à jour !");
    },
  },
};
</script>

<style scoped>
.profil-container {
  max-width: 600px;
  margin: 40px auto;
  background: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
  font-family: Arial, sans-serif;
}

h2 {
  margin-bottom: 20px;
  color: #2563eb;
}

.profil-details {
  list-style: none;
  padding: 0;
  font-size: 1rem;
}

.profil-details li {
  margin-bottom: 10px;
  padding-left: 10px;
  position: relative;
}

.profil-details li::before {
  content: "•";
  color: #2563eb;
  font-weight: bold;
  display: inline-block;
  width: 1em;
  margin-left: -1em;
}

.btn-edit {
  margin-top: 20px;
  background-color: #2563eb;
  color: white;
  padding: 10px 18px;
  border-radius: 6px;
  border: none;
  font-weight: bold;
  cursor: pointer;
}

.btn-edit:hover {
  background-color: #1e40af;
}

.edit-form {
  margin-top: 25px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

input {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.btn-save {
  margin-top: 10px;
  background-color: #16a34a;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}

.btn-save:hover {
  background-color: #15803d;
}
</style>
