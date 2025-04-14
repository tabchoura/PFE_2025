<template>
  <section class="about-container">
    <div class="about-content">
      <h4>JobGO</h4>
      <p class="intro">
        JobGo est une plateforme innovante dédiée au recrutement en ligne. Conçue pour
        simplifier et accélérer le processus d’embauche, elle met en relation les
        recruteurs et les candidats de manière intuitive et efficace. Grâce à des outils
        avancés et une interface fluide, JobGo optimise la gestion des candidatures, la
        création des CV, et facilite l’accès aux opportunités professionnelles.
      </p>

      <h3>Pourquoi devriez-vous nous choisir ?</h3>
      <p class="why-choose-us">
        Nos clients apprécient notre plateforme de recrutement en ligne pour sa simplicité
        et son efficacité. En utilisant notre plateforme, ils bénéficient d'un processus
        de recrutement fluide et rapide, facilitant la recherche et la sélection des
        meilleurs talents. Notre engagement envers l'innovation technologique assure une
        expérience utilisateur optimale, tandis que notre équipe dédiée offre un soutien
        personnalisé à chaque étape du processus. Grâce à notre approche centrée sur le
        client et notre capacité à comprendre leurs besoins uniques, nous nous efforçons
        de créer des partenariats durables basés sur la confiance et la réussite mutuelle.
      </p>
    </div>
  </section>
</template>

<script>
export default {
  name: "AboutUs",
};
</script>

<style scoped>
/* ===== Container for the About Us section ===== */
.about-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  max-width: 1200px;
  margin: 120px auto;
  padding: 40px 20px;
  background-color: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.about-content {
  max-width: 800px;
  margin-bottom: 40px;
  text-align: center;
}

h2 {
  color: #2c3e50;
  font-size: 2.2rem;
  margin-bottom: 10px;
}

h4 {
  color: #5a5a5a;
  font-size: 1.6rem;
  margin-bottom: 20px;
}

.intro {
  color: #555;
  line-height: 1.6;
  font-size: 1.1rem;
  text-align: justify;
  margin-bottom: 20px;
}

h3 {
  color: #3a5a99;
  font-size: 1.5rem;
  margin-top: 20px;
}

.why-choose-us {
  color: #555;
  line-height: 1.7;
  font-size: 1.1rem;
  text-align: justify;
  margin-top: 15px;
}

.about-image {
  max-width: 100%;
  text-align: center;
}

.about-image img {
  width: 100%;
  max-width: 700px;
  border-radius: 8px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* ===== Responsive Styles ===== */
@media (max-width: 768px) {
  .about-container {
    padding: 20px;
  }

  h2 {
    font-size: 1.8rem;
  }

  h4 {
    font-size: 1.4rem;
  }

  .intro,
  .why-choose-us {
    font-size: 1rem;
  }

  .about-image img {
    max-width: 100%;
  }
}
</style>
