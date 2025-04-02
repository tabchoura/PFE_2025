import { createApp } from 'vue'
import App from './components/App.vue'
import router from './router'

const app = createApp(App)

app.use(router)
app.mount('#app')
import axios from 'axios';

axios.defaults.baseURL = 'http://localhost:8000';
axios.defaults.withCredentials = true;
axios.defaults.headers.common['Accept'] = 'application/json';
