import { createRouter, createWebHistory } from 'vue-router'

import Home from '@/components/Home.vue'
import Offres from '@/components/Offres.vue'
import Authentification from '@/components/Authentification.vue'
import RegisterCandidat from '@/components/RegisterCandidat.vue'
import RegisterRecruteur from '@/components/RegisterRecruteur.vue'
import Apropos from '@/components/Apropos.vue'
import CompteCandidat from '@/components/CompteCandidat.vue' // Chemin corrigé pour le composant
import Monprofile from '@/components/Monprofile.vue'
import Candidature from '@/components/Candidature.vue'
import CompteRecruteur from '../components/CompteRecruteur.vue'
import Monprofilerecruteur from "../components/Monprofilerecruteur.vue"; // Si le composant est dans le même dossier

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
  {path:'/CompteRecruteur', name:'Recruteur',component:CompteRecruteur},
  {path:'/Monprofilerecruteur', name:'Monprofilerecruteur',component:Monprofilerecruteur},
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const userSession = JSON.parse(localStorage.getItem('userSession') || sessionStorage.getItem('userSession') || 'null')

  if (requiresAuth && !userSession) {
    next('/authentification')
  } else if (to.path === '/authentification' && userSession) {
    // Si l'utilisateur est déjà connecté et essaie d'accéder à la page d'authentification
    if (userSession.type === 'recruteur') {
      next('/CompteRecruteur')
    } else {
      next('/CompteCandidat')
    }
  } else {
    next()
  }
})

export default router
