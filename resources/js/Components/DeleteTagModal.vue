<template>
    <Dialog
        :visible="show"
        modal
        header="Delete Tag"
        :style="{ width: '25rem' }"
        @update:visible="(val) => !val && $emit('close')"
    >
        <div class="flex items-center gap-3 mb-4">
            <i class="pi pi-exclamation-triangle text-red-500 text-2xl" />
            <p class="m-0">
                Are you sure you want to delete
                <span class="font-medium">{{ tag?.name }}</span>? This action cannot be undone.
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
    tag: {
        type: Object,
        default: null,
    },
});

defineEmits(['close', 'confirm']);
</script>

