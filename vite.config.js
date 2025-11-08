import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import path from "path";
export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",

                "resources/css/personalization.css",
                "resources/js/app.js",
                "resources/js/app-cetak.js",
                "resources/js/theme/app.min.js",
                "resources/js/vendor.min.js",
            ],
            refresh: false,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
    resolve: {
        alias: {
            $: "jquery",
            moment: path.resolve(__dirname, "node_modules/moment/moment"),
        },
    },
    build: {
        // 🔹 Pisahkan library besar ke file vendor terpisah
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: [
                        "jquery",
                        "preline",
                        "simplebar",
                        "sweetalert2",
                        "tom-select",
                        "dayjs", // ganti moment ke dayjs
                    ],
                },
            },
        },

        // 🔹 Tambah batas peringatan ukuran
        // chunkSizeWarningLimit: 1500,

        // 🔹 Aktifkan minifikasi agresif
        minify: "esbuild",
    },
});
