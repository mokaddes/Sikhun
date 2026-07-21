/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                heading: ['Syne', 'sans-serif'],
                sans: ['DM Sans', 'sans-serif'],
                bangla: ['Hind Siliguri', 'sans-serif'],
                mono: ['JetBrains Mono', 'monospace'],
            },
            colors: {
                primary: {
                    DEFAULT: 'var(--primary)',
                    hover: 'var(--primary-hover)',
                },
                secondary: 'var(--secondary)',
                accent: 'var(--accent)',
                surface: 'var(--surface)',
                surface2: 'var(--surface2)',
            },
            borderColor: {
                DEFAULT: 'var(--border)',
            },
        },
    },
    plugins: [],
};
