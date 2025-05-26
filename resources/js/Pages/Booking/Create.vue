<template>
  <SidebarLayout>
    <Head title="Book Appointment" />
    <div class="min-h-screen bg-gray-50">
      <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Book Your Appointment</h1>

            <div class="bg-white shadow-lg rounded-lg p-6">
              <form @submit.prevent="submitForm" class="space-y-6">
                <div v-if="errorMessage" class="mb-4 text-red-600 text-center font-semibold">{{ errorMessage }}</div>
                <!-- Service Selection -->
                <div>
                  <label for="service" class="block text-sm font-medium text-gray-700">Select Service</label>
                  <select
                    id="service"
                    v-model="form.service"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required
                  >
                    <option value="">Choose a service...</option>
                    <option value="classic-cut">Classic Haircut - $30</option>
                    <option value="kids-cut">Kids Haircut - $25</option>
                    <option value="hair-color">Hair Coloring - $45</option>
                    <option value="beard-grooming">Beard Grooming - $20</option>
                    <option value="mobile-service">Mobile Service - $50</option>
                    <option value="special-treatment">Special Treatment - $35</option>
                  </select>
                </div>

                <!-- Date Selection -->
                <div>
                  <label for="date" class="block text-sm font-medium text-gray-700">Select Date</label>
                  <div class="relative">
                    <div class="flex items-center space-x-2">
                      <button type="button" @click="showCalendar = !showCalendar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white text-left px-3 py-2">
                        {{ form.date ? form.date : 'Select a date...' }}
                      </button>
                    </div>
                    <div v-if="showCalendar" class="absolute z-10 bg-white border rounded shadow-lg mt-2 p-4">
                      <div class="flex items-center justify-between mb-2">
                        <button type="button" @click="prevMonth" class="p-1 hover:bg-gray-100 rounded-full">&lt;</button>
                        <span class="font-semibold">{{ monthNames[currentMonth] }} {{ currentYear }}</span>
                        <button type="button" @click="nextMonth" class="p-1 hover:bg-gray-100 rounded-full">&gt;</button>
                      </div>
                      <div class="grid grid-cols-7 gap-1 mb-1">
                        <div v-for="day in weekDays" :key="day" class="text-center text-xs font-medium text-gray-600 py-1">{{ day }}</div>
                      </div>
                      <div class="grid grid-cols-7 gap-1">
                        <div v-for="(date, idx) in calendarDays" :key="idx" class="aspect-square">
                          <button
                            v-if="date"
                            type="button"
                            :disabled="isPastDate(date)"
                            @click="selectDate(date)"
                            :class="[
                              'w-full h-full flex items-center justify-center rounded-full text-xs',
                              isSelectedDate(date) ? 'bg-green-600 text-white' : isPastDate(date) ? 'text-gray-400 cursor-not-allowed' : 'hover:bg-gray-100'
                            ]"
                          >
                            {{ date.getDate() }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="text-xs text-gray-500 mt-1">Date must be in YYYY-MM-DD format.</div>
                </div>

                <!-- Time Selection -->
                <div>
                  <label for="time" class="block text-sm font-medium text-gray-700">Select Time</label>
                  <select
                    id="time"
                    v-model="form.time"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required
                  >
                    <option value="">Choose a time...</option>
                    <option v-for="time in availableTimes" :key="time" :value="time">
                      {{ time }}
                    </option>
                  </select>
                </div>

                <!-- Barber Selection -->
                <div v-if="form.date && form.time">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Select Barber</label>
                  <div v-if="loadingBarbers" class="text-gray-500 text-sm">Loading available barbers...</div>
                  <div v-else-if="barberError" class="text-red-500 text-sm">{{ barberError }}</div>
                  <div v-else-if="availableBarbers.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label v-for="barber in availableBarbers" :key="barber.id"
                      class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors duration-150 hover:bg-gray-50"
                      :class="form.barber_id === barber.id ? 'border-green-500 ring-2 ring-green-300' : 'border-gray-300'">
                      <img
                        v-if="barber.user && barber.user.profile_photo"
                        :src="`/storage/${barber.user.profile_photo}`"
                        alt="Barber photo"
                        class="w-12 h-12 rounded-full object-cover mr-3 border"
                        :class="form.barber_id === barber.id ? 'border-4 border-green-500' : 'border'"
                      />
                      <div v-else class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 mr-3 font-bold text-lg"
                        :class="form.barber_id === barber.id ? 'ring-2 ring-green-400 border-green-500 border-4' : 'border'">
                        {{ (barber.user && barber.user.name ? barber.user.name.charAt(0) : 'B') }}
                      </div>
                      <input
                        type="radio"
                        v-model="form.barber_id"
                        :value="barber.id"
                        class="mr-2"
                        required
                      />
                      <span>{{ barber.user && barber.user.name ? barber.user.name : ('Barber #' + barber.id) }}</span>
                    </label>
                  </div>
                  <div v-else class="text-gray-500 text-sm">No barbers available for this slot.</div>
                </div>

                <!-- Notes -->
                <div>
                  <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    placeholder="Any specific requests or requirements?"
                  ></textarea>
                </div>

                <!-- Submit Button -->
                <div>
                  <button
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    :disabled="form.processing"
                  >
                    Book Appointment
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </main>
    </div>
    <PaymentModal
      :show="showPaymentModal"
      :booking="createdBooking"
      @close="showPaymentModal = false"
      @payment-success="handlePaymentSuccess"
    />
  </SidebarLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import PaymentModal from '@/Components/PaymentModal.vue'

const form = ref({
  service: '',
  date: '',
  time: '',
  barber_id: '',
  notes: '',
  processing: false
})

// Get today's date for the minimum date
const today = new Date()
const minDate = computed(() => {
  return today.toISOString().split('T')[0]
})

// 1-hour time slots (9 AM to 6 PM)
const availableTimes = [
  '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
]

const availableBarbers = ref([])
const loadingBarbers = ref(false)
const barberError = ref('')

watch(() => [form.value.date, form.value.time], async ([date, time]) => {
  form.value.barber_id = ''
  availableBarbers.value = []
  barberError.value = ''
  if (date && time) {
    loadingBarbers.value = true
    try {
      const response = await fetch(`/api/available-barbers?date=${date}&time=${time}`)
      const data = await response.json()
      availableBarbers.value = data.barbers || []
    } catch (e) {
      barberError.value = 'Failed to load available barbers.'
    } finally {
      loadingBarbers.value = false
    }
  }
})

const showPaymentModal = ref(false)
const createdBooking = ref(null)

const errorMessage = ref('')

const showCalendar = ref(false)
const todayDate = new Date()
const weekDays = ['S', 'M', 'T', 'W', 'T', 'F', 'S']
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]
const currentMonth = ref(todayDate.getMonth())
const currentYear = ref(todayDate.getFullYear())

const calendarDays = computed(() => {
  const year = currentYear.value
  const month = currentMonth.value
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const daysInMonth = lastDay.getDate()
  const startingDay = firstDay.getDay()
  const days = Array(42).fill(null)
  for (let i = 0; i < daysInMonth; i++) {
    days[i + startingDay] = new Date(year, month, i + 1)
  }
  return days
})

function prevMonth() {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value--
  } else {
    currentMonth.value--
  }
}
function nextMonth() {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value++
  } else {
    currentMonth.value++
  }
}
function isPastDate(date) {
  if (!date) return true
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return date < today
}
function formatDateLocal(date) {
  // Formats a JS Date object as YYYY-MM-DD in local time
  const yyyy = date.getFullYear();
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const dd = String(date.getDate()).padStart(2, '0');
  return `${yyyy}-${mm}-${dd}`;
}
function isSelectedDate(date) {
  if (!date || !form.value.date) return false
  return formatDateLocal(date) === form.value.date
}
function selectDate(date) {
  if (!date || isPastDate(date)) return
  form.value.date = formatDateLocal(date)
  showCalendar.value = false
}

const submitForm = async () => {
  // Validate date format
  const datePattern = /^\d{4}-\d{2}-\d{2}$/;
  if (!datePattern.test(form.value.date)) {
    errorMessage.value = 'Date must be in YYYY-MM-DD format (e.g., 2025-05-23).';
    return;
  }
  form.value.processing = true
  errorMessage.value = ''
  try {
    const response = await fetch('/booking', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({
        service: form.value.service,
        booking_date: form.value.date,
        booking_time: form.value.time,
        barber_id: form.value.barber_id,
        notes: form.value.notes
      })
    })
    if (!response.ok) {
      const data = await response.json()
      if (response.status === 409 && data.error) {
        errorMessage.value = data.error
      } else {
        errorMessage.value = data.message || 'Booking failed'
      }
      return
    }
    const booking = await response.json()
    createdBooking.value = booking.booking
    showPaymentModal.value = true
    // Optionally reset form
    form.value = {
      service: '',
      date: '',
      time: '',
      barber_id: '',
      notes: '',
      processing: false
    }
  } catch (e) {
    errorMessage.value = e.message || 'Booking failed. Please try again.'
  } finally {
    form.value.processing = false
  }
}

const handlePaymentSuccess = () => {
  showPaymentModal.value = false
  window.location.href = '/customer/bookings'
}
</script>
