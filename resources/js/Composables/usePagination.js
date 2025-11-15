import { computed } from 'vue';

/**
 * Composable for handling simple pagination from Inertia's paginated response
 * @param {Object} data - The paginated data object from Inertia
 * @returns {Object} Object containing pagination, links, and hasPagination computed properties
 */
export function usePagination(data) {
    // Extract pagination from Inertia's paginated response
    // When Resource::collection() is used with a simple paginator, Laravel automatically includes:
    // - data.links: simple pagination links (prev, next, first)
    // - data.meta: simple pagination metadata (current_page, per_page)
    const pagination = computed(() => data.value?.meta || {});
    
    const links = computed(() => {
        // Simple pagination structure: {prev: string|null, next: string|null, first: string|null}
        const linkData = data.value?.links || {};
        
        return {
            prev: linkData.prev || null,
            next: linkData.next || null,
            first: linkData.first || null,
        };
    });
    
    const hasPagination = computed(() => {
        return links.value.prev !== null || links.value.next !== null;
    });
    
    return {
        pagination,
        links,
        hasPagination,
    };
}

