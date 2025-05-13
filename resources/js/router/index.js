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
import EntretiensRecruteurs from '@/components/Entretiensrecruteurs.vue';
import Voircv from '@/components/Voircv.vue';
import CreateLettre from '@/components/CreateLettre.vue';
import Test from '@/components/Test.vue';
import Mesnotifications from '@/components/MesNotifications.vue';
import Candidaturesrecruteur from '../components/Candidaturesrecruteur.vue';
import Offresrecruteur from '../components/Offresrecruteur.vue';
import Ajouteroffre from '../components/Ajouteroffre.vue';
import Modifieroffre from '../components/Modifieroffre.vue';
import Supprimeroffre from '../components/Supprimeroffre.vue';
import Voirdetails from '../components/Voirdetails.vue';  // Importation de Voirdetails
import LoginCandidat from '../components/LoginCandidat.vue';
import LoginRecruteur from '../components/LoginRecruteur.vue';
import Postuler from '../components/Postuler.vue';
import Ajouterimage from '../components/Ajouterimage.vue';
import Modifiercv from '../components/Modifiercv.vue';
import Mescv from '../components/Mescv.vue';
import Mesoffres from '../components/Mesoffres.vue';

import Confirmationpostuler from '../components/Confirmationpostuler.vue';
import Creercv from '../components/Creercv.vue';
// import Aftercvpostuler from '../components/Aftercvpostuler.vue'
import DetailsCandidature from '../components/DetailsCandidature.vue';


const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/offres', name: 'Offres', component: Offres },
  { path: '/authentification', name: 'Authentification', component: Authentification },
  { path: '/registercandidat', name: 'RegisterCandidat', component: RegisterCandidat },
  { path: '/registerrecruteur', name: 'RegisterRecruteur', component: RegisterRecruteur },
  { path: '/apropos', name: 'Apropos', component: Apropos },


  { path: '/ajouterimage', name: 'Ajouterimage', component:Ajouterimage },

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
// router/index.ts
{
  path: '/planifier-entretien/:candidatureId',
  name: 'PlanifierEntretien',
  component: EntretiensRecruteurs,  // Le composant qui gère la planification
  props: true  // Passe candidatureId en prop
}

,
  
  { path: '/mesoffres', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Mesoffres', component: Mesoffres }
  ]},
  { path: '/mescv', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Mescv', component: Mescv }
  ]},
  { path: '/creercv', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'CreerCv', component: Creercv }
  ]},

  // { path: '/aftercvpostuler', component: CompteCandidat, meta: { requiresAuth: true }, children: [
  //   { path: '', name: 'Aftercvpostuler', component: Aftercvpostuler }
  // ]},
  {
    path: '/candidatures/:candidatureId',
    component: CompteRecruteur,
    meta: { requiresAuth: true, role: 'recruteur' },
    children: [
      {
        path: '',
        name: 'DetailsCandidature',
        component: DetailsCandidature
      }
    ]
  },

  // Vue recruteur
 
  
  { path: '/voircv/:id', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'VoirCv', component: Voircv }
  ]},
 

  
  
    {
    path: '/modifiercv/:id',          // segment dynamique
    name: 'Modifiercv',               // nom EXACT
    component: Modifiercv,
    meta: { requiresAuth: true },
    props: true                   // si tu préfères passer l’id en prop
  }, 
   
   // … vos autres routes …
   {
    path: '/postuler/confirm/:offerId',
    name: 'Confirmationpostuler',
    component: Confirmationpostuler,
    props: true,
    meta: { requiresAuth: true }
  },
  
,  
  // { path: '/CreateLettre', component: CompteCandidat, meta: { requiresAuth: true }, children: [
  //   { path: '', name: 'CreateLettre', component: CreateLettre }
  // ]},
  { path: '/voirdetails/:id' , component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Voirdetails', component: Voirdetails }
  ]},

  { path: '/postuler/:id', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Postuler', component: Postuler }
  ]}, 
  { path: '/LoginCandidat', name: 'LoginCandidat', component: LoginCandidat },
  { path: '/LoginRecruteur', name: 'LoginRecruteur', component: LoginRecruteur },

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
  { path: '/ajouteroffre', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Ajouteroffre', component: Ajouteroffre }
  ]},
  { path: '/modifieroffre/:id', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Modifieroffre', component: Modifieroffre ,  props: true, }
  ]},
  { path: '/supprimeroffre/:id', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Supprimeroffre', component: Supprimeroffre ,  props: true, }
  ]},

  // Détails de l'offre

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
