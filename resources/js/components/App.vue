<template>
  <div>
    <!-- Barre de Navigation -->
    <nav class="navbar">
      <div class="nav-container">
        <a class="logo">
          <div class="logo-content">
            <img class="logo-img" src="C:\laragon\www\PFE_2025\public\images\logo.png" alt="Logo JobGo" />
          </div>
        </a>

        <div class="nav-links">
          <button @click="page = 'home'">Accueil</button>
          <button @click="page = 'offres'">Nos Offres</button>

          <!-- Mon Espace avec menu déroulant -->
          <div 
            class="espace">
            <button @click="page = 'authentification'">
              Mon Espace
              <img class="icon" src="C:\laragon\www\PFE_2025\public\images\login.png" alt="Mon Espace Icon" />
            </button>

            <!-- Menu déroulant qui apparaît sur survol -->
            
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu Dynamique -->
    <div>
      <component :is="currentComponent"></component>
    </div>

    <!-- Pied de page -->
    <!--<footer>
      <p>© 2024 JobGo - Tous droits réservés.</p>
    </footer>-->
  </div>
</template>

<script>
import Home from '../components/Home.vue';
import Offres from '../components/Offres.vue';

export default {
  components: { Home, Offres },
  data() {
    return {
      page: 'home',
      showDropdown: false
    };
  },
  computed: {
    currentComponent() {
      return this.page === 'home' ? Home
           : this.page === 'offres' ? Offres
           : null;
    }
  },
  methods: {
    logout() {
      alert("Déconnexion réussie !");
      this.page = 'home';
    },
    hideDropdownWithDelay() {
      setTimeout(() => {
        this.showDropdown = false;
      }, 200); // Délai pour éviter la fermeture instantanée
    }
  }
};
</script>

<style scoped>
/* --- Navigation Bar --- */
.navbar {
  background-color: #2c3e50;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.nav-container {
  display: flex;
  align-items: center;
  width: 100%;
  justify-content: space-between;
}

.logo-content {
  display: flex;
  align-items: center;
}

.logo-img {
  height: 50px;
}

/* --- Navigation Links --- */
.nav-links {
  display: flex;
  align-items: center;
  gap: 20px;
}

.nav-links button {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
  padding: 10px 15px;
  transition: background 0.3s, color 0.3s;
}

.nav-links button:hover {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 5px;
}

/* --- Dropdown Container --- */
.espace {
  position: relative;
  display: inline-block;
}

/* --- Bouton Mon Espace --- */
.espace button {
  display: flex;
  align-items: center;
  gap: 10px;
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
  padding: 10px;
}

.espace .icon {
  width: 20px;
  height: 20px;
}

/* --- Menu déroulant --- */
.dropdown-content {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  border-radius: 5px;
  min-width: 150px;
  display: flex;
  flex-direction: column;
}

.dropdown-content a {
  padding: 10px;
  text-decoration: none;
  color: #333;
  display: block;
}

.dropdown-content a:hover {
  background: #f1f1f1;
}

/* --- Footer --- */
/*footer {
  background: #2c3e50;
  color: white;
  text-align: center;
  padding: 10px;
  margin-top: 50px;
}*/
</style>
