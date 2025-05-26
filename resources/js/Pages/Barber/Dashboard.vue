<script setup>
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { defineProps } from 'vue'
import { Doughnut, Bar } from 'vue-chartjs'
import { Chart, ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'

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
const doughnutData2 = {
  labels: ['X', 'Y', 'Z'],
  datasets: [{
    data: [10, 60, 30],
    backgroundColor: ['#f87171', '#6366f1', '#fbbf24'],
    borderWidth: 0
  }]
}
const doughnutData3 = {
  labels: ['P', 'Q', 'R'],
  datasets: [{
    data: [40, 40, 20],
    backgroundColor: ['#10b981', '#f59e42', '#3b82f6'],
    borderWidth: 0
  }]
}
const doughnutOptions = {
  cutout: '70%',
  plugins: { legend: { display: false } },
  responsive: true,
  maintainAspectRatio: false
}

const barData = {
  labels: [],
  datasets: [{
    label: 'Monthly Rating',
    data: [],
    backgroundColor: '#3b82f6',
    borderRadius: 6,
    barPercentage: 0.7,
    categoryPercentage: 0.7
  }]
}
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

const weekDays = [
  'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
]

const props = defineProps({
  bookings: {
    type: Array,
    default: () => []
  },
  schedules: {
    type: Array,
    default: () => []
  }
})

const showModal = ref(false)
const successMessage = ref('')

const form = ref({})

// Map schedules to form
function initializeForm() {
  weekDays.forEach(day => {
    const found = props.schedules.find(s => s.day_of_week === day)
    form.value[day] = {
      is_available: found ? found.is_available : false,
      start_time: found ? found.start_time?.slice(0,5) : '09:00',
      end_time: found ? found.end_time?.slice(0,5) : '17:00',
    }
  })
}

watch(() => props.schedules, initializeForm, { immediate: true })

const availabilityMap = computed(() => {
  const map = {}
  weekDays.forEach(day => {
    map[day] = form.value[day] || { is_available: false, start_time: '', end_time: '' }
  })
  return map
})

// Sanitize time values for all days
function sanitizeTime(val) {
  if (!val) return '';
  if (typeof val !== 'string') val = String(val);
  val = val.trim();
  // Only allow HH:mm
  if (/^\d{2}:\d{2}$/.test(val)) return val;
  // Convert H:mm or HH.mm
  if (/^\d{1,2}[.:]\d{2}$/.test(val)) {
    let [h, m] = val.split(/[.:]/);
    return h.padStart(2, '0') + ':' + m.padStart(2, '0');
  }
  return '';
}

watch(form, (newForm) => {
  weekDays.forEach(day => {
    newForm[day].start_time = sanitizeTime(newForm[day].start_time);
    newForm[day].end_time = sanitizeTime(newForm[day].end_time);
  });
}, { deep: true });

function submitAvailability() {
  const schedules = weekDays.map(day => ({
    day_of_week: day,
    is_available: form.value[day].is_available,
    start_time: form.value[day].start_time,
    end_time: form.value[day].end_time
  }));
  router.post('/barber/update-schedule', { schedules }, {
    onSuccess: () => {
      showModal.value = false
      successMessage.value = 'Availability updated!'
      setTimeout(() => successMessage.value = '', 3000)
    }
  })
}

const fetchMonthlyRatings = async () => {
  try {
    const response = await axios.get('/api/barber/monthly-ratings');
    barData.labels = response.data.map(item => item.month);
    barData.datasets[0].data = response.data.map(item => item.rating);
  } catch (error) {
    console.error('Error fetching monthly ratings:', error);
  }
};

fetchMonthlyRatings();
</script>

<template>
    <Head title="Barber Dashboard" />

    <SidebarLayout>
        <div class="min-h-screen bg-gray-50">
            <!-- Four summary cards: full width, single line on large screens -->
            <div class="w-full px-4 pt-8 pb-4">
                <h1 class="text-xl font-bold mb-6">Barber Dashboard</h1>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white shadow-lg p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Doughnut 1</h3>
                        <DoughnutChart :data="doughnutData1" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                    </div>
                    <div class="bg-white shadow-lg p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Doughnut 2</h3>
                        <DoughnutChart :data="doughnutData2" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                    </div>
                    <div class="bg-white shadow-lg p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Doughnut 3</h3>
                        <DoughnutChart :data="doughnutData3" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                    </div>
                    <div class="bg-white shadow-lg p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Bar Chart</h3>
                        <BarChart :data="barData" :options="barOptions" style="max-width:180px; height:128px;" />
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                  <!-- Availability Card -->
                  <div class="bg-white shadow-lg p-0 flex flex-col items-center">
                    <div class="w-full bg-yellow-400 flex flex-col items-center pt-6 pb-4">
                      <img :src="$page.props.auth.user.profile_photo_url" class="w-16 h-16 rounded-full object-cover mb-2 border-4 border-white shadow" alt="Profile" />
                      <div class="font-semibold text-lg mb-1">{{$page.props.auth.user.name}}</div>
                      <div class="text-xs text-gray-700 mb-2">Barber</div>
                    </div>
                    <div class="w-full p-6">
                      <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold">Availability</span>
                        <button @click="showModal = true" class="px-3 py-1 text-xs font-medium border border-green-600 text-green-600 bg-white rounded hover:bg-green-600 hover:text-white transition-colors duration-200">Edit</button>
                      </div>
                      <ul class="text-sm text-gray-700">
                        <li v-for="day in weekDays" :key="day" class="flex justify-between">
                          <span :class="[
                            ['monday','tuesday','wednesday','thursday','friday'].includes(day) ? 'text-blue-600' : '',
                            ['saturday','sunday'].includes(day) ? 'text-pink-500' : '',
                            'capitalize'
                          ]">{{ day }}</span>
                          <span v-if="availabilityMap[day] && availabilityMap[day].is_available">
                            {{ availabilityMap[day].start_time }} - {{ availabilityMap[day].end_time }}
                          </span>
                          <span v-else class="text-gray-400">Unavailable</span>
                        </li>
                      </ul>
                    </div>
                    <div v-if="successMessage" class="text-green-600 text-xs mt-2">{{ successMessage }}</div>
                  </div>
                  <!-- End Availability Card -->
                  <div class="lg:col-span-2">
                    <div class="space-y-4 bg-white shadow-lg p-6">
                        <h2 class="text-lg font-semibold">Upcoming Appointments</h2>
                        <div v-if="bookings.length" class="grid gap-4">
                            <div v-for="booking in bookings" :key="booking.id" class="bg-gray-100 p-4">
                                <p class="font-semibold">Service: {{ booking.service_name || booking.service?.name || 'N/A' }}</p>
                                <p class="text-sm text-gray-500">
                                    Client: {{ booking.user?.name || 'N/A' }}<br />
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

            <!-- Availability Modal -->
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
              <div class="bg-white shadow-xl w-full max-w-lg p-0 relative">
                <!-- Modal Header -->
                <div class="flex items-center justify-between bg-gray-100 px-6 py-2 border-b border-gray-200">
                  <h2 class="text-lg font-semibold">Set Weekly Availability</h2>
                  <button @click="showModal = false" class="text-gray-400 hover:text-gray-700 hover:bg-gray-200 rounded-full p-1 transition-transform duration-150 hover:scale-110 focus:outline-none">
                    <span class="text-xl">&times;</span>
                  </button>
                </div>
                <!-- Modal Body -->
                <form @submit.prevent="submitAvailability" class="px-6 py-4">
                  <div v-for="day in weekDays" :key="day" class="mb-4 flex items-center justify-between">
                    <span class="capitalize w-20">{{ day }}</span>
                    <!-- Toggle Switch -->
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="form[day].is_available" class="sr-only peer">
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-400 rounded-full peer peer-checked:bg-green-500 transition-all duration-200"></div>
                      <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow transition-all duration-200 peer-checked:translate-x-5"></div>
                    </label>
                    <input type="time" v-model="form[day].start_time" :disabled="!form[day].is_available" class="border rounded px-2 py-1 text-xs" step="1800" />
                    <span>-</span>
                    <input type="time" v-model="form[day].end_time" :disabled="!form[day].is_available" class="border rounded px-2 py-1 text-xs" step="1800" />
                  </div>
                  <div class="flex justify-end mt-6">
                    <button type="button" @click="showModal = false" class="px-4 py-2 mr-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded border border-green-600 text-green-600 font-semibold bg-white hover:bg-green-600 hover:text-white transition-colors duration-200">Save</button>
                  </div>
                </form>
              </div>
            </div>
            <!-- End Modal -->
        </div>
    </SidebarLayout>
</template>
