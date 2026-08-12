// src/axios.js
import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:8000",
  withCredentials: false          // ← en mode token, on n’utilise pas les cookies
});

// ➜ Ajoute automatiquement le token à chaque requête
api.interceptors.request.use(config => {
  const session = JSON.parse(localStorage.getItem("userSession") || "{}");
  if (session.token) {
    config.headers.Authorization = `Bearer ${session.token}`;
  }
  return config;
});

export default api;