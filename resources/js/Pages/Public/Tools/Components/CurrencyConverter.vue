<script setup>
import { ref, computed, onMounted } from 'vue';
import {
    BanknotesIcon,
    ArrowPathIcon,
    ArrowsRightLeftIcon,
    SparklesIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const amount = ref(100);
const fromCurrency = ref('USD');
const toCurrency = ref('BDT');
const isFetching = ref(false);
const lastUpdated = ref(new Date().toLocaleDateString('bn-BD'));

// Base USD rates (Standard reliable fallback)
const exchangeRates = ref({
    USD: 1.0,
    BDT: 122.50,
    EUR: 0.93,
    GBP: 0.79,
    SAR: 3.75,
    AED: 3.67,
    MYR: 4.45,
    INR: 84.50,
    CAD: 1.38,
    AUD: 1.52,
    SGD: 1.34,
    KWD: 0.31,
    QAR: 3.64,
    OMR: 0.38,
    JPY: 154.20,
});

const currencyDetails = {
    BDT: { nameBn: 'বাংলাদেশী টাকা', nameEn: 'Bangladeshi Taka', symbol: '৳', flag: '🇧🇩' },
    USD: { nameBn: 'মার্কিন ডলার', nameEn: 'US Dollar', symbol: '$', flag: '🇺🇸' },
    EUR: { nameBn: 'ইউরো', nameEn: 'Euro', symbol: '€', flag: '🇪🇺' },
    GBP: { nameBn: 'ব্রিটিশ পাউন্ড', nameEn: 'British Pound', symbol: '£', flag: '🇬🇧' },
    SAR: { nameBn: 'সৌদি রিয়াল', nameEn: 'Saudi Riyal', symbol: '﷼', flag: '🇸🇦' },
    AED: { nameBn: 'ইউএই দিরহাম', nameEn: 'UAE Dirham', symbol: 'د.إ', flag: '🇦🇪' },
    MYR: { nameBn: 'মালয়েশিয়ান রিঙ্গিত', nameEn: 'Malaysian Ringgit', symbol: 'RM', flag: '🇲🇾' },
    INR: { nameBn: 'ভারতীয় রুপি', nameEn: 'Indian Rupee', symbol: '₹', flag: '🇮🇳' },
    CAD: { nameBn: 'কানাডিয়ান ডলার', nameEn: 'Canadian Dollar', symbol: 'C$', flag: '🇨🇦' },
    AUD: { nameBn: 'অস্ট্রেলিয়ান ডলার', nameEn: 'Australian Dollar', symbol: 'A$', flag: '🇦🇺' },
    SGD: { nameBn: 'সিঙ্গাপুর ডলার', nameEn: 'Singapore Dollar', symbol: 'S$', flag: '🇸🇬' },
    KWD: { nameBn: 'কুয়েতি দিনার', nameEn: 'Kuwaiti Dinar', symbol: 'د.ك', flag: '🇰🇼' },
    QAR: { nameBn: 'কাতারি রিয়াল', nameEn: 'Qatari Riyal', symbol: 'ر.ق', flag: '🇶🇦' },
    OMR: { nameBn: 'ওমানি রিয়াল', nameEn: 'Omani Rial', symbol: 'ر.ع', flag: '🇴🇲' },
    JPY: { nameBn: 'জাপানিজ ইয়েন', nameEn: 'Japanese Yen', symbol: '¥', flag: '🇯🇵' },
};

onMounted(async () => {
    fetchLiveRates();
});

async function fetchLiveRates() {
    isFetching.value = true;
    try {
        const res = await fetch('https://open.er-api.com/v6/latest/USD');
        if (res.ok) {
            const data = await res.json();
            if (data && data.rates) {
                Object.keys(exchangeRates.value).forEach(curr => {
                    if (data.rates[curr]) {
                        exchangeRates.value[curr] = data.rates[curr];
                    }
                });
                lastUpdated.value = new Date().toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' });
            }
        }
    } catch (e) {
        // Fallback rates silently kept
    } finally {
        isFetching.value = false;
    }
}

function swapCurrencies() {
    const temp = fromCurrency.value;
    fromCurrency.value = toCurrency.value;
    toCurrency.value = temp;
}

const convertedAmount = computed(() => {
    const amt = Number(amount.value);
    if (isNaN(amt) || amt <= 0) return 0;

    const fromRate = exchangeRates.value[fromCurrency.value] || 1;
    const toRate = exchangeRates.value[toCurrency.value] || 1;

    // Convert from Currency -> USD -> to Currency
    const usdVal = amt / fromRate;
    const res = usdVal * toRate;
    return Number(res.toFixed(2));
});

const singleUnitRate = computed(() => {
    const fromRate = exchangeRates.value[fromCurrency.value] || 1;
    const toRate = exchangeRates.value[toCurrency.value] || 1;
    const rate = (1 / fromRate) * toRate;
    return Number(rate.toFixed(4));
});

const inverseUnitRate = computed(() => {
    const fromRate = exchangeRates.value[fromCurrency.value] || 1;
    const toRate = exchangeRates.value[toCurrency.value] || 1;
    const rate = (1 / toRate) * fromRate;
    return Number(rate.toFixed(4));
});
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <BanknotesIcon class="w-6 h-6 text-emerald-500" />
                    {{ en ? 'Live Currency Converter (BDT & Foreign FX)' : 'লাইভ মুদ্রা কনভার্টার (টাকা ও বৈদেশিক মুদ্রা)' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Accurate live currency exchange rate converter for BDT, USD, SAR, AED, EUR, GBP, MYR, and more.' : 'বাংলাদেশী টাকা, ইউএস ডলার, সৌদি রিয়াল, দিরহাম, ইউরো, পাউন্ডসহ বিশ্বব্যাপী মুদ্রার তাৎক্ষণিক নির্ভুল রূপান্তর।' }}
                </p>
            </div>

            <!-- Refresh Rates Button -->
            <button
                type="button"
                @click="fetchLiveRates"
                :disabled="isFetching"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 transition"
            >
                <ArrowPathIcon class="w-3.5 h-3.5" :class="isFetching ? 'animate-spin text-emerald-500' : ''" />
                <span>{{ en ? 'Refresh Live Rates' : 'রেট রিফ্রেশ করুন' }}</span>
            </button>
        </div>

        <!-- Conversion Form Box -->
        <div class="mt-6 p-6 rounded-3xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-11 gap-4 items-center">
                <!-- From Amount & Currency -->
                <div class="md:col-span-5 space-y-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">
                        {{ en ? 'Amount to Convert:' : 'রূপান্তরের পরিমাণ:' }}
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            v-model.number="amount"
                            type="number"
                            min="0"
                            class="flex-1 px-4 py-3 rounded-2xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-base font-mono font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />
                        <select
                            v-model="fromCurrency"
                            class="w-40 px-3 py-3 rounded-2xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option v-for="(info, code) in currencyDetails" :key="code" :value="code">
                                {{ info.flag }} {{ code }} - {{ en ? info.nameEn : info.nameBn }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Swap Button -->
                <div class="md:col-span-1 flex justify-center py-2 md:py-0">
                    <button
                        type="button"
                        @click="swapCurrencies"
                        class="p-3 rounded-full bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 hover:border-emerald-500 text-gray-700 dark:text-gray-300 shadow-sm transition hover:scale-105"
                        title="Swap"
                    >
                        <ArrowsRightLeftIcon class="w-5 h-5 text-emerald-500" />
                    </button>
                </div>

                <!-- To Amount & Currency -->
                <div class="md:col-span-5 space-y-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">
                        {{ en ? 'Converted Amount:' : 'রূপান্তরিত ফলাফল:' }}
                    </label>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 px-4 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-base font-mono font-bold text-emerald-600 dark:text-emerald-400 overflow-x-auto whitespace-nowrap">
                            {{ currencyDetails[toCurrency]?.symbol }} {{ convertedAmount.toLocaleString() }}
                        </div>
                        <select
                            v-model="toCurrency"
                            class="w-40 px-3 py-3 rounded-2xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option v-for="(info, code) in currencyDetails" :key="code" :value="code">
                                {{ info.flag }} {{ code }} - {{ en ? info.nameEn : info.nameBn }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Single Unit Exchange Rate Info -->
            <div class="pt-4 border-t border-gray-200 dark:border-white/5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 font-mono">
                    <span class="text-gray-500">{{ en ? 'Exchange Rate:' : 'বিনিময় হার:' }}</span>
                    <strong class="text-gray-900 dark:text-white">1 {{ fromCurrency }} = {{ singleUnitRate }} {{ toCurrency }}</strong>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-500">1 {{ toCurrency }} = {{ inverseUnitRate }} {{ fromCurrency }}</span>
                </div>

                <!-- Quick Amount Chips -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button
                        v-for="amt in [50, 100, 500, 1000]"
                        :key="amt"
                        type="button"
                        @click="amount = amt"
                        class="px-2.5 py-1 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-[11px] font-mono hover:border-emerald-500 text-gray-700 dark:text-gray-300"
                    >
                        {{ amt }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Common Currencies to BDT Live Table -->
        <div class="mt-6">
            <h3 class="text-xs font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-1.5">
                <BanknotesIcon class="w-4 h-4 text-emerald-500" />
                {{ en ? 'Popular Currencies against Bangladeshi Taka (1 Unit to BDT)' : 'জনপ্রিয় বৈদেশিক মুদ্রা বনাম বাংলাদেশী টাকা (১ ইউনিট = কত টাকা)' }}
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                <div
                    v-for="code in ['USD', 'SAR', 'AED', 'MYR', 'EUR', 'GBP']"
                    :key="code"
                    @click="fromCurrency = code; toCurrency = 'BDT'"
                    class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 text-center hover:border-emerald-500 transition cursor-pointer group"
                >
                    <span class="text-lg block">{{ currencyDetails[code]?.flag }}</span>
                    <strong class="font-bold text-xs text-gray-900 dark:text-white block mt-0.5">1 {{ code }}</strong>
                    <span class="font-mono text-xs font-extrabold text-emerald-600 dark:text-emerald-400 block mt-1">
                        ৳ {{ (exchangeRates['BDT'] / exchangeRates[code]).toFixed(2) }}
                    </span>
                    <span class="text-[10px] text-gray-400 block truncate">{{ en ? currencyDetails[code]?.nameEn : currencyDetails[code]?.nameBn }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
