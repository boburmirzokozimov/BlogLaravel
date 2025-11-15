<template>
    <Head :title="`Tag: ${props.data.name}`" />
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <div class="flex items-center gap-4">
                        <Link
                            :href="route('admin.tags.index')"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                />
                            </svg>
                        </Link>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Tag Details</h1>
                            <p class="mt-2 text-sm text-gray-700">View tag information and details.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex gap-3">
                    <Link
                        :href="route('admin.tags.edit', props.data.id)"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        <svg
                            class="-ml-0.5 mr-1.5 h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                        Edit Tag
                    </Link>
                </div>
            </div>

            <!-- Tag Information Card -->
            <div class="mt-8 overflow-hidden bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <!-- Name -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ props.data.name }}</dd>
                        </div>

                        <!-- Slug -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1">
                                <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ props.data.slug }}</code>
                            </dd>
                        </div>

                        <!-- Tag ID -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tag ID</dt>
                            <dd class="mt-1 font-mono text-sm text-gray-900">{{ props.data.id }}</dd>
                        </div>

                        <!-- Created At -->
                        <div v-if="props.data.created_at">
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ formatDateTime(props.data.created_at) }}
                            </dd>
                        </div>

                        <!-- Updated At -->
                        <div v-if="props.data.updated_at">
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ formatDateTime(props.data.updated_at) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="mt-6 overflow-hidden bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Actions</h3>
                    <div class="mt-5 flex gap-3">
                        <Link
                            :href="route('admin.tags.edit', props.data.id)"
                            class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                        >
                            <svg
                                class="-ml-0.5 mr-1.5 h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                />
                            </svg>
                            Edit Tag
                        </Link>
                        <button
                            @click="confirmDelete"
                            class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-300 hover:bg-red-50"
                        >
                            <svg
                                class="-ml-0.5 mr-1.5 h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>
                            Delete Tag
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <DeleteTagModal
                v-if="showDeleteModal"
                :show="showDeleteModal"
                :tag="props.data"
                @close="showDeleteModal = false"
                @confirm="deleteTag"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteTagModal from '@/Components/DeleteTagModal.vue';

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const showDeleteModal = ref(false);

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteTag = () => {
    router.delete(route('admin.tags.destroy', props.data.id), {
        onSuccess: () => {
            router.visit(route('admin.tags.index'));
        },
    });
};

const formatDateTime = (date) => {
    if (!date) {
        return 'N/A';
    }
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

