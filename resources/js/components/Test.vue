<template>
  <div class="tests-container">
    <h1 class="title">🧪 Mes Tests</h1>

    <div v-if="tests.length" class="test-list">
      <div v-for="(test, index) in tests" :key="index" class="test-card">
        <div class="test-info">
          <h2 class="test-title">{{ test.nom }}</h2>
          <p class="test-date">📅 Date : {{ test.date }}</p>
          <p class="test-type">📝 Type : {{ test.type }}</p>
        </div>
        <span :class="['badge', test.statut.toLowerCase()]">{{ test.statut }}</span>
      </div>
    </div>

    <p v-else class="no-tests">Aucun test enregistré.</p>
  </div>
</template>

<script>
export default {
  name: "Test",
  data() {
    return {
      tests: [
        {
          nom: "Test logique et raisonnement",
          date: "2025-04-15",
          type: "En ligne",
          statut: "Terminé",
        },
        {
          nom: "Test technique Vue.js",
          date: "2025-04-20",
          type: "Présentiel",
          statut: "Prévu",
        },
        {
          nom: "Test anglais B2",
          date: "2025-03-30",
          type: "En ligne",
          statut: "Refusé",
        },
      ],
    };
  },
};
</script>

<style scoped>
.tests-container {
  max-width: 900px;
  margin: 2rem auto;
  padding: 1rem;
}

.title {
  text-align: center;
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 2rem;
}

.test-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.test-card {
  background-color: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  padding: 1.25rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: 0.2s ease;
}

.test-card:hover {
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
}

.test-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.test-date,
.test-type {
  color: #6b7280;
  font-size: 0.95rem;
  margin-top: 0.4rem;
}

.badge {
  padding: 0.4rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.8rem;
  font-weight: bold;
  text-transform: uppercase;
}

.badge.terminé {
  background-color: #d1fae5;
  color: #065f46;
}

.badge.prévu {
  background-color: #fef3c7;
  color: #92400e;
}

.badge.refusé {
  background-color: #fee2e2;
  color: #991b1b;
}

.no-tests {
  text-align: center;
  color: #6b7280;
  font-style: italic;
  margin-top: 2rem;
}
</style>
