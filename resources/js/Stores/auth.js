import { defineStore } from 'pinia';
import { usePage } from '@inertiajs/vue3';

export const useAuthStore = defineStore('auth', {
    getters: {
        student: () => usePage().props.auth?.student ?? null,
        admin: () => usePage().props.auth?.admin ?? null,
        isStudentLoggedIn: () => !!usePage().props.auth?.student,
        isAdminLoggedIn: () => !!usePage().props.auth?.admin,
    },
});
