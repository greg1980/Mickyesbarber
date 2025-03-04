<template>
    <div class="mt-4">
        <h3 class="text-lg font-medium text-gray-900 mb-3">Choose your barber</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
            <div
                v-for="barber in barbers"
                :key="barber.id"
                class="relative cursor-pointer text-center"
                @click="selectBarber(barber)"
            >
                <!-- Circular Barber Image -->
                <div
                    class="relative mx-auto w-24 h-24 mb-2"
                    :class="{ 'opacity-40': !isBarberAvailable(barber.id) }"
                >
                    <img
                        :src="barber.profile_photo"
                        :alt="barber.name"
                        class="w-24 h-24 rounded-full object-cover border-2 transition-all duration-200"
                        :class="[
                            selectedBarberId === barber.id ? 'border-green-500 scale-105' : 'border-gray-200',
                            !isBarberAvailable(barber.id) ? 'opacity-40' : 'hover:border-green-300'
                        ]"
                        @error="handleImageError"
                    />

                    <!-- Selected Indicator -->
                    <div
                        v-if="selectedBarberId === barber.id"
                        class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center border-2 border-white shadow-lg animate-scale-in"
                    >
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <!-- Unavailable Indicator -->
                    <div
                        v-if="!isBarberAvailable(barber.id)"
                        class="absolute inset-0 flex items-center justify-center rounded-full bg-black bg-opacity-30"
                    >
                        <span class="text-xs font-medium text-white bg-black bg-opacity-50 px-2 py-1 rounded-full">
                            Unavailable
                        </span>
                    </div>
                </div>

                <!-- Barber Name -->
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-900">{{ barber.name }}</p>
                    <p class="text-xs text-gray-500">{{ barber.years_of_experience }} years exp.</p>
                </div>

                <!-- Specialties Tooltip -->
                <div
                    class="hidden group-hover:block absolute z-10 w-48 px-2 py-1 text-xs text-center text-white bg-gray-900 rounded-lg -translate-x-1/2 left-1/2 -bottom-12"
                >
                    {{ barber.specialties.join(', ') }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    barbers: {
        type: Array,
        required: true
    },
    selectedDate: {
        type: [Date, String],
        required: true
    },
    availableBarbers: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['select']);
const selectedBarberId = ref(null);

const isBarberAvailable = (barberId) => {
    return props.availableBarbers.includes(barberId);
};

const selectBarber = (barber) => {
    if (!isBarberAvailable(barber.id)) return;
    selectedBarberId.value = barber.id;
    emit('select', barber);
};

const handleImageError = (e) => {
    // Fallback to a default image if the profile photo fails to load
    e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(e.target.alt)}&size=96&rounded=true`;
};

// Reset selection when date changes
watch(() => props.selectedDate, () => {
    selectedBarberId.value = null;
});
</script>

<style>
@keyframes scale-in {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-scale-in {
    animation: scale-in 0.2s ease-out forwards;
}
</style>
