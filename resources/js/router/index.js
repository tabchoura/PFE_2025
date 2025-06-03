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
import EntretiensRecruteurs from '@/components/Entretiensrecruteurs.vue';
import Voircv from '@/components/Voircv.vue';
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

import Creercv from '../components/Creercv.vue';
import DetailsCandidature from '../components/DetailsCandidature.vue';
import detailsoffre_recruteur from '../components/detailsoffre_recruteur.vue';

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/offres', name: 'Offres', component: Offres },
  { path: '/authentification', name: 'Authentification', component: Authentification },
  { path: '/apropos', name: 'Apropos', component: Apropos },

  //candidat 
  { path: '/registercandidat', name: 'RegisterCandidat', component: RegisterCandidat },

    { path: '/LoginCandidat', name: 'LoginCandidat', component: LoginCandidat },

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
  
  { path: '/creercv', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'CreerCv', component: Creercv }
  ]},

  { path: '/ajouterimage', name: 'Ajouterimage', component:Ajouterimage },

   { path: '/mescv', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Mescv', component: Mescv }
  ]},
 { path: '/voircv/:id', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'VoirCv', component: Voircv }
  ]},
   { path: '/modifiercv/:id', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Modifiercv', component: Modifiercv }
  ]},
 

  { path: '/mesoffres', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Mesoffres', component: Mesoffres }
  ]},
   { path: '/postuler/:id', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Postuler', component: Postuler }
  ]}, 

  { path: '/candidature', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Candidature', component: Candidature }
  ]},

 
  { path: '/voirdetails/:id' , component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Voirdetails', component: Voirdetails }
  ]},


   { path:'/candidat/offres/:id', component: CompteCandidat, meta: { requiresAuth: true }, children: [
    { path: '',  name: 'CandidatVoirdetails',
  component: Voirdetails,
  props: true, }
  ]},


  



  



// Recruteur 
    { path: '/registerrecruteur', name: 'RegisterRecruteur', component: RegisterRecruteur },

  { path: '/LoginRecruteur', name: 'LoginRecruteur', component: LoginRecruteur },

  
  { path: '/CompteRecruteur', name: 'CompteRecruteur', component: CompteRecruteur, meta: { requiresAuth: true } },


  { path: '/monprofilerecruteur', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Monprofilerecruteur', component: Monprofilerecruteur }
  ]},
  { path: '/offresrecruteur', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Offresrecruteur', component: Offresrecruteur },

  { path: '/modifieroffre/:id', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Modifieroffre', component: Modifieroffre ,  props: true, }
  ]},
  { path: '/supprimeroffre/:id', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Supprimeroffre', component: Supprimeroffre ,  props: true, }
  ]},

  {  path: '/recruteur/offres/:id',
 component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '',   name: 'RecruteurVoirdetails',  component: detailsoffre_recruteur,
  props: true, }
  ]},
    
  ]},
  { path: '/Candidaturesrecruteur', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'Candidaturesrecruteur', component: Candidaturesrecruteur }
  ]},

{
  path: '/candidatures/:candidatureId',
  component: CompteRecruteur,
  children: [
    { path: '', name: 'DetailsCandidature', component: DetailsCandidature }
  ]
}
,

, { path:  '/planifier-entretien/:candidatureId', component: CompteRecruteur, meta: { requiresAuth: true }, children: [
    { path: '', name: 'PlanifierEntretien', component:  EntretiensRecruteurs }
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
