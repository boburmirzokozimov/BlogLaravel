<template>
    <div
        v-if="message"
        class="rounded-md bg-red-50 border border-red-200 p-4 mb-4"
        role="alert"
    >
        <div class="flex">
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-red-400"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-red-800">
                    {{ title }}
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p v-if="typeof message === 'string'">{{ message }}</p>
                    <ul v-else class="list-disc list-inside space-y-1">
                        <li v-for="(msg, index) in messageArray" :key="index">
                            {{ msg }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button
                        v-if="dismissible"
                        @click="$emit('dismiss')"
                        type="button"
                        class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50"
                    >
                        <span class="sr-only">Dismiss</span>
                        <svg
                            class="h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    message: {
        type: [String, Array, Object],
        default: null,
    },
    title: {
        type: String,
        default: 'Error',
    },
    dismissible: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['dismiss']);

const messageArray = computed(() => {
    if (Array.isArray(props.message)) {
        return props.message;
    }
    if (typeof props.message === 'object' && props.message !== null) {
        // Handle bilingual messages { en: '...', ru: '...' }
        const locale = document.documentElement.lang || 'en';
        return Object.values(props.message).map((msg) => {
            if (typeof msg === 'object' && msg !== null) {
                return msg[locale] || msg.en || Object.values(msg)[0];
            }
            return msg;
        });
    }
    return [];
});
</script>

