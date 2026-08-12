<template>
  <section class="about-section">
    <div class="about-container">
      <div class="about-content">
        <h2 class="section-title">À propos de <span class="highlight">JobGO</span></h2>
        <p class="intro">
          JobGo est une plateforme innovante dédiée au recrutement en ligne. Conçue pour
          simplifier et accélérer le processus d'embauche, elle met en relation les
          recruteurs et les candidats de manière intuitive et efficace. Grâce à des outils
          avancés et une interface fluide, JobGo optimise la gestion des candidatures, la
          création des CV, et facilite l'accès aux opportunités professionnelles.
        </p>

        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">🚀</div>
            <h3>Rapidité</h3>
            <p>Processus de recrutement accéléré grâce à nos outils intelligents</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">🎯</div>
            <h3>Précision</h3>
            <p>Matching précis entre candidats et offres d'emploi</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">💡</div>
            <h3>Innovation</h3>
            <p>Technologies avancées pour une expérience utilisateur optimale</p>
          </div>
        </div>

        <div class="why-choose-us">
          <h3 class="section-subtitle">
            Pourquoi choisir <span class="highlight">JobGO</span>
          </h3>
          <p>
            Nos clients apprécient notre plateforme de recrutement en ligne pour sa
            simplicité et son efficacité. En utilisant notre plateforme, ils bénéficient
            d'un processus de recrutement fluide et rapide, facilitant la recherche et la
            sélection des meilleurs talents. Notre engagement envers l'innovation
            technologique assure une expérience utilisateur optimale, tandis que notre
            équipe dédiée offre un soutien personnalisé à chaque étape du processus.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "AboutUs",
};
</script>

<style scoped>
/* Variables CSS */
:root {
  --primary-color: #3a5a99;
  --primary-light: #4b7bec;
  --secondary-color: #f7b731;
  --text-dark: #2c3e50;
  --text-medium: #5a5a5a;
  --text-light: #7f8c8d;
  --bg-gradient: linear-gradient(145deg, #f5f7fa 0%, #e4e8f0 100%);
  --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

/* Base Styles */
.about-section {
  background: var(--bg-gradient);
  padding: 6rem 0;
  position: relative;
  overflow: hidden;
}

.about-section::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #e0eafc, #cfdef3); /* Dégradé léger */

  opacity: 0.5;
  z-index: 0;
}

.about-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 1;
}

.about-content {
  background: white;
  border-radius: 16px;
  padding: 4rem;
  box-shadow: var(--card-shadow);
  position: relative;
  overflow: hidden;
}

.about-content::after {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  width: 200px;
  height: 200px;
  background: radial-gradient(
    circle,
    rgba(58, 90, 153, 0.1) 0%,
    rgba(58, 90, 153, 0) 70%
  );
  z-index: -1;
}

/* Typography */
.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 2rem;
  text-align: center;
  position: relative;
  line-height: 1.2;
}

.section-title::after {
  content: "";
  display: block;
  width: 80px;
  height: 4px;
  background: var(--secondary-color);
  margin: 1rem auto;
  border-radius: 2px;
}

.highlight {
  color: #3d63ea;
}

.section-subtitle {
  font-size: 1.8rem;
  color: var(--primary-color);
  margin: 3rem 0 1.5rem;
  text-align: center;
  font-weight: 600;
}

.intro,
.why-choose-us p {
  color: var(--text-medium);
  line-height: 1.8;
  font-size: 1.1rem;
  margin-bottom: 2rem;
  text-align: center;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

/* Features Grid */
.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin: 3rem 0;
}

.feature-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  transition: var(--transition);
  border: 1px solid rgba(0, 0, 0, 0.03);
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
}

.feature-card h3 {
  color: var(--primary-color);
  font-size: 1.3rem;
  margin-bottom: 1rem;
}

.feature-card p {
  color: var(--text-light);
  font-size: 1rem;
  line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .about-content {
    padding: 3rem;
  }

  .section-title {
    font-size: 2.2rem;
  }
}

@media (max-width: 768px) {
  .about-section {
    padding: 4rem 0;
  }

  .about-content {
    padding: 2rem;
  }

  .section-title {
    font-size: 1.8rem;
  }

  .section-subtitle {
    font-size: 1.5rem;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .intro,
  .why-choose-us p {
    text-align: left;
  }
}

@media (max-width: 480px) {
  .about-content {
    padding: 1.5rem;
  }

  .section-title {
    font-size: 1.6rem;
  }
}
</style>
