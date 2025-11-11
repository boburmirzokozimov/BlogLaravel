<template>
    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 transform scale-95"
        enter-to-class="opacity-100 transform scale-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 transform scale-100"
        leave-to-class="opacity-0 transform scale-95"
    >
        <p v-if="message" class="mt-1 text-sm text-red-600 flex items-start gap-1">
            <svg
                class="h-4 w-4 mt-0.5 flex-shrink-0"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 8a1 1 0 100-2 1 1 0 000 2z"
                    clip-rule="evenodd"
                />
            </svg>
            <span>{{ displayMessage }}</span>
        </p>
    </transition>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    message: {
        type: [String, Array, Object],
        default: null,
    },
});

const displayMessage = computed(() => {
    if (!props.message) {
        return '';
    }

    // Handle string messages
    if (typeof props.message === 'string') {
        return props.message;
    }

    // Handle array of messages (take first one)
    if (Array.isArray(props.message)) {
        const firstMessage = props.message[0];
        if (typeof firstMessage === 'string') {
            return firstMessage;
        }
        // Handle array of bilingual objects
        if (typeof firstMessage === 'object' && firstMessage !== null) {
            return getLocalizedMessage(firstMessage);
        }
        return String(firstMessage);
    }

    // Handle bilingual object { en: '...', ru: '...' }
    if (typeof props.message === 'object' && props.message !== null) {
        return getLocalizedMessage(props.message);
    }

    return String(props.message);
});

function getLocalizedMessage(messageObj) {
    const locale = document.documentElement.lang || 'en';
    // Try current locale, then English, then first available
    return messageObj[locale] || messageObj.en || Object.values(messageObj)[0] || '';
}
</script>

