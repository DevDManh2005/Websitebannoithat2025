import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/frontend/home.css",
                "resources/js/frontend/home.js",
                "resources/css/app.css",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Cho phép truy cập từ ngoài localhost
        port: 5173, // Cổng mặc định của Vite
        hmr: {
            host: '6bdd7b71c655.ngrok-free.app', // URL ngrok của bạn
            protocol: 'https',
        },
    },
});
