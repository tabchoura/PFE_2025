import { createApp } from 'vue';
import App from './components/App.vue'; // ou './App.vue' selon ton arborescence
import router from './router';
import axios from 'axios';
import { ProCalendar } from '@lbgm/pro-calendar-vue';
import '@lbgm/pro-calendar-vue/style'; // important pour le style du calendrier
import 'vue-toastification/dist/index.css';
import Toast from 'vue-toastification' 

// Configuration de Axios
axios.defaults.baseURL = 'http://localhost:8000';
axios.defaults.withCredentials = true;//active envoi auto des cookies necessaire pour auth 
axios.defaults.headers.common['Accept'] = 'application/json';//Accept pour tt requete http (kif appjson)

// Création de l'application Vue
const app = createApp(App);

// Plugins
app.use(Toast, { position: 'top-right', timeout: 3000 })

app.use(router);
app.use(ProCalendar);

// Montage de l'application
app.mount('#app');

