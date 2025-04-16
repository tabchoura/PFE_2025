<template>
  <div class="profil-container">
    <h2 class="title">👤 Bienvenue {{ profile.prenom }} {{ profile.nom }}</h2>

    <ul class="profil-details" v-if="!editMode">
      <li><strong>Nom de la Société :</strong> {{ profile.nomsociete }}
      </li>
      <li><strong>Département de la Société :</strong> {{ profile.departement }}</li>
      <li><strong>Email :</strong> {{ profile.email }}</li>

      <li><strong>CIN :</strong> {{ profile.cin }}</li>
      <li><strong>Numéro de téléphone :</strong> {{ profile.numtel }}</li>

      <li><strong>Num Société :</strong> {{ profile.numsociete }}</li>
      <li><strong>Lieu de la Société :</strong> {{ profile.lieu }}</li>
    </ul>

    <button
      class="btn-edit"
      @click="toggleEditMode"
      :aria-label="editMode ? 'Annuler la modification' : 'Modifier le profil'"
    >
      {{ editMode ? "❌ Annuler" : "✏️ Modifier le profil" }}
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
        <label for="nomsociete">Nom de la Société</label>
        <input type="text" id="nomsociete" v-model="form.nomsociete" required />
      </div>
      <div class="form-group">
        <label for="departement">Département de la Société</label>
        <input type="text" id="departement" v-model="form.departement" required />
      </div>
      <div class="form-group">
        <label for="lieu">Localisation de la Société</label>
        <input type="text" id="lieu" v-model="form.lieu" required />
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
      <div class="form-group">
        <label for="numsociete">Numéro de la Société</label>
        <input
          type="tel"
          id="numsociete"
          v-model="form.numsociete"
          required
          pattern="[0-9]{8,}"
          title="Numéro de téléphone valide (minimum 8 chiffres)"
        />
      </div>

      <div class="form-actions">
        <button type="button" class="btn-cancel" @click="toggleEditMode">❌ Annuler</button>
        <button type="submit" class="btn-save">💾 Enregistrer</button>
      </div>
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
        lieu: "",
        cin: "",
        numtel: "",
        nomsociete: "",
        numsociete: "",
        departement:"",
      },
    
      form: {},
      editMode: false,
    };
  },
  mounted() {
    this.loadProfileData();
  },
  methods: {
    loadProfileData() {
      try {
        const userSession = JSON.parse(
          sessionStorage.getItem("userSession") ||
          localStorage.getItem("userSession") ||
          '{}'
        );

        if (userSession) {
          this.profile = {
  nom: userSession.nom || "",
  prenom: userSession.prenom || "",
  email: userSession.email || "",
  lieu: userSession.lieu || userSession.lieuNaissance || "",
  cin: userSession.cin || "",
  numtel: userSession.numtel || "",
  nomsociete: userSession.nomsociete || userSession.nomentreprise || "",
  numsociete: userSession.numsociete || "",
  departement: userSession.departement || "",
};

          this.form = { ...this.profile };
        }
      } catch (error) {
        console.error("Erreur lors du chargement des données du profil:", error);
      }
    },

    toggleEditMode() {
      if (this.editMode) {
        this.form = { ...this.profile };
      }
      this.editMode = !this.editMode;
    },

    updateProfile() {
      if (!this.form.email || !this.form.nom || !this.form.prenom) {
        alert("❌ Veuillez remplir tous les champs obligatoires");
        return;
      }

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(this.form.email)) {
        alert("❌ Veuillez entrer un email valide");
        return;
      }

      this.profile = { ...this.form };

      try {
        const userSession = JSON.parse(
          sessionStorage.getItem("userSession") ||
          localStorage.getItem("userSession") ||
          '{}'
        );

        const updatedSession = {
  ...userSession,
  nom: this.form.nom,
  prenom: this.form.prenom,
  email: this.form.email,
  numtel: this.form.numtel,
  lieu: this.form.lieu,
  cin: this.form.cin,
  nomsociete: this.form.nomsociete,
  numsociete: this.form.numsociete,
  departement: this.form.departement,
};

        if (sessionStorage.getItem("userSession")) {
          sessionStorage.setItem("userSession", JSON.stringify(updatedSession));
        }
        if (localStorage.getItem("userSession")) {
          localStorage.setItem("userSession", JSON.stringify(updatedSession));
        }

        this.editMode = false;
        this.showSuccessMessage("Profil mis à jour avec succès !");
      } catch (error) {
        console.error("Erreur lors de la sauvegarde du profil:", error);
        alert("❌ Une erreur est survenue lors de la sauvegarde du profil.");
      }
    },

   
    showSuccessMessage(message) {
      const notification = document.createElement("div");
      notification.className = "success-notification";
      notification.textContent = "✅ " + message;

      document.body.appendChild(notification);

      setTimeout(() => notification.classList.add("show"), 100);
      setTimeout(() => {
        notification.classList.remove("show");
        setTimeout(() => {
          document.body.removeChild(notification);
        }, 500);
      }, 3000);
    },
  },
};
</script>



<style scoped>
.profil-container {
  max-width: 900px;
  padding: 4rem;
  background: #ffffff;
  border-radius: 1.25rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  font-family: "Roboto", sans-serif;
  transition: all 0.3s ease;
  margin: 2px auto; /* Centre le contenu */
}

.profil-container:hover {
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
}

.title {
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #0055a5;
  margin-bottom: 2rem;
}

.profil-details {
  list-style: none;
  padding: 0;
  font-size: 1.1rem;
  color: #374151;
}

.profil-details li {
  margin-bottom: 20px;
  position: relative;
  padding-left: 1.5em;
}

.profil-details li::before {
  content: "•";
  color: #2563eb;
  font-weight: bold;
  position: absolute;
  left: 0;
  font-size: 1.2rem;
}

.btn-edit {
  margin-top: 20px;
  background-color: #2563eb;
  color: white;
  padding: 14px 20px;
  border-radius: 8px;
  border: none;
  font-weight: bold;
  cursor: pointer;
  width: 100%;
  transition: all 0.3s ease;
  text-align: center;
}

.btn-edit:hover {
  background-color: #1e40af;
  transform: scale(1.03);
}

.btn-edit:focus {
  outline: 3px solid rgba(37, 99, 235, 0.5);
  outline-offset: 2px;
}

.edit-form {
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

label {
  font-weight: 500;
  color: #4b5563;
}

input {
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

input:focus {
  border-color: #2563eb;
  outline: none;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
}

input:invalid {
  border-color: #ef4444;
}

.form-actions {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.btn-cancel {
  background-color: #f3f4f6;
  color: #4b5563;
  padding: 14px 20px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  flex: 1;
}

.btn-cancel:hover {
  background-color: #e5e7eb;
}

.btn-save {
  background-color: #16a34a;
  color: white;
  padding: 14px 20px;
  border-radius: 8px;
  border: none;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  flex: 1;
}

.btn-save:hover {
  background-color: #15803d;
  transform: scale(1.03);
}

.btn-save:focus {
  outline: 3px solid rgba(22, 163, 74, 0.5);
  outline-offset: 2px;
}

.success-notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #16a34a;
  color: white;
  padding: 15px 25px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.3s ease, transform 0.3s ease;
  z-index: 1000;
}

.success-notification.show {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 768px) {
  .profil-container {
    padding: 2rem;
    margin: 0 15px;
    border-radius: 1rem;
  }

  .title {
    font-size: 1.5rem;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-cancel,
  .btn-save {
    width: 100%;
  }
}
</style>
