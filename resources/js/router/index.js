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
  { path: '/CompteCandidat', name: 'CompteCandidat', component: CompteCandidat },
  { path: '/mon-profile', name: 'Monprofile', component: Monprofile },
  { path: '/candidature', name: 'Candidature', component: Candidature },
  { path: '/CompteRecruteur', name: 'CompteRecruteur', component: CompteRecruteur },
  { path: '/Monprofilerecruteur', name: 'Monprofilerecruteur', component: Monprofilerecruteur },

  // 🔍 Page de détail dynamique d'une offre
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

// ✅ Navigation guard
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const userSession = JSON.parse(localStorage.getItem('userSession') || sessionStorage.getItem('userSession') || 'null');

  if (requiresAuth && !userSession) {
    next('/authentification');
  } else if (to.path === '/authentification' && userSession) {
    if (userSession.type === 'recruteur') {
      next('/CompteRecruteur');
    } else {
      next('/CompteCandidat');
    }
  } else {
    next();
  }
});

export default router;
