import { defineStore } from 'pinia';
import axios from 'axios';
import { createEcho } from '@/echo';

export const useNotificationStore = defineStore('notifications', {
    state: () => ({
        items: [],
        unreadCount: 0,
        echo: null,
    }),

    actions: {
        async fetchInitial() {
            const { data } = await axios.get('/notifications');
            this.items = data.data;
            this.unreadCount = data.unread_count;
        },

        /** Called once after login, from app.js — wires up the live feed. */
        listen(studentId) {
            if (this.echo) return; // already listening

            try {
                this.echo = createEcho();
                this.echo.private(`student.${studentId}`).listen('.notification.new', (payload) => {
                    this.items.unshift(payload);
                    this.unreadCount++;
                });
            } catch (e) {
                // Reverb not running locally — fail silently, the bell just
                // won't update live. Polling fallback: fetchInitial() on page load.
                console.warn('Realtime notifications unavailable:', e.message);
            }
        },

        async markRead(id) {
            await axios.put(`/notifications/${id}/read`);
            const item = this.items.find((n) => n.id === id);
            if (item && !item.read_at) {
                item.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        },

        async markAllRead() {
            await axios.put('/notifications/read-all');
            this.items.forEach((n) => (n.read_at = n.read_at ?? new Date().toISOString()));
            this.unreadCount = 0;
        },
    },
});
