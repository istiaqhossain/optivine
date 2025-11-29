import path from "path";
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), tailwindcss()],
  build: {
    outDir: __dirname + "/../../../build/admin/vue",
    assetsDir: "",
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: path.resolve(__dirname, "src/main.ts"),
      output: {
        entryFileNames: "js/script.js",
        chunkFileNames: "js/script-[hash].js",
        assetFileNames: ({ name }) => {
          if (/\.(css)$/.test(name ?? "")) {
            return "css/style[extname]";
          }
          return "assets/asset-[hash][extname]";
        },
      },
    },
  },
});
