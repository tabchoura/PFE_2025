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
import Entretiens from '@/components/Entretiens.vue';
import CreateCv from '@/components/CreateCv.vue';
import CreateLettre from '@/components/CreateLettre.vue';
import Test from '@/components/Test.vue';
import Mesnotifications from '@/components/MesNotifications.vue';
import Candidaturesrecruteur from '../components/Candidaturesrecruteur.vue';
import Offresrecruteur from '../components/Offresrecruteur.vue'

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/offres', name: 'Offres', component: Offres },
  { path: '/authentification', name: 'Authentification', component: Authentification },
  { path: '/registercandidat', name: 'RegisterCandidat', component: RegisterCandidat },
  { path: '/registerrecruteur', name: 'RegisterRecruteur', component: RegisterRecruteur },
  { path: '/apropos', name: 'Apropos', component: Apropos },

  // Candidat
  { 
    path: '/CompteCandidat', 
    component: CompteCandidat,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'CompteCandidat', component: CompteCandidat }
    ]
  },
  { path: '/monprofile', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Monprofile', component: Monprofile }
  ]},
  { path: '/candidature', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Candidature', component: Candidature }
  ]},
  { path: '/entretiens', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Entretiens', component: Entretiens }
  ]},
  { path: '/CreateCv', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'CreateCv', component: CreateCv }
  ]},
  { path: '/CreateLettre', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'CreateLettre', component: CreateLettre }
  ]},
  { path: '/Test', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Test', component: Test }
  ]},
  { path: '/Mesnotifications', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Mesnotifications', component: Mesnotifications }
  ]},

  // Recruteur
  { path: '/CompteRecruteur', name: 'CompteRecruteur', component: CompteRecruteur, meta: { requiresAuth: true } },
  { path: '/monprofilerecruteur', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Monprofilerecruteur', component: Monprofilerecruteur }
  ]},
  { path: '/offresrecruteur', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Offresrecruteur', component: Offresrecruteur }
  ]},
  { path: '/Candidaturesrecruteur', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Candidaturesrecruteur', component: Candidaturesrecruteur }
  ]},
  { path: '/Entretiensrecruteurs', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Entretiensrecruteurs', component: Entretiens }
  ]},

  // Catch-all
  { path: '/:pathMatch(.*)*', redirect: '/' }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const requiresAuth = to.meta.requiresAuth;
  const sessionData = localStorage.getItem('userSession') || sessionStorage.getItem('userSession');
  const isAuthenticated = !!sessionData;

  if (requiresAuth && !isAuthenticated) {
    next('/authentification');
  } else if (to.path === '/authentification' && isAuthenticated) {
    const session = JSON.parse(sessionData);
    const role = session.role || session.type || session.user?.role;
    next(role === 'recruteur' ? '/monprofilerecruteur' : '/monprofile');
  } else {
    next();
  }
});

export default router;
