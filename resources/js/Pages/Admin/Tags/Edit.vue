<template>
    <Head title="Edit Tag" />
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Tag</h1>
                    <p class="mt-2 text-sm text-gray-700">Update tag information.</p>
                </div>
            </div>

            <div class="mt-8 max-w-2xl">
                <Panel class="shadow">
                    <template #header>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-tag"></i>
                            <span>Tag Information</span>
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
                                <label for="slug" class="text-sm font-medium text-gray-700">Slug</label>
                                <InputText
                                    id="slug"
                                    v-model="form.slug"
                                    :invalid="!!form.errors.slug"
                                    class="w-full"
                                />
                                <InputError :message="form.errors.slug" />
                                <small class="text-gray-500">Leave empty to auto-generate from name.</small>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <Link :href="route('admin.tags.index')">
                                <Button
                                    label="Cancel"
                                    severity="secondary"
                                    outlined
                                />
                            </Link>
                            <Button
                                type="submit"
                                :label="form.processing ? 'Updating...' : 'Update Tag'"
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
import Panel from 'primevue/panel';

const props = defineProps({
    tag: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.tag.name,
    slug: props.tag.slug || '',
});

const submit = () => {
    form.put(route('admin.tags.update', props.tag.id));
};

const hasGeneralErrors = computed(() => {
    if (!form.hasErrors) {
        return false;
    }
    return Object.keys(form.errors).some(
        (key) => !['name', 'slug'].includes(key)
    );
});

const generalErrors = computed(() => {
    if (!hasGeneralErrors.value) {
        return null;
    }
    const errors = [];
    for (const [key, error] of Object.entries(form.errors)) {
        if (!['name', 'slug'].includes(key)) {
            errors.push(...getAllLocalizedErrors(error));
        }
    }
    return errors.length > 0 ? errors : null;
});
</script>

