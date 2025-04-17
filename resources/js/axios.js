import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000', // ou ton URL backend
  withCredentials: true, // ✅ Important pour Sanctum
});

export default api;
