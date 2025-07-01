<template>
    <Head title="Customer Dashboard" />

    <SidebarLayout>
        <div class="p-3 lg:p-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="bg-white shadow rounded px-3 lg:px-6 py-3 lg:py-4 mb-4 lg:mb-6">
                <h1 class="text-lg lg:text-xl font-bold flex items-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 lg:h-6 lg:w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Customer Dashboard
                </h1>
            </div>

            <!-- Success Message -->
            <div v-if="barberSuccessMessage" class="mb-4 lg:mb-6 px-3 lg:px-4 py-2 bg-green-100 text-green-800 rounded text-center font-semibold shadow text-sm lg:text-base">{{ barberSuccessMessage }}</div>

            <!-- Four summary cards -->
            <div class="w-full px-0 lg:px-4 pt-4 lg:pt-8 pb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-4">
                    <!-- Next Appointment Card -->
                    <div class="bg-white shadow-lg border border-gray-300 rounded p-4 lg:p-6 w-full flex flex-col h-52 lg:h-64 transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-base lg:text-lg font-semibold mb-3 lg:mb-4 flex items-center gap-2 text-gray-600">
                            <div class="h-5 w-5 lg:h-6 lg:w-6 bg-orange-100 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 lg:w-4 lg:h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <span class="text-sm lg:text-base">Next Appointment</span>
                        </h3>
                        <div v-if="nextAppointment" class="flex flex-col items-center justify-center h-full text-center">
                            <img
                                v-if="nextAppointment.barber_photo_url"
                                :src="nextAppointment.barber_photo_url"
                                alt="Barber photo"
                                class="w-12 h-12 lg:w-16 lg:h-16 rounded-full object-cover mb-2 lg:mb-3 border"
                            />
                            <div v-else class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-gray-200 flex items-center justify-center mb-2 lg:mb-3 text-gray-500 text-lg lg:text-xl font-bold">
                                <span>{{ nextAppointment.barber_name.charAt(0) }}</span>
                            </div>
                            <div class="text-sm lg:text-lg font-semibold text-gray-600 mb-1">{{ nextAppointment.barber_name }}</div>
                            <div class="text-xs lg:text-sm text-gray-500 mb-1 flex items-center justify-center gap-1 lg:gap-2">
                                <span class="hidden sm:inline">Date:</span> <span class="font-semibold text-gray-500">{{ formatDate(nextAppointment.date) }}</span>
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 animate-pulse text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div class="text-xs lg:text-sm text-gray-500 flex items-center justify-center mb-2">
                                <span class="hidden sm:inline">Time:</span> <span class="font-semibold text-gray-500 ml-1">{{ formatTime(nextAppointment.time) }}</span>
                            </div>
                            <template v-if="nextAppointment.status !== 'checked_in' && !nextAppointment.checked_in_at">
                                <button
                                    @click="handleCheckIn"
                                    :disabled="checkingIn"
                                    class="px-2 py-1 bg-gradient-to-r from-green-500 to-green-600 text-white rounded font-medium text-xs hover:from-green-600 hover:to-green-700 disabled:opacity-50 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-1"
                                >
                                    <svg v-if="!checkingIn" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <svg v-else class="animate-spin w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ checkingIn ? 'Checking...' : 'Check In' }}
                                </button>
                            </template>
                            <template v-else>
                                <span class="text-xs lg:text-sm text-green-600 font-semibold">Checked in!</span>
                            </template>
                            <div v-if="checkInMessage && (nextAppointment.status !== 'checked_in' && !nextAppointment.checked_in_at)" class="mt-1 lg:mt-2 text-xs lg:text-sm text-green-600">{{ checkInMessage }}</div>
                        </div>
                        <div v-else class="flex-1 flex items-center justify-center text-sm lg:text-base text-gray-400">No upcoming appointments.</div>
                    </div>

                    <!-- Favourite Barber Card -->
                    <div class="bg-white shadow-lg border border-gray-300 rounded p-4 lg:p-6 w-full flex flex-col h-52 lg:h-64 transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-base lg:text-lg font-semibold mb-3 lg:mb-4 text-gray-600 flex items-center gap-2">
                            <div class="h-5 w-5 lg:h-6 lg:w-6 bg-blue-100 rounded-full flex items-center justify-center">
                                <UserIcon class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600" />
                            </div>
                            <span class="text-sm lg:text-base">Favourite Barber</span>
                        </h3>
                        <div v-if="favouriteBarber" class="flex flex-col items-center justify-center h-full text-center">
                            <img
                                v-if="favouriteBarber.barber_photo_url"
                                :src="favouriteBarber.barber_photo_url"
                                alt="Barber photo"
                                class="w-12 h-12 lg:w-16 lg:h-16 rounded-full object-cover mb-2 lg:mb-3 border"
                            />
                            <div v-else class="w-12 h-12 lg:w-16 lg:h-16 rounded-full bg-gray-200 flex items-center justify-center mb-2 lg:mb-3 text-gray-500 text-lg lg:text-xl font-bold">
                                <span>{{ favouriteBarber.barber_name.charAt(0) }}</span>
                            </div>
                            <div class="text-sm lg:text-lg font-semibold text-gray-600 mb-1">{{ favouriteBarber.barber_name }}</div>
                            <div class="text-xs lg:text-sm text-gray-500 mb-1">Bookings: <span class="font-semibold text-gray-600">{{ favouriteBarber.bookings_count }}</span></div>
                            <div class="text-xs lg:text-sm text-gray-500">Avg. Rating: <span class="font-semibold text-gray-600">{{ favouriteBarber.avg_rating }}</span>
                                <span v-if="favouriteBarber.avg_rating">&nbsp;&#11088;</span>
                            </div>
                        </div>
                        <div v-else class="flex-1 flex items-center justify-center text-sm lg:text-base text-gray-400">No favourite barber yet.</div>
                    </div>

                    <!-- Bookings per Barber Card -->
                    <div class="bg-white shadow-lg border border-gray-300 rounded p-4 lg:p-6 w-full flex flex-col h-52 lg:h-64 transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-base lg:text-lg font-semibold mb-2 lg:mb-4 text-gray-600 flex items-center gap-2">
                            <div class="h-5 w-5 lg:h-6 lg:w-6 bg-purple-100 rounded-full flex items-center justify-center">
                                <ChartPieIcon class="w-3 h-3 lg:w-4 lg:h-4 text-purple-600" />
                            </div>
                            <span class="text-sm lg:text-base">Bookings per Barber</span>
                        </h3>
                        <div class="flex-1 flex flex-col items-center justify-center w-full">
                            <!-- Chart Container -->
                            <div class="flex justify-center mb-2 lg:mb-3">
                                <DoughnutChart :data="doughnutData3" :options="doughnutOptions" style="max-width:80px; height:80px;" class="lg:max-w-[100px] lg:h-24" />
                            </div>
                            <!-- Legend below chart -->
                            <div v-if="doughnutData3.labels.length" class="w-full">
                                <ul class="text-xs text-gray-700 text-center space-y-0.5">
                                    <li v-for="(label, idx) in doughnutData3.labels" :key="label" class="flex items-center justify-center">
                                        <div
                                            class="w-2 h-2 rounded-full mr-2 flex-shrink-0"
                                            :style="{ backgroundColor: doughnutData3.datasets[0].backgroundColor[idx] }"
                                        ></div>
                                        <span class="font-medium">{{ label }}: {{ doughnutData3.datasets[0].data[idx] }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Spending Card (Hidden on mobile) -->
                    <div class="hidden lg:flex bg-white shadow-lg border border-gray-300 rounded p-4 lg:p-6 w-full flex-col h-52 lg:h-64 transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-base lg:text-lg font-semibold mb-3 lg:mb-4 text-gray-600 flex items-center gap-2">
                            <div class="h-5 w-5 lg:h-6 lg:w-6 bg-green-100 rounded-full flex items-center justify-center">
                                <CurrencyDollarIcon class="w-3 h-3 lg:w-4 lg:h-4 text-green-600" />
                            </div>
                            <span class="text-sm lg:text-base">Monthly Spending</span>
                        </h3>
                        <div class="flex-1 flex items-center justify-center">
                            <BarChart :data="barData" :options="barOptions" style="max-width:140px; height:120px;" class="lg:max-w-[160px] lg:h-36" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two-column layout for Become a Barber and Appointments (Hidden on mobile) -->
            <div class="hidden lg:flex max-w-5xl mx-auto py-6 lg:py-12 flex-col lg:flex-row gap-4 lg:gap-8">
                <!-- Left: Become a Barber card -->
                <div class="w-full lg:w-1/3 flex-shrink-0">
                    <div class="bg-white shadow-lg border border-gray-300 rounded p-4 lg:p-6 h-full flex flex-col items-center min-h-[250px] lg:min-h-[300px] transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <img src="/images/services/become_a_barber.jpg" alt="Become a Barber" class="w-full object-contain mb-2 bg-white" />
                        <h3 class="text-base lg:text-lg font-semibold mb-0 mt-2 lg:mt-4 flex items-center gap-2 text-gray-600">
                            <div class="h-6 w-6 lg:h-8 lg:w-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <ScissorsIcon class="h-4 w-4 lg:h-5 lg:w-5 text-orange-600" />
                            </div>
                            <span class="text-sm lg:text-base">Become a Barber</span>
                        </h3>
                        <div class="flex-1 flex flex-col items-center">
                            <p class="text-sm lg:text-base text-gray-600 mb-3 text-center mt-0">Want to offer your services? Register as a barber and join our team!</p>
                            <button @click="openBarberModal" class="inline-block px-4 lg:px-6 py-2 bg-gray-800 text-white rounded font-semibold hover:bg-gray-200 hover:text-gray-900 transition-all text-center mt-2 lg:mt-4 text-sm lg:text-base">Register as Barber</button>
                        </div>
                    </div>
                    <BarberRegisterModal :show="showBarberModal" @close="closeBarberModal" @success="handleBarberSuccess" />
                </div>

                <!-- Right: Appointments -->
                <div class="w-full lg:w-2/3">
                    <div class="space-y-4 bg-white shadow-lg border border-gray-300 rounded p-4 lg:p-6">
                        <h2 class="text-base lg:text-lg font-semibold text-gray-600 flex items-center gap-2">
                            <div class="h-5 w-5 lg:h-6 lg:w-6 bg-blue-100 rounded-full flex items-center justify-center">
                                <CalendarIcon class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600" />
                            </div>
                            Your Appointments
                        </h2>
                        <div v-if="bookings.length" class="grid gap-3 lg:gap-4">
                            <div v-for="booking in bookings" :key="booking.id" class="bg-gray-100 p-3 lg:p-4 rounded-xl">
                                <p class="font-semibold text-gray-600 text-sm lg:text-base">Service: {{ booking.service_name || booking.service?.name || 'N/A' }}</p>
                                <p class="text-xs lg:text-sm text-gray-500">
                                    Barber: {{ booking.barber?.user?.name || 'N/A' }}<br />
                                    {{ booking.booking_time ? new Date(booking.booking_time).toLocaleString() : 'N/A' }}
                                </p>
                                <span class="text-xs uppercase px-2 py-1 rounded font-medium" :class="getStatusClass(booking.status)">
                                    {{ booking.status || 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-sm lg:text-base text-gray-500">No appointments yet.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Quick Book Appointment Button (Fixed Position) -->
        <div class="lg:hidden fixed bottom-6 right-4 z-50">
            <Link :href="route('booking.create')"
                  class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2 font-semibold text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Book Now
            </Link>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { defineProps, ref, onMounted } from 'vue'
import { Doughnut, Bar } from 'vue-chartjs'
import { Chart, ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import axios from 'axios'
import { ScissorsIcon, UserIcon, ChartPieIcon, CurrencyDollarIcon, CalendarIcon } from '@heroicons/vue/24/outline'
import BarberRegisterModal from '@/Components/BarberRegisterModal.vue'

Chart.register(ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const DoughnutChart = Doughnut
const BarChart = Bar

const doughnutData1 = {
  labels: ['A', 'B', 'C'],
  datasets: [{
    data: [30, 50, 20],
    backgroundColor: ['#fbbf24', '#3b82f6', '#10b981'],
    borderWidth: 0
  }]
}
const doughnutData3 = ref({
  labels: [],
  datasets: [{
    data: [],
    backgroundColor: ['#10b981', '#f59e42', '#3b82f6', '#fbbf24', '#6366f1', '#f87171', '#e5e7eb'],
    borderWidth: 0
  }]
})
const doughnutOptions = {
  cutout: '70%',
  plugins: { legend: { display: false } },
  responsive: true,
  maintainAspectRatio: false
}

const barData = ref({
  labels: [],
  datasets: [{
    label: 'Monthly Spending (£)',
    data: [],
    backgroundColor: '#3b82f6',
    borderRadius: 6,
    barPercentage: 0.7,
    categoryPercentage: 0.7
  }]
})

const barOptions = {
  plugins: { legend: { display: false } },
  responsive: true,
  maintainAspectRatio: false,
  layout: { padding: 10 },
  scales: {
    x: {
      grid: { display: false },
      ticks: { font: { size: 14 } }
    },
    y: {
      grid: { display: false },
      beginAtZero: true,
      ticks: { font: { size: 14 } }
    }
  }
}

const favouriteBarber = ref(null)
const nextAppointment = ref(null)
const checkingIn = ref(false)
const checkInMessage = ref("")
const showBarberModal = ref(false)
const barberSuccessMessage = ref('')

function getMonthName(monthStr) {
  const monthNum = parseInt(monthStr.split('-')[1], 10) - 1;
  return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][monthNum];
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString(undefined, { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatTime(timeStr) {
  if (!timeStr) return '';
  const d = new Date(timeStr);
  return d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', hour12: true });
}

async function handleCheckIn() {
  if (!nextAppointment.value) return
  checkingIn.value = true
  checkInMessage.value = ""
  try {
    const response = await axios.post(`/booking/${nextAppointment.value.id}/check-in`)
    nextAppointment.value = {
      ...nextAppointment.value,
      ...response.data.booking
    }
    checkInMessage.value = "Checked in successfully!"
  } catch (error) {
    checkInMessage.value = error.response?.data?.message || "Check-in failed."
  } finally {
    checkingIn.value = false
  }
}

function openBarberModal() {
  showBarberModal.value = true
}
function closeBarberModal() {
  showBarberModal.value = false
}
function handleBarberSuccess() {
  barberSuccessMessage.value = 'Your application has been submitted and is pending admin approval.'
  showBarberModal.value = false
  setTimeout(() => { barberSuccessMessage.value = '' }, 5000)
}

onMounted(async () => {
  try {
    const response = await axios.get('/customer/stats/monthly-spending')
    const data = response.data
    barData.value = {
      labels: data.map(item => getMonthName(item.month)),
      datasets: [{
        label: 'Monthly Spending (£)',
        data: data.map(item => parseFloat(item.total)),
        backgroundColor: '#3b82f6',
        borderRadius: 6,
        barPercentage: 0.7,
        categoryPercentage: 0.7
      }]
    }
  } catch (error) {
    // fallback to empty chart
    barData.value = {
      labels: [],
      datasets: [{
        label: 'Monthly Spending (£)',
        data: [],
        backgroundColor: '#3b82f6',
        borderRadius: 6,
        barPercentage: 0.7,
        categoryPercentage: 0.7
      }]
    }
  }

  // Fetch barber booking distribution for doughnut 3
  try {
    const response = await axios.get('/customer/stats/barber-booking-distribution')
    const data = response.data
    doughnutData3.value = {
      labels: data.map(item => item.name),
      datasets: [{
        data: data.map(item => item.count),
        backgroundColor: ['#10b981', '#f59e42', '#3b82f6', '#fbbf24', '#6366f1', '#f87171', '#e5e7eb'],
        borderWidth: 0
      }]
    }
  } catch (error) {
    doughnutData3.value = {
      labels: [],
      datasets: [{
        data: [],
        backgroundColor: ['#10b981', '#f59e42', '#3b82f6', '#fbbf24', '#6366f1', '#f87171', '#e5e7eb'],
        borderWidth: 0
      }]
    }
  }

  // Fetch favourite barber
  try {
    const response = await axios.get('/customer/stats/favourite-barber')
    favouriteBarber.value = response.data.favourite
  } catch (error) {
    favouriteBarber.value = null
  }

  // Fetch next appointment
  try {
    const response = await axios.get('/customer/stats/next-appointment')
    nextAppointment.value = response.data.appointment
  } catch (error) {
    nextAppointment.value = null
  }
})

function getStatusClass(status) {
  switch (status?.toLowerCase()) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'confirmed':
    case 'booked':
      return 'bg-blue-100 text-blue-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    case 'checked_in':
      return 'bg-purple-100 text-purple-800'
    case 'in_progress':
      return 'bg-orange-100 text-orange-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

defineProps({
    bookings: {
        type: Array,
        default: () => []
    }
})
</script>
