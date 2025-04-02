<script setup>
import logo from "../../assets/logo.png";
import search from "../../assets/search.png";
</script>

<template>
  <div id="app">
    <nav class="navbar">
      <div class="nav-container">
        <router-link to="/" class="logo">
          <img class="logo-img" :src="logo" alt="Logo JobGo" />
          <p class="jobgo">JobGo</p>
        </router-link>

        <div class="nav-links">
          <router-link to="/" class="nav-btn">Accueil</router-link>
          <router-link to="/Apropos" class="nav-btn">A propos</router-link>
          <router-link to="/Offres" class="nav-btn">Nos Offres</router-link>
          <div class="search">
            <input class="search-input" type="text" placeholder="Trouvez votre job" />
            <img class="search-img" :src="search" alt="Search" />
          </div>
          <router-link to="/authentification" class="nav-btn">Mon Espace</router-link>
        </div>
      </div>
    </nav>

    <!-- Contenu de la page -->
    <router-view />
  </div>
</template>

<style scoped>
/* Navbar Styling */
.navbar {
  background-color: #ffffff;
  padding: 18px 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.nav-container {
  width: 100%;
  max-width: 1440px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
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
  margin: 0 16px;
  outline: none;
}

.search-img {
  height: 40px;
  width: auto;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .nav-links {
    gap: 16px;
  }

  .search-input {
    width: 150px;
  }
}
</style>
