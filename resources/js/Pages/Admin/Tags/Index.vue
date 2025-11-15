<template>
    <Head title="Tags Management"/>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Tags</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        A list of all tags in the system including their name and slug.
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <Link :href="route('admin.tags.create')">
                        <Button label="Add tag" icon="pi pi-plus"/>
                    </Link>
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
                    <div class="sm:col-span-11">
                        <span class="p-input-icon-left w-full">
                            <InputText
                                id="search"
                                v-model="searchQuery"
                                placeholder="Search by name or slug..."
                                class="w-full"
                                @keyup.enter="applyFilters"
                            />
                        </span>
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

            <!-- Tags Table -->
            <DataTable
                :value="tags"
                :paginator="false"
                class="mt-8"
                stripedRows
                showGridlines
                :emptyMessage="'No tags found.'"
            >
                <Column field="name" header="Name" sortable>
                    <template #body="{ data }">
                        <span class="font-medium">{{ data.name }}</span>
                    </template>
                </Column>
                <Column field="slug" header="Slug" sortable>
                    <template #body="{ data }">
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ data.slug }}</code>
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
                            <Link :href="route('admin.tags.edit', data.id)">
                                <Button
                                    icon="pi pi-pencil"
                                    severity="secondary"
                                    text
                                    rounded
                                    v-tooltip.top="'Edit'"
                                />
                            </Link>
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                text
                                rounded
                                v-tooltip.top="'Delete'"
                                @click="confirmDelete(data)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <!-- Pagination -->
            <SimplePagination :data="data" />

            <!-- Delete Confirmation Modal -->
            <DeleteTagModal
                v-if="tagToDelete"
                :show="!!tagToDelete"
                :tag="tagToDelete"
                @close="tagToDelete = null"
                @confirm="deleteTag"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import {ref, computed} from 'vue';
import {Head, Link, router} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SuccessAlert from '@/Components/SuccessAlert.vue';
import DeleteTagModal from '@/Components/DeleteTagModal.vue';
import SimplePagination from '@/Components/SimplePagination.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
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

// Extract tags array from Inertia's paginated response
const tags = computed(() => props.data.data || []);

// Get filters from URL query params
const searchQuery = ref(new URLSearchParams(window.location.search).get('search') || '');
const tagToDelete = ref(null);

const confirmDelete = (tag) => {
    tagToDelete.value = tag;
};

const deleteTag = () => {
    if (tagToDelete.value) {
        router.delete(route('admin.tags.destroy', tagToDelete.value.id), {
            onSuccess: () => {
                tagToDelete.value = null;
            },
        });
    }
};

const applyFilters = () => {
    const params = new URLSearchParams();
    if (searchQuery.value) {
        params.set('search', searchQuery.value);
    }
    router.get(route('admin.tags.index'), Object.fromEntries(params), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    router.get(route('admin.tags.index'));
};

const hasActiveFilters = computed(() => {
    return !!searchQuery.value;
});

const searchTagValue = computed(() => {
    return `Search: "${searchQuery.value}"`;
});

const formatDate = (date) => {
    if (!date) {
        return '';
    }
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

