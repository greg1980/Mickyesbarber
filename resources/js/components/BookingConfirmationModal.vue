<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Confirm Your Booking
            </h2>

            <div class="mt-4">
                <p class="text-sm text-gray-600 mb-4">
                    Please confirm your booking details:
                </p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date</p>
                                <p class="mt-1">{{ formatDate(bookingDate) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Time</p>
                                <p class="mt-1">{{ bookingTime }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Barber</p>
                            <p class="mt-1">{{ barber?.name || 'Not selected' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Service Price</p>
                                <p class="mt-1">£{{ formatPrice(servicePrice) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Deposit Required</p>
                                <p class="mt-1">£{{ formatPrice(depositAmount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="errorMessage" class="mt-4 p-4 bg-red-50 text-red-600 rounded-md">
                {{ errorMessage }}
            </div>

            <div v-if="successMessage" class="mt-4 p-4 bg-green-50 text-green-600 rounded-md">
                {{ successMessage }}
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton @click="$emit('close')" :disabled="isProcessing">
                    Cancel
                </SecondaryButton>
                <PrimaryButton @click="$emit('confirm')" :disabled="isProcessing">
                    <span v-if="isProcessing" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                    <span v-else>
                        Confirm Booking
                    </span>
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';

const props = defineProps({
    show: Boolean,
    bookingDate: Date,
    bookingTime: String,
    barber: {
        type: Object,
        required: false,
        default: null
    },
    servicePrice: {
        type: Number,
        required: true,
        default: 25.00
    },
    depositAmount: {
        type: Number,
        required: true,
        default: 6.25
    },
    isProcessing: Boolean,
    errorMessage: String,
    successMessage: String
});

defineEmits(['close', 'confirm']);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatPrice = (price) => {
    return price.toFixed(2);
};
</script>
