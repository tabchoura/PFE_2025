import { createApp } from 'vue';
import App from './components/App.vue'; // ✅ Chemin relatif

// 🔥 Import de Bootstrap (CSS et JS)
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// 🚀 Création de l'application Vue
const app = createApp(App);
app.mount('#app');
