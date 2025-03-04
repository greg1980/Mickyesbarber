<template>
    <Head title="Book Appointment" />

    <UserLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Book Appointment
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <Alert v-if="$page.props.flash.success" class="mb-6" variant="success">
                            {{ $page.props.flash.success }}
                        </Alert>
                        <Alert v-if="$page.props.flash.error" class="mb-6" variant="danger">
                            {{ $page.props.flash.error }}
                        </Alert>
                        <Alert v-if="error" class="mb-6" variant="danger">
                            {{ error }}
                        </Alert>

                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <!-- Date and Time Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Calendar Selection -->
                                <div>
                                    <InputLabel value="Select Date" />
                                    <div class="mt-2 p-2 bg-white rounded-lg shadow max-w-sm">
                                        <div class="flex items-center justify-between mb-2">
                                            <button type="button" @click="previousMonth" class="p-1 hover:bg-gray-100 rounded-full">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                            <h3 class="text-sm font-semibold">{{ currentMonthName }} {{ currentYear }}</h3>
                                            <button type="button" @click="nextMonth" class="p-1 hover:bg-gray-100 rounded-full">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-7 gap-0.5 mb-1">
                                            <div v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']"
                                                 :key="day"
                                                 class="text-center text-xs font-medium text-gray-600 py-1">
                                                {{ day }}
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-7 gap-0.5">
                                            <div v-for="(date, index) in calendarDays"
                                                 :key="index"
                                                 class="aspect-square">
                                                <button
                                                    type="button"
                                                    v-if="date"
                                                    :disabled="!isDateSelectable(date)"
                                                    @click="selectDate(date)"
                                                    :class="[
                                                        'w-full h-full flex items-center justify-center rounded-full text-xs',
                                                        isSelectedDate(date) ? 'bg-blue-600 text-white' :
                                                        isDateSelectable(date) ? 'hover:bg-gray-100' : 'text-gray-400 cursor-not-allowed',
                                                        isPastDate(date) ? 'text-gray-400 cursor-not-allowed' : ''
                                                    ]"
                                                >
                                                    {{ date.getDate() }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.date" class="mt-2" />
                                </div>

                                <!-- Time Slots -->
                                <div v-if="form.date">
                                    <InputLabel value="Available Time Slots" />
                                    <div class="mt-2 grid grid-cols-3 gap-2">
                                        <button
                                            v-for="slot in availableSlots"
                                            :key="slot"
                                            type="button"
                                            :class="[
                                                'px-3 py-2 text-xs font-medium rounded-md transition-colors duration-150',
                                                form.time === slot
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-gray-100 text-gray-900 hover:bg-gray-200'
                                            ]"
                                            @click="form.time = slot"
                                        >
                                            {{ formatTime(slot) }}
                                        </button>
                                    </div>
                                    <InputError :message="form.errors.time" class="mt-2" />
                                </div>
                                <div v-else class="flex items-center justify-center text-gray-500 text-sm">
                                    Please select a date to see available time slots
                                </div>
                            </div>

                            <!-- Barber Selection -->
                            <div class="mt-6">
                                <div v-if="error" class="mb-4 p-4 bg-red-50 text-red-700 rounded-md">
                                    {{ error }}
                                </div>
                                <div v-if="isLoading" class="text-center text-gray-500">
                                    Loading available barbers...
                                </div>
                                <BarberSelector
                                    v-else-if="form.date"
                                    :barbers="barbers"
                                    :selected-date="form.date"
                                    :available-barbers="availableBarberIds"
                                    @select="selectBarber"
                                />
                                <div v-else class="text-center text-gray-500">
                                    Please select a date to see available barbers
                                </div>
                                <InputError :message="form.errors.barber_id" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div class="mt-6">
                                <InputLabel for="notes" value="Additional Notes (Optional)" />
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                ></textarea>
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end mt-6">
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    :processing="form.processing"
                                >
                                    Book Appointment
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';
import InputError from '@/components/InputError.vue';
import Button from '@/components/Button.vue';
import Alert from '@/components/Alert.vue';
import BarberSelector from '@/components/BarberSelector.vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    barbers: {
        type: Array,
        required: true
    }
});

// Calendar state
const currentDate = ref(new Date());
const selectedDate = ref(null);

// Calendar computed properties
const currentMonthName = computed(() => {
    return new Intl.DateTimeFormat('en-US', { month: 'long' }).format(currentDate.value);
});

const currentYear = computed(() => {
    return currentDate.value.getFullYear();
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDay = firstDay.getDay();

    const days = Array(42).fill(null);

    for (let i = 0; i < daysInMonth; i++) {
        days[i + startingDay] = new Date(year, month, i + 1);
    }

    return days;
});

// Calendar methods
const previousMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1);
};

const isDateSelectable = (date) => {
    if (!date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return date >= today;
};

const isPastDate = (date) => {
    if (!date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return date < today;
};

const isSelectedDate = (date) => {
    if (!date || !selectedDate.value) return false;
    return date.toDateString() === selectedDate.value.toDateString();
};

const selectDate = (date) => {
    try {
        if (!isDateSelectable(date)) return;
        selectedDate.value = date;
        form.date = date.toISOString().split('T')[0];
    } catch (err) {
        console.error('Error selecting date:', err);
        error.value = 'Failed to select date. Please try again.';
    }
};

// Time slots (1-hour intervals)
const availableSlots = ref([
    '09:00', '10:00', '11:00', '12:00', '13:00',
    '14:00', '15:00', '16:00', '17:00'
]);

const formatTime = (time) => {
    const [hours, minutes] = time.split(':');
    const date = new Date();
    date.setHours(parseInt(hours), parseInt(minutes));
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
};

const form = useForm({
    barber_id: '',
    date: '',
    time: '',
    notes: ''
});

const availableBarberIds = ref([]);
const isLoading = ref(false);
const error = ref(null);

const selectBarber = (barber) => {
    try {
        if (!barber || !barber.id) return;
        form.barber_id = barber.id;
    } catch (err) {
        console.error('Error selecting barber:', err);
        error.value = 'Failed to select barber. Please try again.';
    }
};

// Fetch available barbers when date changes
watch(() => form.date, async (newDate) => {
    if (newDate) {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await fetch(`/api/available-barbers?date=${newDate}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid response format');
            }

            availableBarberIds.value = Array.isArray(data.available_barber_ids) ? data.available_barber_ids : [];
            availableSlots.value = Array.isArray(data.available_slots) ? data.available_slots : [
                '09:00', '10:00', '11:00', '12:00', '13:00',
                '14:00', '15:00', '16:00', '17:00'
            ];

            // Reset barber selection if previously selected barber is no longer available
            if (form.barber_id && !availableBarberIds.value.includes(form.barber_id)) {
                form.barber_id = '';
            }
        } catch (err) {
            console.error('Error fetching available barbers:', err);
            error.value = 'Failed to load available barbers. Please try again.';
            availableBarberIds.value = [];
            form.barber_id = '';
        } finally {
            isLoading.value = false;
        }
    } else {
        availableBarberIds.value = [];
        form.barber_id = '';
        error.value = null;
    }
}, { immediate: false });

const handleSubmit = (e) => {
    try {
        if (!form.date || !form.time || !form.barber_id) {
            error.value = 'Please fill in all required fields.';
            return;
        }
        form.post(route('booking.store'), {
            preserveScroll: true,
            onError: (errors) => {
                error.value = Object.values(errors)[0] || 'An error occurred while booking. Please try again.';
            }
        });
    } catch (err) {
        console.error('Error submitting form:', err);
        error.value = 'Failed to submit booking. Please try again.';
    }
};
</script>

<style>
.animate-scale-in {
    animation: scale-in 0.2s ease-out;
}

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
</style>

