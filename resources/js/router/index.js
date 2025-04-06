// router/index.js
import { createRouter, createWebHistory } from 'vue-router'

import Home from '@/components/Home.vue'
import Offres from '@/components/Offres.vue'
import Authentification from '@/components/Authentification.vue'
import RegisterCandidat from '@/components/RegisterCandidat.vue'
import RegisterRecruteur from '@/components/RegisterRecruteur.vue'
import Apropos from '@/components/Apropos.vue'
import CompteCandidat from '@/components/CompteCandidat.vue'
import Monprofile from '@/components/Monprofile.vue'
import Candidature from '@/components/Candidature.vue'

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/offres', name: 'Offres', component: Offres },
  { path: '/authentification', name: 'Authentification', component: Authentification },
  { path: '/registercandidat', name: 'RegisterCandidat', component: RegisterCandidat },
  { path: '/registerrecruteur', name: 'RegisterRecruteur', component: RegisterRecruteur },
  { path: '/apropos', name: 'Apropos', component: Apropos },
  { path: '/compte-candidat', name: 'CompteCandidat', component: CompteCandidat },
  { path: '/mon-profile', name: 'Monprofile', component: Monprofile },
  { path: '/candidature', name: 'Candidature', component: Candidature }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
