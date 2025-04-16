import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/components/Home.vue';
import Offres from '@/components/Offres.vue';
import Authentification from '@/components/Authentification.vue';
import RegisterCandidat from '@/components/RegisterCandidat.vue';
import RegisterRecruteur from '@/components/RegisterRecruteur.vue';
import Apropos from '@/components/Apropos.vue';
import CompteCandidat from '@/components/CompteCandidat.vue';
import Monprofile from '@/components/Monprofile.vue';
import Candidature from '@/components/Candidature.vue';
import CompteRecruteur from '@/components/CompteRecruteur.vue';
import Monprofilerecruteur from '@/components/Monprofilerecruteur.vue';

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/offres', name: 'Offres', component: Offres },
  { path: '/authentification', name: 'Authentification', component: Authentification },
  { path: '/registercandidat', name: 'RegisterCandidat', component: RegisterCandidat },
  { path: '/registerrecruteur', name: 'RegisterRecruteur', component: RegisterRecruteur },
  { path: '/apropos', name: 'Apropos', component: Apropos },
  
  // Routes protégées - ajout de meta: { requiresAuth: true }
  { 
    path: '/CompteCandidat', 
    name: 'CompteCandidat', 
    component: CompteCandidat,
    meta: { requiresAuth: true } 
  },
  { 
    path: '/mon-profile', 
    name: 'Monprofile', 
    component: Monprofile,
    meta: { requiresAuth: true } 
  },
  { 
    path: '/candidature', 
    name: 'Candidature', 
    component: Candidature,
    meta: { requiresAuth: true } 
  },
  { 
    path: '/CompteRecruteur', 
    name: 'CompteRecruteur', 
    component: CompteRecruteur,
    meta: { requiresAuth: true } 
  },
  { 
    path: '/Monprofilerecruteur', 
    name: 'Monprofilerecruteur', 
    component: Monprofilerecruteur,
    meta: { requiresAuth: true } 
  },
  
  // Page de détail dynamique d'une offre
  {
    path: '/offres/:id',
    name: 'DetailsOffre',
    component: () => import('@/components/DetailsOffre.vue'),
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard amélioré
router.beforeEach((to, from, next) => {
  const requiresAuth = to.meta.requiresAuth; // Vérifie si l'authentification est requise
  const isAuthenticated = localStorage.getItem('userSession') !== null || 
                          sessionStorage.getItem('userSession') !== null; // Vérifie localStorage ET sessionStorage
  
  if (requiresAuth && !isAuthenticated) {
    // Si l'authentification est requise mais l'utilisateur n'est pas authentifié
    next('/authentification'); // Redirection vers la page d'authentification
  } else if (to.path === '/authentification' && isAuthenticated) {
    // Si l'utilisateur est déjà authentifié et essaie d'accéder à la page d'authentification
    // Rediriger vers la page appropriée selon son type d'utilisateur
    const sessionData = localStorage.getItem('userSession') || sessionStorage.getItem('userSession');
    const session = JSON.parse(sessionData);
    next(session.type === 'recruteur' ? '/CompteRecruteur' : '/CompteCandidat');
  } else {
    // Sinon, autoriser la navigation
    next();
  }
});

export default router;