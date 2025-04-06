<template>
  <div>
    <h2>📄 Mes CV & Lettres</h2>

    <div class="buttons">
      <button @click="active = 'cv'" :class="{ active: active === 'cv' }">
        Créer un CV
      </button>
      <button @click="active = 'lettre'" :class="{ active: active === 'lettre' }">
        Créer une lettre
      </button>
    </div>

    <div class="editor">
      <CreateCv v-if="active === 'cv'" />
      <CreateLettre v-if="active === 'lettre'" />
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import CreateCv from "./CreateCv.vue";
import CreateLettre from "./CreateLettre.vue";

const active = ref("cv");
</script>

<style scoped>
.buttons {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.buttons button {
  padding: 10px 16px;
  border: none;
  background: #0055a5;
  color: white;
  border-radius: 6px;
  cursor: pointer;
}

.buttons button.active {
  background-color: #003d80;
}
</style>
