import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

/**
 * Baudigit Vite Configuration
 *
 * Each page that needs React gets its own entry point.
 * Built assets land in client/dist/ which is exposed via composer "expose".
 *
 * Usage in SilverStripe templates:
 *   <script type="module" src="{$ThemeDir('/vendor/kalakotra/silverstripe-baudigit')}/client/dist/invoice.js"></script>
 */
export default defineConfig({
  plugins: [react()],

  root: 'client',

  build: {
    outDir: 'dist',
    emptyOutDir: true,

    // Multi-page app — one bundle per feature
    rollupOptions: {
      input: {
        // Invoice article management (AJAX forms)
        invoice: resolve(__dirname, 'client/src/pages/invoice.jsx'),

        // Offer article management
        offer: resolve(__dirname, 'client/src/pages/offer.jsx'),

        // Project task/document management
        project: resolve(__dirname, 'client/src/pages/project.jsx'),

        // Dashboard charts + stats
        dashboard: resolve(__dirname, 'client/src/pages/dashboard.jsx'),

        // Worker / timesheet view
        worker: resolve(__dirname, 'client/src/pages/worker.jsx'),
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: 'chunks/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash][extname]',
      },
    },
  },

  // So imports resolve relative to client/src/
  resolve: {
    alias: {
      '@': resolve(__dirname, 'client/src'),
    },
  },
});
