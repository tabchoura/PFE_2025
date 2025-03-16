<template>
    <div class="container d-flex justify-content-center align-items-center vh-100">
      <div class="card shadow-lg p-4" style="width: 400px;">
        <h2 class="text-center text-primary mb-3">Connexion</h2>
        <form @submit.prevent="login">
          <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" v-model="email" required />
          </div>
          <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" class="form-control" v-model="password" required />
          </div>
          <button type="submit" class="btn btn-primary w-100 rounded-pill">Se connecter</button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        email: "",
        password: ""
      };
    },
    methods: {
      login() {
        alert(`Connexion avec ${this.email}`);
      }
    }
  };
  </script>
  
  <style scoped>
  .card {
    border-radius: 15px;
  }
  </style>
  