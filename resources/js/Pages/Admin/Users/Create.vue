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
                <Panel class="shadow">
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-user-plus"></i>
                            <span>User Information</span>
                        </div>
                    </template>

                    <form @submit.prevent="submit" class="space-y-6">
                        <ErrorAlert v-if="form.hasErrors" :message="generalErrors" />

                        <div class="grid grid-cols-1 gap-6">
                            <div class="flex flex-col gap-2">
                                <label for="name" class="text-sm font-medium text-gray-700">Name</label>
                                <InputText
                                    id="name"
                                    v-model="form.name"
                                    :invalid="!!form.errors.name"
                                    class="w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                                <InputText
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    :invalid="!!form.errors.email"
                                    class="w-full"
                                    required
                                />
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                                <Password
                                    id="password"
                                    v-model="form.password"
                                    :invalid="!!form.errors.password"
                                    :feedback="false"
                                    toggleMask
                                    class="w-full"
                                    inputClass="w-full"
                                    required
                                />
                                <InputError :message="form.errors.password" />
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="password_confirmation" class="text-sm font-medium text-gray-700">
                                    Confirm Password
                                </label>
                                <Password
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    :feedback="false"
                                    toggleMask
                                    class="w-full"
                                    inputClass="w-full"
                                    required
                                />
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                                <Dropdown
                                    id="status"
                                    v-model="form.status"
                                    :options="statusOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    :invalid="!!form.errors.status"
                                    class="w-full"
                                    required
                                />
                                <InputError :message="form.errors.status" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <Link :href="route('admin.users.index')">
                                <Button
                                    label="Cancel"
                                    severity="secondary"
                                    outlined
                                />
                            </Link>
                            <Button
                                type="submit"
                                :label="form.processing ? 'Creating...' : 'Create User'"
                                :disabled="form.processing"
                                :loading="form.processing"
                            />
                        </div>
                    </form>
                </Panel>
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
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Dropdown from 'primevue/dropdown';
import Panel from 'primevue/panel';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    status: 'active',
});

const statusOptions = [
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' },
    { label: 'Pending', value: 'pending' },
    { label: 'Suspended', value: 'suspended' },
];

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

