<template>
  <Head title="Today's Appointments" />
  <SidebarLayout>
    <div class="min-h-screen bg-gray-100 p-8">
      <h1 class="text-2xl font-bold mb-6">Today's Appointments</h1>
      <div>
        <div v-if="appointments.length">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div
              v-for="appointment in appointments.slice(0, 10)"
              :key="appointment.id"
              class="flex flex-col items-center bg-white shadow rounded-lg p-4 transition-transform duration-200 hover:-translate-y-1 hover:shadow-lg"
            >
              <div class="text-xs text-gray-500 mb-1">
                {{ appointment.booking_time ? new Date(appointment.booking_time).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: '2-digit' }) : 'N/A' }}
              </div>
              <div class="text-lg font-bold mb-2">
                {{ appointment.booking_time ? new Date(appointment.booking_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
              </div>
              <img
                :src="appointment.user?.profile_photo_url || 'https://ui-avatars.com/api/?name=' + (appointment.user?.name || 'U')"
                class="w-12 h-12 rounded-full object-cover border mb-2"
                alt="Client photo"
              />
              <div class="font-semibold mb-1 text-center">{{ appointment.user?.name || 'N/A' }}</div>
              <div class="text-gray-500 text-sm mb-2 text-center">{{ appointment.service_name || appointment.service?.name || 'N/A' }}</div>
              <span
                v-if="appointment.status && appointment.status.toLowerCase() === 'completed'"
                class="bg-green-100 text-green-800 px-3 py-1 rounded text-xs font-semibold"
              >
                COMPLETED
              </span>
              <span
                v-else
                class="bg-gray-100 text-gray-800 px-3 py-1 rounded text-xs font-semibold"
              >
                {{ appointment.status ? appointment.status.toUpperCase() : 'N/A' }}
              </span>
            </div>
          </div>
        </div>
        <div v-else class="bg-white shadow rounded-lg p-8 text-center text-gray-400">
          No appointments for today.
        </div>
      </div>
    </div>
  </SidebarLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { defineProps } from 'vue'

defineProps({
  appointments: {
    type: Array,
    default: () => []
  }
})
</script>
