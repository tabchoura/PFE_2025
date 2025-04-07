<template>
  <div class="mes-notifications">
    <h2>Mes Notifications</h2>
    <ul>
      <li v-for="notification in notifications" :key="notification.id">
        <span>{{ notification.message }}</span> -
        <span class="date">{{ notification.date }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from "vue";

const notifications = ref([
  { id: 1, message: "Entretien prévu pour le poste X", date: "2025-04-10" },
  { id: 2, message: "Nouvelle offre correspondant à votre profil", date: "2025-04-05" },
]);
</script>
<style scoped>
/* Styles pour la section Mes Notifications */
.mes-notifications {
  padding: 20px;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.mes-notifications h2 {
  color: #0055a5;
  font-size: 1.6rem;
  margin-bottom: 15px;
}

.mes-notifications ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.mes-notifications li {
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 10px;
  background-color: #f4f6f9;
  color: #333;
  font-size: 1rem;
  transition: background-color 0.3s, color 0.3s;
}

.mes-notifications li:hover {
  background-color: #cce0ff;
  color: #0055a5;
}

.mes-notifications li span {
  font-weight: 600;
  color: #333;
}

.mes-notifications .date {
  font-style: italic;
  color: #999;
}
</style>
