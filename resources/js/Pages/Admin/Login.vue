<template>
    <Head title="Admin Login" />
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Admin Login
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sign in to access the admin panel
                </p>
            </div>
            <form @submit.prevent="submit" class="mt-8 space-y-6">
                <ErrorAlert
                    v-if="hasGeneralErrors"
                    :message="generalErrors"
                    title="Please correct the following errors:"
                />

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email address
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            :class="[
                                'appearance-none relative block w-full px-3 py-2 border rounded-md placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 sm:text-sm transition-colors',
                                form.errors.email
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                            ]"
                            placeholder="you@example.com"
                            aria-invalid="true"
                            :aria-describedby="form.errors.email ? 'email-error' : undefined"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            :class="[
                                'appearance-none relative block w-full px-3 py-2 border rounded-md placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 sm:text-sm transition-colors',
                                form.errors.password
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                    : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                            ]"
                            placeholder="••••••••"
                            aria-invalid="true"
                            :aria-describedby="form.errors.password ? 'password-error' : undefined"
                        />
                        <InputError :message="form.errors.password" />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm"
                    >
                        <svg
                            v-if="form.processing"
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        <span v-if="form.processing">Logging in...</span>
                        <span v-else>Sign in</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import ErrorAlert from '@/Components/ErrorAlert.vue';
import InputError from '@/Components/InputError.vue';
import { getAllLocalizedErrors } from '@/Utils/errors';

defineOptions({
    layout: null,
});

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('admin.login.post'), {
        onSuccess: () => {
            form.reset('password');
        },
    });
};

// Filter out field-specific errors to show only general errors
const hasGeneralErrors = computed(() => {
    if (!form.hasErrors) {
        return false;
    }
    // Check if there are errors that are not email or password
    return Object.keys(form.errors).some(
        (key) => key !== 'email' && key !== 'password'
    );
});

const generalErrors = computed(() => {
    if (!hasGeneralErrors.value) {
        return null;
    }
    const errors = [];
    for (const [key, error] of Object.entries(form.errors)) {
        if (key !== 'email' && key !== 'password') {
            errors.push(...getAllLocalizedErrors(error));
        }
    }
    return errors.length > 0 ? errors : null;
});
</script>

