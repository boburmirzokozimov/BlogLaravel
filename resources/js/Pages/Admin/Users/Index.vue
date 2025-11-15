<template>
    <Head title="Users Management"/>
    <AdminLayout>
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Users</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        A list of all users in the system including their name, email, and status.
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <Link :href="route('admin.users.create')">
                        <Button label="Add user" icon="pi pi-plus"/>
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
                    <div class="sm:col-span-7">
                        <span class="p-input-icon-left w-full">
                            <InputText
                                id="search"
                                v-model="searchQuery"
                                placeholder="Search by name or email..."
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

            <!-- Users Table -->
            <DataTable
                :value="users"
                :paginator="false"
                class="mt-8"
                stripedRows
                showGridlines
                :emptyMessage="'No users found.'"
            >
                <Column field="name" header="Name" sortable>
                    <template #body="{ data }">
                        <span class="font-medium">{{ data.name }}</span>
                    </template>
                </Column>
                <Column field="email" header="Email" sortable/>
                <Column field="status" header="Status" sortable>
                    <template #body="{ data }">
                        <Tag
                            :value="data.status"
                            :severity="getStatusSeverity(data.status)"
                        />
                    </template>
                </Column>
                <Column field="email_verified_at" header="Email Verified">
                    <template #body="{ data }">
                        <i
                            v-if="data.email_verified_at"
                            class="pi pi-check-circle text-green-600"
                            title="Verified"
                        />
                        <i
                            v-else
                            class="pi pi-times-circle text-gray-400"
                            title="Not verified"
                        />
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
                            <Link :href="route('admin.users.edit', data.id)">
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
            <div v-if="hasPagination" class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Page
                    <span class="font-medium">{{ pagination.current_page }}</span>
                    <span v-if="links.next">(More pages available)</span>
                </div>
                <div class="flex gap-2">
                    <Link v-if="links.prev" :href="links.prev">
                        <Button
                            icon="pi pi-angle-left"
                            severity="secondary"
                            outlined
                            :disabled="!links.prev"
                        />
                    </Link>
                    <Link v-if="links.first" :href="links.first">
                        <Button
                            label="First"
                            severity="secondary"
                            outlined
                            :disabled="pagination.current_page === 1"
                        />
                    </Link>
                    <span class="px-3 py-2 border rounded bg-primary text-white">
                        {{ pagination.current_page }}
                    </span>
                    <Link v-if="links.next" :href="links.next">
                        <Button
                            icon="pi pi-angle-right"
                            severity="secondary"
                            outlined
                            :disabled="!links.next"
                        />
                    </Link>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <DeleteModal
                v-if="userToDelete"
                :show="!!userToDelete"
                :user="userToDelete"
                @close="userToDelete = null"
                @confirm="deleteUser"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import {ref, computed} from 'vue';
import {Head, Link, router} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SuccessAlert from '@/Components/SuccessAlert.vue';
import DeleteModal from '@/Components/DeleteModal.vue';
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

// Extract users array and pagination from Inertia's paginated response
// When UserResource::collection() is used with a simple paginator, Laravel automatically includes:
// - data.data: array of resources
// - data.links: simple pagination links (prev, next, first)
// - data.meta: simple pagination metadata (current_page, per_page)
const users = computed(() => props.data.data || []);
const pagination = computed(() => props.data.meta || {});
const links = computed(() => {
    // Simple pagination structure: {prev: string|null, next: string|null, first: string|null}
    const linkData = props.data.links || {};
    
    return {
        prev: linkData.prev || null,
        next: linkData.next || null,
        first: linkData.first || null,
    };
});

const hasPagination = computed(() => {
    return links.value.prev !== null || links.value.next !== null;
});

// Get filters from URL query params
const searchQuery = ref(new URLSearchParams(window.location.search).get('search') || '');
const statusFilter = ref(new URLSearchParams(window.location.search).get('status') || '');
const userToDelete = ref(null);

const statusOptions = [
    {label: 'All Statuses', value: ''},
    {label: 'Active', value: 'active'},
    {label: 'Inactive', value: 'inactive'},
    {label: 'Pending', value: 'pending'},
    {label: 'Suspended', value: 'suspended'},
];

const confirmDelete = (user) => {
    userToDelete.value = user;
};

const deleteUser = () => {
    if (userToDelete.value) {
        router.delete(route('admin.users.destroy', userToDelete.value.id), {
            onSuccess: () => {
                userToDelete.value = null;
            },
        });
    }
};

const applyFilters = () => {
    const params = new URLSearchParams();
    if (searchQuery.value) {
        params.set('search', searchQuery.value);
    }
    if (statusFilter.value) {
        params.set('status', statusFilter.value);
    }
    router.get(route('admin.users.index'), Object.fromEntries(params), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    router.get(route('admin.users.index'));
};


const getStatusSeverity = (status) => {
    const severities = {
        active: 'success',
        inactive: 'secondary',
        pending: 'warning',
        suspended: 'danger',
    };
    return severities[status] || 'secondary';
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

const formatStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

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

