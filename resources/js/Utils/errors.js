/**
 * Get localized error message from bilingual error object
 * @param {string|object|array} error - Error message(s)
 * @param {string} locale - Preferred locale (defaults to document lang or 'en')
 * @returns {string} Localized error message
 */
export function getLocalizedError(error, locale = null) {
    if (!error) {
        return '';
    }

    // Handle string messages
    if (typeof error === 'string') {
        return error;
    }

    // Handle array of messages (take first one)
    if (Array.isArray(error)) {
        if (error.length === 0) {
            return '';
        }
        return getLocalizedError(error[0], locale);
    }

    // Handle bilingual object { en: '...', ru: '...' }
    if (typeof error === 'object' && error !== null) {
        const preferredLocale = locale || document.documentElement.lang || 'en';
        // Try preferred locale, then English, then first available
        return error[preferredLocale] || error.en || Object.values(error)[0] || '';
    }

    return String(error);
}

/**
 * Get all localized error messages from an error object/array
 * @param {string|object|array} error - Error message(s)
 * @param {string} locale - Preferred locale
 * @returns {string[]} Array of localized error messages
 */
export function getAllLocalizedErrors(error, locale = null) {
    if (!error) {
        return [];
    }

    // Handle string messages
    if (typeof error === 'string') {
        return [error];
    }

    // Handle array of messages
    if (Array.isArray(error)) {
        return error.map((err) => getLocalizedError(err, locale)).filter(Boolean);
    }

    // Handle bilingual object
    if (typeof error === 'object' && error !== null) {
        const message = getLocalizedError(error, locale);
        return message ? [message] : [];
    }

    return [];
}

/**
 * Format validation errors from Inertia form
 * @param {object} errors - Inertia form errors object
 * @param {string} locale - Preferred locale
 * @returns {object} Formatted errors with localized messages
 */
export function formatValidationErrors(errors, locale = null) {
    if (!errors || typeof errors !== 'object') {
        return {};
    }

    const formatted = {};
    for (const [field, error] of Object.entries(errors)) {
        formatted[field] = getLocalizedError(error, locale);
    }

    return formatted;
}

