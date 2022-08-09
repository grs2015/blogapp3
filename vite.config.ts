/// <reference types="vitest" />

import { defineConfig } from "vite";
import i18n from "laravel-vue-i18n/vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

import { quasar, transformAssetUrls } from "@quasar/vite-plugin";

export default defineConfig({
    plugins: [
        laravel(["resources/js/app.js"]),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        i18n(),
        quasar({
            sassVariables: "resources/css/quasar-variables.sass",
        }),
    ],
    resolve: {
        alias: {
            "@": "/resources/js",
            ziggy: "/vendor/tightenco/ziggy/dist/vue",
        },
    },
    test: {
        globals: true,
        environment: "jsdom",
    },
    optimizeDeps: {
        include: ["ziggy"],
    },
});
