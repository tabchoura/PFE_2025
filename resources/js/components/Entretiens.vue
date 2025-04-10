<template>
  <div class="entretiens">
    <h2>🗓️ Mes entretiens</h2>
    <p>Ici s’afficheront les informations liées à vos entretiens.</p>
    <Calendrier />
  </div>
</template>

<script>
import Calendrier from "./Calendrier.vue";

export default {
  name: "Entretiens",
  components: {
    Calendrier,
  },
};
</script>

<style>
.entretiens {
  padding: 1rem;
}
</style>
