<template>
  <div class="hero-section">
    <div class="text-content">
      <h1 class="title">Recrutez <span class="span"> vite</span> et <span class="span">simplemet</span> !</h1>
      <p class="description">JobGo est une plateforme innovante dédiée au recrutement en ligne. Conçue pour simplifier et accélérer le processus d’embauche, elle met en relation les recruteurs et les candidats de manière intuitive et efficace. Grâce à des outils avancés et une interface fluide, JobGo optimise la gestion des candidatures et facilite l’accès aux opportunités professionnelles.</p>
      <p class="inscription">
  ➡️ <strong>Pour plus de détails, <a @click="page = 'register'" href="#">inscrivez-vous dès maintenant</a>.</strong>
</p>
 </div>
    
    <div class="image-content">
    
      <img class="banner-img" src="C:\laragon\www\PFE_2025\public\images\recrutement.jpg" alt="Recrutement">

    </div>
  </div>
</template>

<script>
export default {
  name: "Home"
};
</script>

<style scoped>
.description {
    font-size: 18px;
    color: #444;
    margin-bottom: 20px;
    line-height: 1.6;
  }
.hero-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  align-items: center;
  padding: clamp(2rem, 5vw, 4rem);
  max-width: 1440px;
  margin: 0 auto;
}



.image-content img {
  
max-width:85%; /* L'image occupe plus de place que le texte */
  text-align: right; /* Aligner l'image à droite */
}
.banner-img {
  width: 100%;
  height: auto;
  border-radius: 16px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  aspect-ratio: 16/9;
  object-fit: cover;
}
.title {
  color: #007bffbc;
  font-size: clamp(2rem, 5vw, 3.5rem);
  line-height: 1.2;
  margin-bottom: 1.5rem;
}
.btn{
  border: none;
  font-size: 18px;
  cursor: pointer;
  padding:20px;
  color: white;
  background-color: #007bffbc;
  display: block;
  justify-content: center;
}
.span{
  color: 
#3b8884
;
}
  
  /* ✅ Texte d'inscription */
  .inscription {
    font-size: 18px;
    color: #007bff;
    font-weight: bold;
  }
  
  /* ✅ Lien vers l'inscription */
  .inscription a {
    text-decoration: none;
    color: #0056b3;
    transition: 0.3s;
  }
  
  .inscription a:hover {
    text-decoration: underline;
  }
</style>
