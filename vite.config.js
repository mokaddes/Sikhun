import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '@/utils': '/resources/js/Utils',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('apexcharts')) {
                            return 'vendor-charts';
                        }
                        if (id.includes('jspdf') || id.includes('html2canvas')) {
                            return 'vendor-pdf';
                        }
                        if (id.includes('katex')) {
                            return 'vendor-math';
                        }
                        if (id.includes('@tiptap') || id.includes('prosemirror')) {
                            return 'vendor-editor';
                        }
                        if (id.includes('@heroicons') || id.includes('lucide-vue-next')) {
                            return 'vendor-icons';
                        }
                        if (id.includes('@inertiajs') || id.includes('vue')) {
                            return 'vendor-framework';
                        }
                    }
                },
            },
        },
        chunkSizeWarningLimit: 700,
    },
});
