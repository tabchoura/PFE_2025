import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  resolve: {
    alias: {
      '@': '/resources/js', // Assurez-vous que l'alias @ pointe vers resources/js
    },
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'], // ← ici c’est ton point d’entrée
      refresh: true,
    }),
    vue(),
  ],
});
