<template>
    <Head title="Create User" />
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Create New User</h1>
                    <p class="mt-2 text-sm text-gray-700">Add a new user to the system.</p>
                </div>
            </div>

            <div class="mt-8 max-w-2xl">
                <form @submit.prevent="submit" class="space-y-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                    <ErrorAlert v-if="form.hasErrors" :message="generalErrors" />

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-1">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                :class="[
                                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                                    form.errors.name ? 'border-red-300' : ''
                                ]"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                :class="[
                                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                                    form.errors.email ? 'border-red-300' : ''
                                ]"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                :class="[
                                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                                    form.errors.password ? 'border-red-300' : ''
                                ]"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm Password
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select
                                id="status"
                                v-model="form.status"
                                required
                                :class="[
                                    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                                    form.errors.status ? 'border-red-300' : ''
                                ]"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                                <option value="suspended">Suspended</option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <Link
                            :href="route('admin.users.index')"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create User</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ErrorAlert from '@/Components/ErrorAlert.vue';
import InputError from '@/Components/InputError.vue';
import { getAllLocalizedErrors } from '@/Utils/errors';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    status: 'active',
});

const submit = () => {
    form.post(route('admin.users.store'));
};

const hasGeneralErrors = computed(() => {
    if (!form.hasErrors) {
        return false;
    }
    return Object.keys(form.errors).some(
        (key) => !['name', 'email', 'password', 'password_confirmation', 'status'].includes(key)
    );
});

const generalErrors = computed(() => {
    if (!hasGeneralErrors.value) {
        return null;
    }
    const errors = [];
    for (const [key, error] of Object.entries(form.errors)) {
        if (!['name', 'email', 'password', 'password_confirmation', 'status'].includes(key)) {
            errors.push(...getAllLocalizedErrors(error));
        }
    }
    return errors.length > 0 ? errors : null;
});
</script>

