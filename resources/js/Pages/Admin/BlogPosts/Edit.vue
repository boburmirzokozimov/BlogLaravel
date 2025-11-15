<template>
    <Head title="Edit Blog Post Status" />
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Blog Post Status</h1>
                    <p class="mt-2 text-sm text-gray-700">Update the status of this blog post.</p>
                </div>
            </div>

            <div class="mt-8 max-w-2xl">
                <Panel class="shadow">
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-file-edit"></i>
                            <span>Blog Post: {{ post.title }}</span>
                        </div>
                    </template>

                    <form @submit.prevent="submit" class="space-y-6">
                        <ErrorAlert v-if="form.hasErrors" :message="generalErrors" />

                        <div class="grid grid-cols-1 gap-6">
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
                                <small class="text-gray-500">
                                    Current status: <Tag :value="post.status" :severity="getStatusSeverity(post.status)" />
                                </small>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Status Information</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li><strong>Draft:</strong> Post is not published and not visible to the public</li>
                                                <li><strong>Published:</strong> Post is live and visible to the public</li>
                                                <li><strong>Archived:</strong> Post is archived and no longer active</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <Link :href="route('admin.blog-posts.index')">
                                <Button
                                    label="Cancel"
                                    severity="secondary"
                                    outlined
                                />
                            </Link>
                            <Button
                                type="submit"
                                :label="form.processing ? 'Updating...' : 'Update Status'"
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
import Dropdown from 'primevue/dropdown';
import Tag from 'primevue/tag';
import Panel from 'primevue/panel';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    status: props.post.status,
});

const statusOptions = [
    { label: 'Draft', value: 'draft' },
    { label: 'Published', value: 'published' },
    { label: 'Archived', value: 'archived' },
];

const submit = () => {
    form.put(route('admin.blog-posts.update', props.post.id));
};

const hasGeneralErrors = computed(() => {
    if (!form.hasErrors) {
        return false;
    }
    return Object.keys(form.errors).some(
        (key) => !['status'].includes(key)
    );
});

const generalErrors = computed(() => {
    if (!hasGeneralErrors.value) {
        return null;
    }
    const errors = [];
    for (const [key, error] of Object.entries(form.errors)) {
        if (!['status'].includes(key)) {
            errors.push(...getAllLocalizedErrors(error));
        }
    }
    return errors.length > 0 ? errors : null;
});

const getStatusSeverity = (status) => {
    const severities = {
        draft: 'warning',
        published: 'success',
        archived: 'secondary',
    };
    return severities[status] || 'secondary';
};
</script>

