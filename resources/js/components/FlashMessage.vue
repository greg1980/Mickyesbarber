<script setup>
import { computed } from 'vue';

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'success'
    }
});

const bgColor = computed(() => {
    switch (props.type) {
        case 'success':
            return 'bg-green-100 border-green-500 text-green-700';
        case 'error':
            return 'bg-red-100 border-red-500 text-red-700';
        default:
            return 'bg-blue-100 border-blue-500 text-blue-700';
    }
});
</script>

<template>
    <div v-if="message" :class="['border-l-4 p-4 mb-4', bgColor]" role="alert">
        <p class="font-medium">{{ message }}</p>
    </div>
</template>
