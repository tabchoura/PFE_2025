<template>
  <div class="mes-tests">
    <h2>Mes Tests</h2>
    <ul>
      <li v-for="test in tests" :key="test.id">
        <span>{{ test.name }}</span> - <span class="status">{{ test.status }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from "vue";

const tests = ref([
  { id: 1, name: "Test d'évaluation", status: "Terminé" },
  { id: 2, name: "Test technique", status: "En cours" },
]);
</script>
<style scoped>
/* Styles pour la section Mes Tests */
.mes-tests {
  padding: 20px;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.mes-tests h2 {
  color: #0055a5;
  font-size: 1.6rem;
  margin-bottom: 15px;
}

.mes-tests ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.mes-tests li {
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 10px;
  background-color: #f4f6f9;
  color: #333;
  font-size: 1rem;
  transition: background-color 0.3s, color 0.3s;
}

.mes-tests li:hover {
  background-color: #cce0ff;
  color: #0055a5;
}

.mes-tests li span {
  font-weight: 600;
  color: #333;
}

.mes-tests .status {
  font-style: italic;
  color: #999;
}
</style>
