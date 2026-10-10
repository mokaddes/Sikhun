import { defineStore } from 'pinia';
import axios from 'axios';

export const useThemeStore = defineStore('theme', {
    state: () => ({
        mode: typeof localStorage !== 'undefined'
            ? localStorage.getItem('sikhun_theme') || 'dark'
            : 'dark',
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

            // Persist to backend for authenticated students only. The admin
            // panel has no theme endpoint, so it stays localStorage-only.
            if (typeof window !== 'undefined' && window.location.pathname.startsWith('/admin')) {
                return;
            }

            // Silently ignore failures (e.g. guest visitor, network hiccup) —
            // the localStorage value is always the source of truth client-side.
            try {
                await axios.put('/profile/theme', { theme_mode: mode });
            } catch (e) {
                // not authenticated yet — fine
            }
        },

        cycle() {
            const order = ['light', 'dark'];
            const next = order[(order.indexOf(this.mode) + 1) % order.length];
            this.setMode(next);
        },

        applyTheme() {
            const root = document.documentElement;
            const dark = this.mode === 'dark'
                || (this.mode === 'system' && window.matchMedia?.('(prefers-color-scheme: dark)').matches);

            dark ? root.classList.add('dark') : root.classList.remove('dark');
        },
    },
});
