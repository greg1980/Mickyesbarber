<template>
  <Head title="Today's Appointments" />
  <SidebarLayout>
    <div class="min-h-screen bg-gray-100 p-8">
      <h1 class="text-2xl font-bold mb-6">Appointments</h1>
      <div v-if="successMessage"
        class="fixed left-1/2 z-50 px-6 py-3 rounded border border-green-300 bg-green-100 text-green-900 shadow transition-all duration-300"
        style="top: 7rem; transform: translateX(-50%);"
      >
        {{ successMessage }}
      </div>
      <div class="mb-6 flex flex-col sm:flex-row items-center gap-2">
        <div class="relative">
          <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          </span>
          <input type="date" v-model="selectedDate"
            class="pl-10 pr-4 py-2 rounded-lg border-2 border-blue-400 bg-blue-50 text-blue-900 focus:border-blue-600 focus:bg-white focus:outline-none transition-all duration-200 shadow-sm"
          />
        </div>
        <span class="ml-2 text-blue-600 font-semibold">Pick a date</span>
        <div class="relative flex-1 max-w-xs ml-auto">
          <input
            type="text"
            v-model="search"
            placeholder="Search by customer name..."
            class="w-full pl-10 pr-4 py-2 rounded-lg border-2 border-green-400 bg-green-50 text-green-900 focus:border-green-600 focus:bg-white focus:outline-none transition-all duration-200 shadow-sm"
          />
          <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-green-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          </span>
        </div>
      </div>
      <div>
        <div v-if="appointments.data.length">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div
              v-for="appointment in appointments.data"
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
              <div class="mt-3 flex space-x-16 items-center justify-center">
                <template v-if="appointment.status !== 'completed' && appointment.status !== 'cancelled'">
                  <div class="flex flex-col items-center">
                    <button
                      @click="updateStatus(appointment.id, 'completed')"
                      class="bg-green-500 hover:bg-green-600 text-white rounded-full w-7 h-7 flex items-center justify-center transition-colors mb-1"
                      :title="'Mark as Completed'"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="12" fill="currentColor" opacity="0.1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                    </button>
                    <span class="text-xs text-green-700 font-medium">Mark as Complete</span>
                  </div>
                  <div class="flex flex-col items-center">
                    <button
                      @click="updateStatus(appointment.id, 'cancelled')"
                      class="bg-red-500 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center transition-colors mb-1"
                      :title="'Cancel Appointment'"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="12" fill="currentColor" opacity="0.1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                    <span class="text-xs text-red-700 font-medium">Cancel</span>
                  </div>
                </template>
              </div>
            </div>
          </div>
          <div class="flex justify-center mt-6" v-if="appointments.links && appointments.links.length > 1">
            <button
              v-for="link in appointments.links"
              :key="link.label"
              :disabled="!link.url"
              @click="goToPage(link.url)"
              v-html="link.label"
              class="mx-1 px-3 py-1 rounded border text-blue-600 bg-white hover:bg-blue-50"
              :class="{ 'bg-blue-200 text-white': link.active, 'opacity-50 cursor-not-allowed': !link.url }"
            />
          </div>
        </div>
        <div v-else class="bg-white shadow rounded-lg p-8 text-center text-gray-400">
          No appointments for this date.
        </div>
      </div>

      <!-- Status Update Modal -->
      <Modal :show="showStatusModal" @close="showStatusModal = false">
        <div class="bg-white rounded-lg shadow-lg min-w-[350px] max-w-md mx-auto">
          <div class="p-4 border-b bg-gray-50 rounded-t-lg text-center">
            <h2 class="text-xl font-semibold text-gray-900">Update Appointment Status</h2>
          </div>
          <div class="px-6 py-8 flex flex-col items-center">
            <p class="text-gray-700 mb-8 text-center">
              Are you sure you want to mark this appointment as completed?
            </p>
            <div class="flex justify-center space-x-4 w-full">
              <button
                @click="showStatusModal = false"
                class="px-6 py-2 rounded border border-gray-300 bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition"
              >
                Cancel
              </button>
              <button
                @click="confirmStatusUpdate"
                :disabled="processing"
                class="px-6 py-2 rounded border border-green-500 text-green-600 font-semibold bg-transparent hover:bg-green-500 hover:text-white transition disabled:opacity-50"
              >
                Confirm
              </button>
            </div>
          </div>
        </div>
      </Modal>
    </div>
  </SidebarLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { defineProps, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  appointments: {
    type: Object,
    default: () => ({ data: [] })
  },
  selectedDate: {
    type: String,
    default: () => new Date().toISOString().slice(0, 10)
  },
  search: {
    type: String,
    default: ''
  }
})

const selectedDate = ref(props.selectedDate)
const search = ref(props.search)
const showStatusModal = ref(false)
const selectedAppointmentId = ref(null)
const selectedStatus = ref('')
const processing = ref(false)
const successMessage = ref('')

watch([selectedDate, search], ([date, term]) => {
  router.get('/barber/appointments', { date, search: term }, { preserveState: true })
})

function goToPage(url) {
  if (url) router.get(url, { date: selectedDate.value, search: search.value }, { preserveState: true })
}

const updateStatus = (appointmentId, status) => {
  selectedAppointmentId.value = appointmentId
  selectedStatus.value = status
  showStatusModal.value = true
}

const confirmStatusUpdate = async () => {
  if (!selectedAppointmentId.value || !selectedStatus.value) return

  processing.value = true
  try {
    const response = await axios.patch(`/barber/appointments/${selectedAppointmentId.value}/status`, {
      status: selectedStatus.value
    })

    // Close modal immediately
    showStatusModal.value = false
    // Show confirmation message
    successMessage.value = `Appointment marked as ${selectedStatus.value}.`
    setTimeout(() => {
      successMessage.value = ''
    }, 23000)

    // Refresh the appointments list
    router.reload({ only: ['appointments'] })
  } catch (error) {
    console.error('Error updating appointment status:', error)
  } finally {
    processing.value = false
  }
}
</script>
