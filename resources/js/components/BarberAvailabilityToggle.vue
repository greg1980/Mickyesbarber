<template>
    <div class="flex items-center space-x-4">
        <span class="text-sm font-medium text-gray-700">Availability Status:</span>
        <button
            @click="toggleAvailability"
            :class="[
                'px-4 py-2 rounded-full text-sm font-medium',
                isAvailable
                    ? 'bg-green-100 text-green-800 hover:bg-green-200'
                    : 'bg-red-100 text-red-800 hover:bg-red-200'
            ]"
            :disabled="loading"
        >
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 rounded-full"
                    :class="isAvailable ? 'bg-green-500' : 'bg-red-500'"
                ></div>
                <span>{{ isAvailable ? 'Available' : 'Unavailable' }}</span>
            </div>
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    barberId: {
        type: Number,
        required: true
    }
});

const isAvailable = ref(false);
const loading = ref(false);

onMounted(async () => {
    await fetchAvailability();
});

const fetchAvailability = async () => {
    try {
        const response = await fetch(`/barbers/${props.barberId}/availability`);
        const data = await response.json();
        isAvailable.value = data.is_available;
    } catch (error) {
        console.error('Error fetching availability:', error);
    }
};

const toggleAvailability = async () => {
    if (loading.value) return;

    loading.value = true;
    try {
        const response = await fetch(`/barbers/${props.barberId}/toggle-availability`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });

        const data = await response.json();
        isAvailable.value = data.is_available;

        // Show success message
        router.visit(window.location.pathname, {
            preserveScroll: true,
            preserveState: true,
            only: ['flash'],
            data: {
                message: data.message,
                type: 'success'
            }
        });
    } catch (error) {
        console.error('Error toggling availability:', error);
    } finally {
        loading.value = false;
    }
};
</script>
