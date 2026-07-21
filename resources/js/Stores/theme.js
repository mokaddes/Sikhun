import { defineStore } from 'pinia';
import axios from 'axios';

export const useThemeStore = defineStore('theme', {
    state: () => ({
        mode: typeof localStorage !== 'undefined'
            ? localStorage.getItem('sikhun_theme') || 'system'
            : 'system',
    }),

    actions: {
        init() {
            this.applyTheme();

            if (typeof window !== 'undefined' && window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    if (this.mode === 'system') this.applyTheme();
                });
            }
        },

        async setMode(mode) {
            this.mode = mode;
            localStorage.setItem('sikhun_theme', mode);
            this.applyTheme();

            // Persist to backend if the student is authenticated. Silently
            // ignore failures (e.g. guest visitor, network hiccup) — the
            // localStorage value is always the source of truth client-side.
            try {
                await axios.put('/profile/theme', { theme_mode: mode });
            } catch (e) {
                // not authenticated yet — fine
            }
        },

        cycle() {
            const order = ['light', 'dark', 'system'];
            const next = order[(order.indexOf(this.mode) + 1) % order.length];
            this.setMode(next);
        },

        applyTheme() {
            const root = document.documentElement;
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const dark = this.mode === 'dark' || (this.mode === 'system' && prefersDark);

            dark ? root.classList.add('dark') : root.classList.remove('dark');
        },
    },
});
