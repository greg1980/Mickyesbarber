<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Reschedule Appointment
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Please select a new date and time for your appointment.
                                </p>

                                <!-- Date Picker -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">New Date</label>
                                    <input
                                        type="date"
                                        v-model="newDate"
                                        :min="minDate"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                </div>

                                <!-- Time Slots -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">New Time</label>
                                    <div class="mt-1 grid grid-cols-3 gap-2">
                                        <button
                                            v-for="time in availableTimeSlots"
                                            :key="time"
                                            :class="[
                                                'px-2 py-1 text-sm rounded-md',
                                                newTime === time
                                                    ? 'bg-green-600 text-white'
                                                    : isSlotBooked(time)
                                                        ? 'bg-pink-100 text-pink-500 cursor-not-allowed'
                                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                            ]"
                                            @click="!isSlotBooked(time) && (newTime = time)"
                                            :disabled="isSlotBooked(time)"
                                        >
                                            {{ time }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button
                        type="button"
                        class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                        :disabled="!isValid || isProcessing"
                        @click="confirm"
                    >
                        <svg v-if="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isProcessing ? 'Rescheduling...' : 'Confirm Reschedule' }}
                    </button>
                    <button
                        type="button"
                        class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="cancel"
                    >
                        Cancel
                    </button>
                </div>
                <div v-if="errorMessage" class="text-red-500 text-sm mt-2">
                    {{ errorMessage }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { format } from 'date-fns';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    booking: Object,
    availableTimeSlots: Array,
    isProcessing: Boolean,
    bookedSlots: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['confirm', 'cancel', 'dateSelected', 'close']);

const newDate = ref('');
const newTime = ref('');
const errorMessage = ref('');

// Reset form when modal is opened/closed
watch(() => props.show, (newVal) => {
    if (!newVal) {
        newDate.value = '';
        newTime.value = '';
    }
});

// Watch for date changes to fetch booked slots
watch(() => newDate.value, (date) => {
    if (date) {
        emit('dateSelected', date);
    }
});

const minDate = computed(() => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return today.toISOString().split('T')[0];
});

const isValid = computed(() => {
    return newDate.value && newTime.value;
});

const confirm = async () => {
    try {
        const formattedDate = format(new Date(newDate.value), 'yyyy-MM-dd');

        const response = await axios.post(`/bookings/${props.booking.id}/reschedule`, {
            new_date: formattedDate,
            new_time: newTime.value,
            barber_id: props.booking.barber_id
        });

        if (response.data.success) {
            // Handle success
            emit('close');
            window.location.reload();
        }
    } catch (error) {
        console.error('Error rescheduling booking:', error);
        errorMessage.value = error.response?.data?.message || 'Failed to reschedule booking';
    }
};

const cancel = () => {
    newDate.value = '';
    newTime.value = '';
    emit('cancel');
};

const isSlotBooked = (time) => {
    // Don't consider the current booking's time slot as booked
    if (props.booking && props.booking.booking_time === time) {
        return false;
    }
    return props.bookedSlots.includes(time);
};
</script>
