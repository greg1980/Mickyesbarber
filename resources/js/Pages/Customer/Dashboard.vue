<template>
    <Head title="Customer Dashboard" />

    <SidebarLayout>
        <div class="min-h-screen bg-gray-50">
            <!-- Four summary cards: full width, single line on large screens -->
            <div class="w-full px-4 pt-8 pb-4">
                <h1 class="text-xl font-bold mb-6">Customer Dashboard</h1>
                <div v-if="barberSuccessMessage" class="mb-6 px-4 py-2 bg-green-100 text-green-800 rounded text-center font-semibold shadow">{{ barberSuccessMessage }}</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white shadow-lg rounded p-6 w-full flex flex-col h-64 transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            Next Appointment
                            <span v-if="nextAppointment">
                                <svg class="w-6 h-6 animate-pulse text-green-600 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </span>
                            <span v-else>
                                <svg class="w-6 h-6 text-gray-400 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </span>
                        </h3>
                        <div v-if="nextAppointment" class="flex flex-col items-center justify-center h-full text-center">
                            <img
                                v-if="nextAppointment.barber_photo_url"
                                :src="nextAppointment.barber_photo_url"
                                alt="Barber photo"
                                class="w-16 h-16 rounded-full object-cover mb-3 border"
                            />
                            <div v-else class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mb-3 text-gray-500 text-xl font-bold">
                                <span>{{ nextAppointment.barber_name.charAt(0) }}</span>
                            </div>
                            <div class="text-2xl font-extrabold text-gray-800 mb-1">{{ nextAppointment.barber_name }}</div>
                            <div class="text-gray-500 mb-1">Date: <span class="font-semibold text-gray-500">{{ formatDate(nextAppointment.date) }}</span></div>
                            <div class="text-gray-500 flex items-center justify-center">
                                Time: <span class="font-semibold text-gray-500 ml-1">{{ formatTime(nextAppointment.time) }}</span>
                                <template v-if="nextAppointment.status !== 'checked_in' && !nextAppointment.checked_in_at">
                                    <button
                                        @click="handleCheckIn"
                                        :disabled="checkingIn"
                                        class="ml-3 px-3 py-1 bg-gray-800 text-white rounded font-semibold text-sm hover:bg-gray-200 hover:text-gray-900 disabled:opacity-50 transition-all"
                                        style="min-width: 80px; height: 32px;"
                                    >
                                        {{ checkingIn ? 'Checking...' : 'Check In' }}
                                    </button>
                                </template>
                                <template v-else>
                                    <span class="ml-3 text-green-600 font-semibold">Checked in!</span>
                                </template>
                            </div>
                            <div v-if="checkInMessage && (nextAppointment.status !== 'checked_in' && !nextAppointment.checked_in_at)" class="mt-2 text-green-600">{{ checkInMessage }}</div>
                        </div>
                        <div v-else class="text-gray-400">No upcoming appointments.</div>
                    </div>
                    <div class="bg-white shadow-lg rounded p-6 w-full flex flex-col h-64 transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-lg font-semibold mb-4">Favourite Barber</h3>
                        <div v-if="favouriteBarber" class="flex flex-col items-center justify-center h-full text-center">
                            <img
                                v-if="favouriteBarber.barber_photo_url"
                                :src="favouriteBarber.barber_photo_url"
                                alt="Barber photo"
                                class="w-16 h-16 rounded-full object-cover mb-3 border"
                            />
                            <div v-else class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mb-3 text-gray-500 text-xl font-bold">
                                <span>{{ favouriteBarber.barber_name.charAt(0) }}</span>
                            </div>
                            <div class="text-2xl font-extrabold text-gray-800 mb-1">{{ favouriteBarber.barber_name }}</div>
                            <div class="text-gray-500 mb-1">Bookings: <span class="font-semibold text-gray-800">{{ favouriteBarber.bookings_count }}</span></div>
                            <div class="text-gray-500">Avg. Rating: <span class="font-semibold text-gray-800">{{ favouriteBarber.avg_rating }}</span>
                                <span v-if="favouriteBarber.avg_rating">&#11088;</span>
                            </div>
                        </div>
                        <div v-else class="text-gray-400">No favourite barber yet.</div>
                    </div>
                    <div class="bg-white shadow-lg rounded p-6 w-full flex flex-col h-64 transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-lg font-semibold mb-4">Bookings per Barber</h3>
                        <div class="flex flex-row items-center justify-center w-full">
                            <DoughnutChart :data="doughnutData3" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                            <ul v-if="doughnutData3.labels.length" class="ml-8 text-sm text-gray-700 text-left">
                                <li v-for="(label, idx) in doughnutData3.labels" :key="label">
                                    <span class="font-semibold">{{ label }}</span> — {{ doughnutData3.datasets[0].data[idx] }} bookings
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="bg-white shadow-lg rounded p-6 w-full flex flex-col h-64 transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-lg font-semibold mb-4">Monthly Spending</h3>
                        <BarChart :data="barData" :options="barOptions" style="max-width:180px; height:128px;" />
                    </div>
                </div>
            </div>

            <!-- Two-column layout for Become a Barber and Appointments -->
            <div class="max-w-5xl mx-auto py-12 flex flex-col lg:flex-row gap-8">
                <!-- Left: Become a Barber card -->
                <div class="w-full lg:w-1/3 flex-shrink-0">
                    <div class="bg-white shadow-lg rounded p-6 h-full flex flex-col items-center min-h-[300px] transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <img src="/images/services/become_a_barber.jpg" alt="Become a Barber" class="w-full object-contain mb-2 bg-white" />
                        <h3 class="text-lg font-semibold mb-0 mt-4 flex items-center gap-2">
                            <ScissorsIcon class="h-7 w-7 text-gray-400" />
                            Become a Barber
                        </h3>
                        <div class="flex-1 flex flex-col items-center">
                            <p class="text-gray-600 mb-3 text-center mt-0">Want to offer your services? Register as a barber and join our team!</p>
                            <button @click="openBarberModal" class="inline-block px-6 py-2 bg-gray-800 text-white rounded font-semibold hover:bg-gray-200 hover:text-gray-900 transition-all text-center mt-4">Register as Barber</button>
                        </div>
                    </div>
                    <BarberRegisterModal :show="showBarberModal" @close="closeBarberModal" @success="handleBarberSuccess" />
                </div>
                <!-- Right: Appointments -->
                <div class="w-full lg:w-2/3">
                    <div class="space-y-4 bg-white shadow-lg rounded p-6">
                        <h2 class="text-lg font-semibold">Your Appointments</h2>
                        <div v-if="bookings.length" class="grid gap-4">
                            <div v-for="booking in bookings" :key="booking.id" class="bg-gray-100 p-4 rounded-xl">
                                <p class="font-semibold">Service: {{ booking.service_name || booking.service?.name || 'N/A' }}</p>
                                <p class="text-sm text-gray-500">
                                    Barber: {{ booking.barber?.user?.name || 'N/A' }}<br />
                                    {{ booking.booking_time ? new Date(booking.booking_time).toLocaleString() : 'N/A' }}
                                </p>
                                <span class="text-xs uppercase px-2 py-1 rounded bg-gray-200">
                                    {{ booking.status || 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <p v-else>No appointments yet.</p>
                    </div>
                </div>
            </div>
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
import { ScissorsIcon } from '@heroicons/vue/24/outline'
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

defineProps({
    bookings: {
        type: Array,
        default: () => []
    }
})
</script>
