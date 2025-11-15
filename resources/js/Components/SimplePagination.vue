<template>
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
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

// Extract pagination from Inertia's paginated response
// When Resource::collection() is used with a simple paginator, Laravel automatically includes:
// - data.links: simple pagination links (prev, next, first)
// - data.meta: simple pagination metadata (current_page, per_page)
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
</script>

