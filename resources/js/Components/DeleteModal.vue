<template>
    <Dialog
        :visible="show"
        modal
        header="Delete User"
        :style="{ width: '25rem' }"
        @update:visible="(val) => !val && $emit('close')"
    >
        <div class="flex items-center gap-3 mb-4">
            <i class="pi pi-exclamation-triangle text-red-500 text-2xl" />
            <p class="m-0">
                Are you sure you want to delete
                <span class="font-medium">{{ user?.name }}</span>? This action cannot be undone.
            </p>
        </div>
        <template #footer>
            <Button
                label="Cancel"
                severity="secondary"
                outlined
                @click="$emit('close')"
            />
            <Button
                label="Delete"
                severity="danger"
                @click="$emit('confirm')"
            />
        </template>
    </Dialog>
</template>

<script setup>
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';

defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    user: {
        type: Object,
        default: null,
    },
});

defineEmits(['close', 'confirm']);
</script>

