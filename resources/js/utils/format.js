/**
 * Locale-aware number helpers.
 *
 * The UI is Bengali-first, so every user-facing number (prices, scores,
 * day counts, stats) should render with Bengali digits (০১২৩৪৫৬৭৮৯).
 * Passing `locale` keeps the English UI on Latin digits.
 *
 * Grouping is always Western (1,234) rather than the Bengali lakh/crore
 * grouping because the amounts on this platform are small and mixed
 * grouping confuses prices. Convert first, group second.
 */

const BN_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

export function toBnDigits(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    return String(value).replace(/[0-9]/g, (digit) => BN_DIGITS[Number(digit)]);
}

export function formatNumber(value, locale = 'bn') {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    const number = Number(value);

    if (!Number.isFinite(number)) {
        return String(value);
    }

    const grouped = number.toLocaleString('en-US');

    return locale === 'bn' ? toBnDigits(grouped) : grouped;
}

/**
 * Format a money amount. Integers stay integer (৳১৪৯), decimals keep two
 * places (৳১১০.০০) so wallet balances do not silently lose paisa.
 */
export function formatMoney(value, locale = 'bn', decimals = null) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    const number = Number(value);

    if (!Number.isFinite(number)) {
        return String(value);
    }

    const fixed = decimals ?? (Number.isInteger(number) ? 0 : 2);
    const grouped = number.toLocaleString('en-US', {
        minimumFractionDigits: fixed,
        maximumFractionDigits: fixed,
    });

    return locale === 'bn' ? toBnDigits(grouped) : grouped;
}
