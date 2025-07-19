<template>
  <Head title="Appointments" />
  <SidebarLayout>
    <div class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
      <!-- Enhanced Header Section -->
      <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Appointments</h1>
                <p class="text-gray-600 mt-1">Manage your daily schedule and customer appointments</p>
              </div>
            </div>
            <!-- Quick Stats -->
            <div class="hidden md:flex items-center space-x-6">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ appointments.data.length }}</div>
                <div class="text-sm text-gray-500">Today</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ appointments.data.filter(apt => apt.status === 'completed').length }}</div>
                <div class="text-sm text-gray-500">Completed</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ appointments.data.filter(apt => apt.status === 'confirmed').length }}</div>
                <div class="text-sm text-gray-500">Pending</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage"
        class="fixed left-1/2 z-50 px-6 py-4 rounded-lg bg-green-50 border border-green-200 text-green-800 shadow-lg transition-all duration-300 transform -translate-x-1/2"
        style="top: 7rem;"
      >
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          {{ successMessage }}
        </div>
      </div>

      <!-- Enhanced Filter Controls -->
      <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex flex-col lg:flex-row lg:items-center gap-6">
            <!-- Date Picker Section -->
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                  </svg>
                </div>
                <input
                  type="date"
                  v-model="selectedDate"
                  class="w-full pl-10 pr-4 py-3 border-2 border-blue-200 rounded-lg bg-blue-50 text-gray-900 focus:border-blue-500 focus:bg-white focus:outline-none transition-all duration-200 font-medium"
                />
              </div>
              <p class="text-blue-600 text-sm mt-1 font-medium">Pick a date to view appointments</p>
            </div>

            <!-- Search Section -->
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">Search Customers</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                  </svg>
                </div>
                <input
                  type="text"
                  v-model="search"
                  placeholder="Search by customer name..."
                  class="w-full pl-10 pr-4 py-3 border-2 border-green-200 rounded-lg bg-green-50 text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none transition-all duration-200"
                />
              </div>
              <p class="text-green-600 text-sm mt-1 font-medium">Find specific appointments</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Appointments Grid -->
      <div>
        <div v-if="appointments.data.length">
          <!-- Mobile Quick Stats -->
          <div class="md:hidden mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
              <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                  <div class="text-xl font-bold text-blue-600">{{ appointments.data.length }}</div>
                  <div class="text-xs text-gray-500">Today</div>
                </div>
                <div>
                  <div class="text-xl font-bold text-green-600">{{ appointments.data.filter(apt => apt.status === 'completed').length }}</div>
                  <div class="text-xs text-gray-500">Completed</div>
                </div>
                <div>
                  <div class="text-xl font-bold text-purple-600">{{ appointments.data.filter(apt => apt.status === 'confirmed').length }}</div>
                  <div class="text-xs text-gray-500">Pending</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Appointment Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
            <div
              v-for="appointment in appointments.data"
              :key="appointment.id"
              class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 transition-all duration-300 hover:shadow-md hover:-translate-y-1 group"
            >
              <!-- Enhanced Date & Time Display -->
              <div class="text-center mb-6">
                <div class="text-sm font-medium text-gray-500 mb-1">
                  {{ appointment.booking_time ? new Date(appointment.booking_time).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' }) : 'Date TBD' }}
                </div>
                <div class="text-2xl font-bold text-gray-900 mb-2">
                  {{ appointment.booking_time ? new Date(appointment.booking_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '--:--' }}
                </div>
                <div class="h-1 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full mx-auto w-12 group-hover:w-16 transition-all duration-300"></div>
              </div>

              <!-- Enhanced User Profile -->
              <div class="text-center mb-6">
                <div class="relative inline-block mb-3">
                  <img
                    :src="appointment.user?.profile_photo_url || 'https://ui-avatars.com/api/?name=' + (appointment.user?.name || 'U')"
                    class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg"
                    alt="Client photo"
                  />
                  <!-- Status Indicator on Avatar -->
                  <div
                    v-if="appointment.status === 'completed'"
                    class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white flex items-center justify-center"
                  >
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                  <div
                    v-else-if="appointment.status === 'cancelled'"
                    class="absolute -bottom-1 -right-1 w-6 h-6 bg-red-500 rounded-full border-2 border-white flex items-center justify-center"
                  >
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </div>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ appointment.user?.name || 'N/A' }}</h3>
                <p class="text-gray-500 text-sm">{{ appointment.service_name || appointment.service?.name || 'Service TBD' }}</p>
              </div>

              <!-- Enhanced Status Badge -->
              <div class="text-center mb-6">
                <span
                  v-if="appointment.status && appointment.status.toLowerCase() === 'completed'"
                  class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  COMPLETED
                </span>
                <span
                  v-else-if="appointment.status && appointment.status.toLowerCase() === 'cancelled'"
                  class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  CANCELLED
                </span>
                <span
                  v-else
                  class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 border border-blue-200"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ appointment.status ? appointment.status.toUpperCase() : 'CONFIRMED' }}
                </span>
              </div>

              <!-- Enhanced Action Buttons -->
              <div v-if="appointment.status !== 'completed' && appointment.status !== 'cancelled'" class="flex justify-center gap-3">
                <!-- Complete Button -->
                <button
                  @click="updateStatus(appointment.id, 'completed')"
                  class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg px-4 py-3 font-medium transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 flex items-center justify-center"
                  title="Mark as Completed"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                  Complete
                </button>

                <!-- Cancel Button -->
                <button
                  @click="updateStatus(appointment.id, 'cancelled')"
                  class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg px-4 py-3 font-medium transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 flex items-center justify-center"
                  title="Cancel Appointment"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Cancel
                </button>
              </div>

              <!-- Completed/Cancelled State -->
              <div v-else class="text-center">
                <div class="text-gray-500 text-sm italic">
                  {{ appointment.status === 'completed' ? 'This appointment has been completed' : 'This appointment was cancelled' }}
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced Pagination -->
          <div v-if="appointments.links && appointments.links.length > 1" class="mt-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
              <div class="flex justify-center">
                <div class="flex items-center space-x-2">
                  <button
                    v-for="link in appointments.links"
                    :key="link.label"
                    :disabled="!link.url"
                    @click="goToPage(link.url)"
                    {{ link.label }}
                    class="min-w-[44px] h-11 px-4 py-2 rounded-lg font-medium transition-all duration-200 text-sm"
                    :class="{
                      'bg-blue-600 text-white shadow-md hover:bg-blue-700': link.active,
                      'text-gray-600 bg-gray-100 hover:bg-gray-200': !link.active && link.url,
                      'text-gray-400 bg-gray-50 cursor-not-allowed': !link.url
                    }"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Empty State -->
        <div v-else class="text-center py-16">
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
            <div class="flex flex-col items-center">
              <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                </svg>
              </div>
              <h3 class="text-2xl font-semibold text-gray-900 mb-2">No appointments found</h3>
              <p class="text-gray-600 mb-6 max-w-md">
                {{ search ? `No appointments match "${search}" for the selected date.` : 'There are no appointments scheduled for the selected date.' }}
              </p>
              <div class="flex flex-col sm:flex-row gap-3">
                <button
                  v-if="search"
                  @click="search = ''"
                  class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  Clear Search
                </button>
                <button
                  @click="selectedDate = new Date().toISOString().slice(0, 10)"
                  class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                  </svg>
                  View Today
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Status Update Modal -->
      <Modal :show="showStatusModal" @close="showStatusModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md mx-auto overflow-hidden">
          <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
              <div class="h-8 w-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                <PencilSquareIcon class="w-5 h-5 text-white" />
              </div>
              Confirm Action
            </h2>
          </div>
          <div class="px-6 py-8">
            <div class="text-center mb-8">
              <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <p class="text-gray-700 text-lg font-medium">
                Are you sure you want to mark this appointment as
                <span :class="selectedStatus === 'completed' ? 'text-green-600' : 'text-red-600'" class="font-semibold">
                  {{ selectedStatus }}
                </span>?
              </p>
              <p class="text-gray-500 text-sm mt-2">This action cannot be undone.</p>
            </div>
            <div class="flex gap-4">
              <button
                @click="showStatusModal = false"
                class="flex-1 px-6 py-3 border-2 border-gray-300 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200"
              >
                Cancel
              </button>
              <button
                @click="confirmStatusUpdate"
                :disabled="processing"
                :class="selectedStatus === 'completed' ? 'bg-green-600 hover:bg-green-700 border-green-600' : 'bg-red-600 hover:bg-red-700 border-red-600'"
                class="flex-1 px-6 py-3 border-2 text-white font-semibold rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="processing" class="flex items-center justify-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </span>
                <span v-else>Confirm</span>
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
import { defineProps, ref, watch, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { PencilSquareIcon } from '@heroicons/vue/24/outline'

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
const showSuccess = ref(false)

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
    }, 3000)

    // Refresh the appointments list
    router.reload({ only: ['appointments'] })
  } catch (error) {
    console.error('Error updating appointment status:', error)
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  const params = new URLSearchParams(window.location.search)
  if (params.get('success') === '1') {
    showSuccess.value = true
    window.history.replaceState({}, document.title, window.location.pathname)
  }
})
</script>
