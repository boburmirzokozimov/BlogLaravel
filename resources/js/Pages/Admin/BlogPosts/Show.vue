<template>
    <Head :title="`Blog Post: ${props.data.title}`" />
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <div class="flex items-center gap-4">
                        <Link
                            :href="route('admin.blog-posts.index')"
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
                            <h1 class="text-2xl font-semibold text-gray-900">Blog Post Details</h1>
                            <p class="mt-2 text-sm text-gray-700">View blog post information and details.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex gap-3">
                    <Link
                        :href="route('admin.blog-posts.edit', props.data.id)"
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
                        Edit Status
                    </Link>
                </div>
            </div>

            <!-- Blog Post Information Card -->
            <div class="mt-8 overflow-hidden bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <!-- Title -->
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Title</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ props.data.title }}</dd>
                        </div>

                        <!-- Slug -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1">
                                <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ props.data.slug }}</code>
                            </dd>
                        </div>

                        <!-- Status -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <Tag
                                    :value="props.data.status"
                                    :severity="getStatusSeverity(props.data.status)"
                                />
                            </dd>
                        </div>

                        <!-- Content -->
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Content</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ props.data.content }}</dd>
                        </div>

                        <!-- Author ID -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Author ID</dt>
                            <dd class="mt-1 font-mono text-sm text-gray-900">{{ props.data.author_id }}</dd>
                        </div>

                        <!-- Published At -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Published At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ props.data.published_at ? formatDateTime(props.data.published_at) : 'N/A' }}
                            </dd>
                        </div>

                        <!-- Tags -->
                        <div v-if="props.data.tags && props.data.tags.length > 0" class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Tags</dt>
                            <dd class="mt-1 flex flex-wrap gap-2">
                                <Tag
                                    v-for="tag in props.data.tags"
                                    :key="tag"
                                    :value="tag"
                                    severity="info"
                                />
                            </dd>
                        </div>

                        <!-- Post ID -->
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Post ID</dt>
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
                            :href="route('admin.blog-posts.edit', props.data.id)"
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
                            Edit Status
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Tag from 'primevue/tag';

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const getStatusSeverity = (status) => {
    const severities = {
        draft: 'warning',
        published: 'success',
        archived: 'secondary',
    };
    return severities[status] || 'secondary';
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

