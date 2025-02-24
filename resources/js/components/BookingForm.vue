<template>
    <!-- ... other template content ... -->
    <button
        @click="handleBooking"
        :disabled="!canBook"
        class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed mt-4"
    >
        {{ !selectedBarber ? 'Please select a barber' : 'Book Appointment' }}
    </button>
</template>
