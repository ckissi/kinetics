import { defineConfig } from 'astro/config';

// https://astro.build/config
export default defineConfig({
  site: 'https://kinetics.dev',
  server: {
    port: process.env.PORT ? Number(process.env.PORT) : 4321,
  },
});
