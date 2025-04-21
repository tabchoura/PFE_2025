<template>
  <div class="profil-container">
    <h2 class="title">👤 Bienvenue {{ profile.prenom }} {{ profile.nom }}</h2>

    <ul class="profil-details" v-if="!editMode">
      <li><strong>Date de naissance :</strong> {{ formatDate(profile.date_naissance) }}</li>
      <li><strong>Numéro de téléphone :</strong> {{ profile.phone }}</li>
      <li><strong>CIN :</strong> {{ profile.cin }}</li>
      <li><strong>Lieu de naissance :</strong> {{ profile.lieudenaissance }}</li>
      <li><strong>Email :</strong> {{ profile.email }}</li>
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
        <input type="text" id="lieudenaissance" v-model="form.lieudenaissance" required />
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
        <button type="button" class="btn-cancel" @click="toggleEditMode">❌ Annuler</button>
        <button type="submit" class="btn-save">💾 Enregistrer</button>
      </div>
    </form>
  </div>
</template>

<script>
import api from "@/axios"; // adapte le chemin si besoin

export default {
  name: "Monprofile",
  data() {
    return {
      profile: {
        nom: "",
        prenom: "",
        email: "",
        date_naissance: "",
        lieudenaissance: "",
        cin: "",
        phone: "",
      },
      form: {},
      editMode: false,
    };
  },
  mounted() {
    this.loadProfileData();
  },
  methods: {
    // Ajout de la méthode toggleEditMode
    toggleEditMode() {
      if (!this.editMode) {
        // Si nous entrons en mode édition, copier les données du profil dans le formulaire
        this.form = { ...this.profile };
      }
      this.editMode = !this.editMode;
    },
    
    async loadProfileData() {
      try {
        await api.get('/sanctum/csrf-cookie'); // Assurez-vous d'obtenir le cookie CSRF

        const me = await api.get("/api/me"); // Requête pour charger les données du profil utilisateur

        const user = me.data;
        this.profile = {
          nom: user.nom || "",
          prenom: user.prenom || "",
          email: user.email || "",
          date_naissance: user.date_naissance || "",
          lieudenaissance: user.lieudenaissance || "",
          cin: user.cin || "",
          phone: user.phone || "",
        };

        this.form = { ...this.profile };

      } catch (error) {
        console.error("Erreur lors du chargement des données du profil:", error);

        if (error.response && error.response.status === 401) {
          // Gestion spécifique des erreurs 401 (Unauthorized)
          alert("Votre session a expiré. Veuillez vous reconnecter.");
          // Redirigez l'utilisateur vers la page de connexion ou effectuez une autre action appropriée
        } else {
          // Gestion des autres erreurs
          alert("Une erreur est survenue lors du chargement des données du profil.");
        }
      }
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
          phone: this.form.phone,
          lieudenaissance: this.form.lieudenaissance,
          cin: this.form.cin,
          date_naissance: this.form.date_naissance,
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

    formatDate(dateString) {
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
    },

    showSuccessMessage(message) {
      const notification = document.createElement("div");
      notification.className = "success-notification";
      notification.textContent = "✅ " + message;

      document.body.appendChild(notification);

      setTimeout(() => {
        notification.classList.add("show");
      }, 100);

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