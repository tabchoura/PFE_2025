
import { createRouter, createWebHistory } from 'vue-router'

import Home from '@/components/Home.vue'
import Offres from '@/components/Offres.vue'
import Authentification from '@/components/Authentification.vue'
import RegisterCandidat from '@/components/RegisterCandidat.vue'
import RegisterRecruteur from '@/components/RegisterRecruteur.vue'
import Apropos from '../components/Apropos.vue'

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/offres', name: 'Offres', component: Offres },
  { path: '/authentification', name: 'Authentification', component: Authentification },
  { path: '/RegisterCandidat', name: 'RegisterCandidat', component: RegisterCandidat },
  { path: '/RegisterRecruteur', name: 'RegisterRecruteut', component: RegisterRecruteur },
  {path :'/Apropos', name:'Apropos' , component:Apropos},



]

  
  const router = createRouter({
    history: createWebHistory(),
    routes
  })
  
  export default router