import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

/**
 * Lightweight EN/BN translation composable — no external i18n library
 * needed. Translations are shared into every Inertia response by
 * HandleInertiaRequests (see lang/bn.json and lang/en.json) so there is
 * zero extra network request; switching language is a single POST that
 * re-renders the current page with the new dictionary attached.
 *
 * Usage:
 *   const { t, locale, switchLocale } = useI18n();
 *   t('nav.dashboard')                 -> "ড্যাশবোর্ড" / "Dashboard"
 *   t('auth.welcome_flash', { name })  -> replaces :name in the string
 */
export function useI18n() {
    const page = usePage();

    const locale = computed(() => page.props.locale ?? 'bn');
    const messages = computed(() => page.props.translations ?? {});

    function t(key, replacements = {}) {
        const parts = key.split('.');
        let value = messages.value;

        for (const part of parts) {
            value = value?.[part];
            if (value === undefined) {
                return key; // fall back to the key itself so missing
                            // translations are visibly obvious in dev
            }
        }

        if (typeof value === 'string') {
            for (const [k, v] of Object.entries(replacements)) {
                value = value.replaceAll(`:${k}`, v);
            }
        }

        return value;
    }

    function switchLocale(newLocale) {
        if (newLocale === locale.value) return;
        router.post(`/language/${newLocale}`, {}, { preserveScroll: true, preserveState: false });
    }

    return { t, locale, switchLocale };
}
