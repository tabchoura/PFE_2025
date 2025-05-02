<script setup>
import { ref } from 'vue';
import logo from "../../assets/logo.png";
import search from "../../assets/search.png";

// State to control mobile menu visibility
const mobileMenuOpen = ref(false);

// Toggle mobile menu
const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
};

// Close mobile menu when a link is clicked
const closeMenu = () => {
  mobileMenuOpen.value = false;
};
</script>

<template>
  <div id="app">
    <nav class="navbar">
      <div class="nav-container">
        <router-link to="/" class="logo" @click="closeMenu">
          <img class="logo-img" :src="logo" alt="Logo JobGo" />
          <p class="jobgo">JobGo</p>
        </router-link>
        
        <!-- Hamburger menu button (mobile only) -->
        <div class="hamburger" @click="toggleMobileMenu">
          <span></span>
          <span></span>
          <span></span>
        </div>

        <div class="nav-links" :class="{ 'active': mobileMenuOpen }">
          <router-link to="/" class="nav-btn" @click="closeMenu">Accueil</router-link>
          <router-link to="/Apropos" class="nav-btn" @click="closeMenu">A propos</router-link>
          <router-link to="/Offres" class="nav-btn" @click="closeMenu">Nos Offres</router-link>
          <div class="search">
            <input class="search-input" type="text" placeholder="Trouvez votre job" />
            <img class="search-img" :src="search" alt="Search" />
          </div>
          <router-link to="/authentification" class="nav-btn auth-btn" @click="closeMenu">Mon Espace</router-link>
        </div>
      </div>
    </nav>

    <!-- Overlay for mobile menu -->
    <div class="overlay" v-if="mobileMenuOpen" @click="closeMenu"></div>

    <!-- Contenu de la page -->
    <div class="page-content">
      <router-view />
    </div>
  </div>
</template>

<style scoped>
/* Navbar Styling */
.navbar {
  background-color: #ffffff;
  padding: 18px 20px;
  margin-bottom: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
}

.nav-container {
  width: 100%;
  max-width: 1440px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 0 auto;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  z-index: 1001; /* Ensure logo stays above mobile menu */
}

.logo-img {
  height: 40px;
  width: auto;
}

.jobgo {
  font-family: "Inspiration", cursive;
  font-size: 26px;
  color: #2c3e50;
  margin: 3px;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 28px;
}

.nav-btn {
  font-size: 16px;
  color: #333;
  text-decoration: none;
  padding: 10px 16px;
  border-radius: 8px;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.nav-btn:hover {
  background-color: #f0f0f0;
  color: #0056b3;
}

.auth-btn {
  font-weight: 600;
}

.search {
  display: flex;
  align-items: center;
  gap: 3px;
}

.search-input {
  padding: 8px 14px;
  border: 1px solid #ccc;
  border-radius: 30px;
  font-size: 14px;
  width: 200px;
  outline: none;
}

.search-img {
  height: 24px;
  width: auto;
  cursor: pointer;
}

/* Hamburger menu styling */
.hamburger {
  display: none;
  flex-direction: column;
  justify-content: space-between;
  width: 30px;
  height: 21px;
  cursor: pointer;
  z-index: 1001;
}

.hamburger span {
  display: block;
  height: 3px;
  width: 100%;
  background-color: #333;
  border-radius: 3px;
  transition: all 0.3s ease;
}

/* Overlay styling */
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

/* Page content padding to account for fixed navbar */
.page-content {
  padding-top: 76px; /* Navbar height + padding */
}

/* Mobile responsiveness */
@media screen and (max-width: 1024px) {
  .search-input {
    width: 160px;
  }
  
  .nav-links {
    gap: 15px;
  }
  
  .nav-btn {
    padding: 8px 12px;
  }
}

@media screen and (max-width: 768px) {
  .hamburger {
    display: flex;
  }
  
  .nav-links {
    position: fixed;
    top: 0;
    right: -300px;
    width: 280px;
    height: 100vh;
    background-color: #ffffff;
    flex-direction: column;
    justify-content: flex-start;
    padding: 80px 20px 20px;
    transition: right 0.3s ease;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    gap: 20px;
    align-items: flex-start;
  }
  
  .nav-links.active {
    right: 0;
  }
  
  .search {
    width: 100%;
    margin: 10px 0;
  }
  
  .search-input {
    width: 100%;
    margin: 0;
  }
  
  .auth-btn {
    margin-top: 10px;
    width: 100%;
    text-align: center;
    background-color: #f5f5f5;
  }
}

@media screen and (max-width: 480px) {
  .navbar {
    padding: 15px;
  }
  
  .logo-img {
    height: 32px;
  }
  
  .jobgo {
    font-size: 22px;
  }
  
  .nav-links {
    width: 250px;
    right: -260px;
  }
}
</style>