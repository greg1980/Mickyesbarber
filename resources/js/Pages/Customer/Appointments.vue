<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900">My Appointments</h1>
    </div>

    <!-- Appointments List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="divide-y divide-gray-200">
        <div v-for="booking in bookings" :key="booking.id" class="p-6">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-medium text-gray-900">
                {{ booking.service.name }}
              </h3>
              <p class="mt-1 text-sm text-gray-500">
                with {{ booking.barber.user.name }}
              </p>
              <p class="mt-1 text-sm text-gray-500">
                {{ formatDate(booking.booking_time) }}
              </p>
              <p class="mt-1 text-sm text-gray-500">
                Status: <span :class="getStatusClass(booking.status)">{{ formatStatus(booking.status) }}</span>
              </p>
            </div>
            <div class="flex space-x-3">
              <!-- Check In Button -->
              <button
                v-if="canCheckIn(booking)"
                @click="checkIn(booking)"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
              >
                <CheckCircleIcon class="h-5 w-5 mr-2" />
                Check In
              </button>
              <!-- Reschedule Button -->
              <button
                v-if="canReschedule(booking)"
                @click="showRescheduleModal(booking)"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <CalendarIcon class="h-5 w-5 mr-2" />
                Reschedule
              </button>
              <!-- Cancel Button -->
              <button
                v-if="canCancel(booking)"
                @click="showCancelModal(booking)"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <XMarkIcon class="h-5 w-5 mr-2" />
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { CalendarIcon, XMarkIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'
import { format } from 'date-fns'

// ... existing code ...

const canCheckIn = (booking) => {
  const now = new Date()
  const bookingTime = new Date(booking.booking_time)
  const timeDiff = bookingTime - now
  const hoursDiff = timeDiff / (1000 * 60 * 60)

  return (
    booking.status === 'confirmed' &&
    hoursDiff >= -1 && // Can check in up to 1 hour before
    hoursDiff <= 1 && // Can check in up to 1 hour after
    !booking.checked_in_at // Haven't checked in yet
  )
}

const checkIn = async (booking) => {
  try {
    const response = await axios.post(`/booking/${booking.id}/check-in`)
    // Update the booking in the list
    const index = bookings.value.findIndex(b => b.id === booking.id)
    if (index !== -1) {
      bookings.value[index] = response.data.booking
    }
  } catch (error) {
    console.error('Failed to check in:', error)
  }
}

// ... existing code ...
</script>
