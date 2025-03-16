<template>
    <div class="container d-flex justify-content-center align-items-center vh-100">
      <div class="card shadow-lg p-4" style="width: 400px;">
        <h2 class="text-center text-primary mb-3">Inscription</h2>
        <form @submit.prevent="register">
          <div class="mb-3">
            <label>Nom</label>
            <input type="text" class="form-control" v-model="name" required />
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" v-model="email" required />
          </div>
          <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" class="form-control" v-model="password" required />
          </div>
          <button type="submit" class="btn btn-primary w-100 rounded-pill">S'inscrire</button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        name: "",
        email: "",
        password: ""
      };
    },
    methods: {
      register() {
        alert(`Inscription réussie pour ${this.name}`);
      }
    }
  };
  </script>
  
  <style scoped>
  .card {
    border-radius: 15px;
  }
  </style>
  