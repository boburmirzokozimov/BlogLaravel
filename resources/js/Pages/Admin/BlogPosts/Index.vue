<template>
    <Head title="Blog Posts Management"/>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Blog Posts</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        A list of all blog posts in the system including their title, status, and publication date.
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <Panel header="Filters" class="mt-6" :toggleable="true">
                <template #header>
                    <div class="flex items-center gap-2">
                        <i class="pi pi-filter"></i>
                        <span>Filters</span>
                    </div>
                </template>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                    <!-- Search Input -->
                    <div class="sm:col-span-7">
                        <span class="p-input-icon-left w-full">
                            <InputText
                                id="search"
                                v-model="searchQuery"
                                placeholder="Search by title or slug..."
                                class="w-full"
                                @keyup.enter="applyFilters"
                            />
                        </span>
                    </div>

                    <!-- Status Filter -->
                    <div class="sm:col-span-4">
                        <Dropdown
                            id="status"
                            v-model="statusFilter"
                            :options="statusOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="All Statuses"
                            class="w-full"
                            @change="applyFilters"
                        />
                    </div>

                    <!-- Apply Button -->
                    <div class="sm:col-span-1 flex items-end">
                        <Button
                            label="Apply"
                            icon="pi pi-search"
                            class="w-full sm:w-auto"
                            @click="applyFilters"
                        />
                    </div>
                </div>

                <!-- Active Filters Badges -->
                <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                    <Tag
                        v-if="searchQuery"
                        :value="searchTagValue"
                        severity="info"
                        @remove="searchQuery = ''; applyFilters()"
                    />
                    <Tag
                        v-if="statusFilter"
                        :value="statusTagValue"
                        severity="info"
                        @remove="statusFilter = ''; applyFilters()"
                    />
                </div>

                <div v-if="hasActiveFilters" class="mt-4">
                    <Button
                        label="Clear all"
                        icon="pi pi-times"
                        severity="secondary"
                        text
                        @click="clearFilters"
                    />
                </div>
            </Panel>

            <!-- Success Message -->
            <SuccessAlert
                v-if="$page.props.flash?.success"
                :message="$page.props.flash.success"
                class="mt-4"
            />

            <!-- Blog Posts Table -->
            <DataTable
                :value="posts"
                :paginator="false"
                class="mt-8"
                stripedRows
                showGridlines
                :emptyMessage="'No blog posts found.'"
            >
                <Column field="title" header="Title" sortable>
                    <template #body="{ data }">
                        <span class="font-medium">{{ data.title }}</span>
                    </template>
                </Column>
                <Column field="slug" header="Slug" sortable>
                    <template #body="{ data }">
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ data.slug }}</code>
                    </template>
                </Column>
                <Column field="status" header="Status" sortable>
                    <template #body="{ data }">
                        <Tag
                            :value="data.status"
                            :severity="getStatusSeverity(data.status)"
                        />
                    </template>
                </Column>
                <Column field="published_at" header="Published" sortable>
                    <template #body="{ data }">
                        {{ formatDate(data.published_at) }}
                    </template>
                </Column>
                <Column field="created_at" header="Created" sortable>
                    <template #body="{ data }">
                        {{ formatDate(data.created_at) }}
                    </template>
                </Column>
                <Column header="Actions" :exportable="false">
                    <template #body="{ data }">
                        <div class="flex gap-2">
                            <Link :href="route('admin.blog-posts.show', data.id)">
                                <Button
                                    icon="pi pi-eye"
                                    severity="secondary"
                                    text
                                    rounded
                                    v-tooltip.top="'View'"
                                />
                            </Link>
                            <Link :href="route('admin.blog-posts.edit', data.id)">
                                <Button
                                    icon="pi pi-pencil"
                                    severity="secondary"
                                    text
                                    rounded
                                    v-tooltip.top="'Edit Status'"
                                />
                            </Link>
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- Pagination -->
            <SimplePagination :data="data" />
        </div>
    </AdminLayout>
</template>

<script setup>
import {ref, computed} from 'vue';
import {Head, Link, router} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SuccessAlert from '@/Components/SuccessAlert.vue';
import SimplePagination from '@/Components/SimplePagination.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Panel from 'primevue/panel';

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

// Extract posts array from Inertia's paginated response
const posts = computed(() => props.data.data || []);

// Get filters from URL query params
const searchQuery = ref(new URLSearchParams(window.location.search).get('search') || '');
const statusFilter = ref(new URLSearchParams(window.location.search).get('status') || '');

const statusOptions = [
    {label: 'All Statuses', value: ''},
    {label: 'Draft', value: 'draft'},
    {label: 'Published', value: 'published'},
    {label: 'Archived', value: 'archived'},
];

const applyFilters = () => {
    const params = new URLSearchParams();
    if (searchQuery.value) {
        params.set('search', searchQuery.value);
    }
    if (statusFilter.value) {
        params.set('status', statusFilter.value);
    }
    router.get(route('admin.blog-posts.index'), Object.fromEntries(params), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    router.get(route('admin.blog-posts.index'));
};

const hasActiveFilters = computed(() => {
    return !!searchQuery.value || !!statusFilter.value;
});

const searchTagValue = computed(() => {
    return `Search: "${searchQuery.value}"`;
});

const statusTagValue = computed(() => {
    return `Status: ${formatStatus(statusFilter.value)}`;
});

const getStatusSeverity = (status) => {
    const severities = {
        draft: 'warning',
        published: 'success',
        archived: 'secondary',
    };
    return severities[status] || 'secondary';
};

const formatStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const formatDate = (date) => {
    if (!date) {
        return 'N/A';
    }
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

