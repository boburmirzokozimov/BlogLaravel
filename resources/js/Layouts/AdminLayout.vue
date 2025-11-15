<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-900">Admin Panel</h1>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <Link
                                :href="route('admin.dashboard')"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium',
                                    $page.url.startsWith('/admin/dashboard')
                                        ? 'border-indigo-500 text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                ]"
                            >
                                Dashboard
                            </Link>
                            <Link
                                :href="route('admin.users.index')"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium',
                                    $page.url.startsWith('/admin/users')
                                        ? 'border-indigo-500 text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                ]"
                            >
                                Users
                            </Link>
                            <Link
                                :href="route('admin.tags.index')"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium',
                                    $page.url.startsWith('/admin/tags')
                                        ? 'border-indigo-500 text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                                ]"
                            >
                                Tags
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-sm text-gray-700 mr-4">{{ $page.props.auth.user?.name }}</span>
                            <Link
                                :href="route('admin.logout')"
                                method="post"
                                as="button"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <ErrorAlert
                v-if="permissionError"
                :message="permissionError"
                title="Permission Denied"
            />
            <slot />
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ErrorAlert from '@/Components/ErrorAlert.vue';
import { getLocalizedError } from '@/Utils/errors';

const page = usePage();

const permissionError = computed(() => {
    const error = page.props.errors?.permission || page.props.flash?.error;
    if (!error) {
        return null;
    }
    // Get localized message from bilingual object
    return getLocalizedError(error);
});
</script>

