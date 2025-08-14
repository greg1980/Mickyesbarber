<template>
  <Head title="Business Hours Management" />

  <SidebarLayout>
    <div class="p-3 lg:p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
      <!-- Header -->
      <div class="mb-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 rounded-2xl shadow-xl p-6 lg:p-8 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
          <svg class="w-full h-full" fill="currentColor" viewBox="0 0 100 100">
            <pattern id="business-hours-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="2" cy="2" r="1"/>
              <circle cx="18" cy="18" r="1"/>
              <circle cx="10" cy="10" r="0.5"/>
            </pattern>
            <rect width="100" height="100" fill="url(#business-hours-pattern)"/>
          </svg>
        </div>

        <div class="relative z-10 flex items-center space-x-4">
          <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
            <ClockIcon class="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 class="text-3xl lg:text-4xl font-bold mb-2">Business Hours Management</h1>
            <p class="text-blue-100 text-lg">Manage your barbershop's opening hours and availability</p>
          </div>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="showSuccessMessage" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl shadow-sm">
        <div class="flex items-center space-x-3">
          <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
            <CheckCircleIcon class="w-5 h-5 text-white" />
          </div>
          <p class="text-green-800 font-medium">{{ successMessage }}</p>
        </div>
      </div>

      <div v-if="showErrorMessage" class="mb-6 p-4 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl shadow-sm">
        <div class="flex items-center space-x-3">
          <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
            <ExclamationTriangleIcon class="w-5 h-5 text-white" />
          </div>
          <p class="text-red-800 font-medium">{{ errorMessage }}</p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mb-6 flex flex-col sm:flex-row gap-3">
        <button
          @click="saveChanges"
          :disabled="saving || !hasChanges"
          class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 disabled:from-gray-400 disabled:to-gray-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl disabled:shadow-none disabled:cursor-not-allowed"
        >
          <svg v-if="saving" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <CheckIcon v-else class="w-5 h-5" />
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>

        <button
          @click="resetToDefault"
          :disabled="resetting"
          class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 disabled:from-gray-400 disabled:to-gray-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl disabled:shadow-none disabled:cursor-not-allowed"
        >
          <svg v-if="resetting" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <ArrowPathIcon v-else class="w-5 h-5" />
          {{ resetting ? 'Resetting...' : 'Reset to Default' }}
        </button>
      </div>

      <!-- Business Hours Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <div v-for="(hour, index) in businessHours" :key="hour.id"
             class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 lg:p-6 hover:shadow-xl transition-all duration-200">

          <!-- Day Header -->
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg lg:text-xl font-semibold text-gray-800 capitalize">{{ hour.day_of_week }}</h3>

            <!-- Open/Closed Toggle -->
            <div class="flex items-center space-x-3">
              <span class="text-sm font-medium text-gray-600">Status:</span>
              <button
                @click="toggleDayStatus(index)"
                :class="hour.is_open ? 'bg-green-500' : 'bg-red-500'"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                <span
                  :class="hour.is_open ? 'translate-x-6' : 'translate-x-1'"
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200"
                />
              </button>
              <span class="text-sm font-medium" :class="hour.is_open ? 'text-green-600' : 'text-red-600'">
                {{ hour.is_open ? 'Open' : 'Closed' }}
              </span>
            </div>
          </div>

          <!-- Time Inputs (only show if day is open) -->
          <div v-if="hour.is_open" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Open Time -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Open Time</label>
                <input
                  v-model="hour.open_time"
                  type="time"
                  @change="markAsChanged"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>

              <!-- Close Time -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Close Time</label>
                <input
                  v-model="hour.close_time"
                  type="time"
                  @change="markAsChanged"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>

            <!-- Custom Display Text -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Custom Display Text (Optional)</label>
              <input
                v-model="hour.display_text"
                type="text"
                @change="markAsChanged"
                placeholder="e.g., 10:00 AM - 6:00 PM"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <p class="text-xs text-gray-500 mt-1">Leave empty to use auto-generated format</p>
            </div>
          </div>

          <!-- Closed Day Message -->
          <div v-else class="text-center py-4">
            <div class="text-red-500 font-medium">Closed</div>
            <p class="text-sm text-gray-500 mt-1">No time inputs needed for closed days</p>
          </div>

          <!-- Preview -->
          <div class="mt-4 p-3 bg-gray-50 rounded-lg">
            <div class="text-sm font-medium text-gray-700 mb-1">Preview:</div>
            <div class="text-lg font-semibold" :class="hour.is_open ? 'text-green-600' : 'text-red-600'">
              {{ getPreviewText(hour) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Current Status Info -->
      <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <InformationCircleIcon class="w-6 h-6 text-blue-600" />
          Current Business Status
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-gray-600">Currently:</span>
              <span class="font-semibold" :class="isCurrentlyOpen ? 'text-green-600' : 'text-red-600'">
                {{ isCurrentlyOpen ? 'Open' : 'Closed' }}
              </span>
            </div>

            <div v-if="nextOpening" class="flex items-center justify-between">
              <span class="text-gray-600">Next Opening:</span>
              <span class="font-semibold text-blue-600">{{ nextOpening.day }}, {{ nextOpening.time }}</span>
            </div>
          </div>

          <div class="space-y-3">
            <div class="text-sm text-gray-600">
              <p><strong>Note:</strong> Changes are saved automatically when you click "Save Changes".</p>
              <p class="mt-1">The updated hours will appear on your contact page and footer immediately.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SidebarLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import {
  ClockIcon,
  CheckIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon
} from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
  businessHours: {
    type: Array,
    default: () => []
  }
})

// Reactive data
const businessHours = ref([...props.businessHours])
const saving = ref(false)
const resetting = ref(false)
const showSuccessMessage = ref(false)
const showErrorMessage = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const originalHours = ref([])
const isCurrentlyOpen = ref(false)
const nextOpening = ref(null)

// Computed properties
const hasChanges = computed(() => {
  if (originalHours.value.length === 0) return false

  return JSON.stringify(businessHours.value) !== JSON.stringify(originalHours.value)
})

// Methods
function toggleDayStatus(index) {
  businessHours.value[index].is_open = !businessHours.value[index].is_open

  // Clear times if closing the day
  if (!businessHours.value[index].is_open) {
    businessHours.value[index].open_time = null
    businessHours.value[index].close_time = null
    businessHours.value[index].display_text = ''
  }

  markAsChanged()
}

function markAsChanged() {
  // This function is called when any input changes
  // The hasChanges computed property will automatically update
}

function getPreviewText(hour) {
  if (!hour.is_open) return 'Closed'

  if (hour.display_text) return hour.display_text

  if (hour.open_time && hour.close_time) {
    const open = formatTime(hour.open_time)
    const close = formatTime(hour.close_time)
    return `${open} - ${close}`
  }

  return 'Hours TBD'
}

function formatTime(timeString) {
  if (!timeString) return ''

  const [hours, minutes] = timeString.split(':')
  const hour = parseInt(hours)
  const ampm = hour >= 12 ? 'PM' : 'AM'
  const displayHour = hour > 12 ? hour - 12 : hour === 0 ? 12 : hour

  return `${displayHour}:${minutes} ${ampm}`
}

async function saveChanges() {
  if (!hasChanges.value) return

  saving.value = true
  showSuccessMessage.value = false
  showErrorMessage.value = false

  try {
    const response = await axios.post('/admin/business-hours/update', {
      businessHours: businessHours.value
    })

    // Update the business hours with the response
    businessHours.value = response.data.businessHours
    originalHours.value = JSON.parse(JSON.stringify(businessHours.value))

    successMessage.value = response.data.message
    showSuccessMessage.value = true

    // Hide success message after 5 seconds
    setTimeout(() => {
      showSuccessMessage.value = false
    }, 5000)

  } catch (error) {
    errorMessage.value = error.response?.data?.error || 'Failed to save changes'
    showErrorMessage.value = true

    // Hide error message after 5 seconds
    setTimeout(() => {
      showErrorMessage.value = false
    }, 5000)
  } finally {
    saving.value = false
  }
}

async function resetToDefault() {
  if (!confirm('Are you sure you want to reset all business hours to default values? This action cannot be undone.')) {
    return
  }

  resetting.value = true
  showSuccessMessage.value = false
  showErrorMessage.value = false

  try {
    const response = await axios.post('/admin/business-hours/reset')

    businessHours.value = response.data.businessHours
    originalHours.value = JSON.parse(JSON.stringify(businessHours.value))

    successMessage.value = response.data.message
    showSuccessMessage.value = true

    setTimeout(() => {
      showSuccessMessage.value = false
    }, 5000)

  } catch (error) {
    errorMessage.value = error.response?.data?.error || 'Failed to reset business hours'
    showErrorMessage.value = true

    setTimeout(() => {
      showErrorMessage.value = false
    }, 5000)
  } finally {
    resetting.value = false
  }
}

async function checkBusinessStatus() {
  try {
    const response = await axios.get('/api/business-hours')
    const hours = response.data.businessHours

    // Check if currently open
    const today = new Date()
    const todayName = today.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase()
    const todayHour = hours.find(h => h.day.toLowerCase() === todayName)

    if (todayHour && todayHour.is_open) {
      // For now, we'll just show if the day is marked as open
      // In a real implementation, you'd check the actual time
      isCurrentlyOpen.value = true
    } else {
      isCurrentlyOpen.value = false
    }

    // Find next opening
    const openDays = hours.filter(h => h.is_open)
    if (openDays.length > 0) {
      nextOpening.value = {
        day: openDays[0].day,
        time: openDays[0].hours
      }
    }

  } catch (error) {
    console.error('Failed to check business status:', error)
  }
}

onMounted(() => {
  // Store original hours for change detection
  originalHours.value = JSON.parse(JSON.stringify(businessHours.value))

  // Check business status
  checkBusinessStatus()
})
</script>

<style scoped>
/* Mobile-specific styles */
@media (max-width: 640px) {
  .grid {
    grid-template-columns: 1fr;
  }

  .flex-col.sm\:flex-row {
    flex-direction: column;
  }

  .gap-3 {
    gap: 0.75rem;
  }
}
</style>
