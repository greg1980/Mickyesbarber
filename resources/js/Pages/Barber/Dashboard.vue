<script setup>
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { defineProps } from 'vue'
import { Doughnut, Bar } from 'vue-chartjs'
import { Chart, ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { ref, computed, watch, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'

Chart.register(ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const DoughnutChart = Doughnut
const BarChart = Bar

const todaysAppointmentsCount = ref(0)
const todaysCompletedAppointmentsCount = ref(0)
const monthlyCompletedAppointmentsCount = ref(0)

const fetchTodaysAppointmentsCount = async () => {
  try {
    const response = await axios.get('/barber/todays-appointments-count', { withCredentials: true });
    todaysAppointmentsCount.value = response.data.count;
  } catch (error) {
    todaysAppointmentsCount.value = 0;
  }
}

const fetchTodaysCompletedAppointmentsCount = async () => {
  try {
    const response = await axios.get('/barber/todays-completed-appointments-count', { withCredentials: true });
    todaysCompletedAppointmentsCount.value = response.data.count;
  } catch (error) {
    todaysCompletedAppointmentsCount.value = 0;
  }
}

const fetchMonthlyCompletedAppointmentsCount = async () => {
  try {
    const response = await axios.get('/barber/monthly-completed-appointments-count', { withCredentials: true });
    monthlyCompletedAppointmentsCount.value = response.data.count;
  } catch (error) {
    monthlyCompletedAppointmentsCount.value = 0;
  }
}

const doughnutData1 = computed(() => {
  if (todaysAppointmentsCount.value === 0) {
    return {
      labels: ['No Appointments'],
      datasets: [{
        data: [1],
        backgroundColor: ['#e5e7eb'],
        borderWidth: 0
      }]
    }
  }
  return {
    labels: ['Today'],
    datasets: [{
      data: [todaysAppointmentsCount.value],
      backgroundColor: ['#fbbf24'],
      borderWidth: 0
    }]
  }
})

const doughnutData2 = computed(() => {
  if (todaysCompletedAppointmentsCount.value === 0) {
    return {
      labels: ['No Completed'],
      datasets: [{
        data: [1],
        backgroundColor: ['#e5e7eb'],
        borderWidth: 0
      }]
    }
  }
  return {
    labels: ['Completed'],
    datasets: [{
      data: [todaysCompletedAppointmentsCount.value],
      backgroundColor: ['#3b82f6'],
      borderWidth: 0
    }]
  }
})

const doughnutData3 = computed(() => {
  if (monthlyCompletedAppointmentsCount.value === 0) {
    return {
      labels: ['No Completed'],
      datasets: [{
        data: [1],
        backgroundColor: ['#e5e7eb'],
        borderWidth: 0
      }]
    }
  }
  return {
    labels: ['Completed'],
    datasets: [{
      data: [monthlyCompletedAppointmentsCount.value],
      backgroundColor: ['#10b981'],
      borderWidth: 0
    }]
  }
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
    label: 'Monthly Rating',
    data: [],
    backgroundColor: '#3b82f6',
    borderRadius: 6,
    barPercentage: 0.7,
    categoryPercentage: 0.7
  }]
})

const barOptions = {
  plugins: {
    legend: { display: false },
    tooltip: {
      callbacks: {
        label: function(context) {
          return `Rating: ${context.raw?.toFixed(2) || 'N/A'}`;
        }
      }
    }
  },
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
      max: 5,
      ticks: {
        font: { size: 14 },
        stepSize: 1
      }
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
    const response = await axios.get('/barber/monthly-ratings', {
      withCredentials: true,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    console.log('API Response:', response.data); // Debug log

    if (response.data && Array.isArray(response.data)) {
      barData.value = {
        labels: response.data.map(item => item.month),
        datasets: [{
          label: 'Monthly Rating',
          data: response.data.map(item => item.rating),
          backgroundColor: '#3b82f6',
          borderRadius: 6,
          barPercentage: 0.7,
          categoryPercentage: 0.7
        }]
      };
    } else {
      console.error('Invalid data format received:', response.data);
      // Set default data if the response is invalid
      barData.value = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Monthly Rating',
          data: Array(12).fill(0),
          backgroundColor: '#3b82f6',
          borderRadius: 6,
          barPercentage: 0.7,
          categoryPercentage: 0.7
        }]
      };
    }
  } catch (error) {
    console.error('Error fetching monthly ratings:', error.response?.data || error.message);
    // Set default data on error
    barData.value = {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Monthly Rating',
        data: Array(12).fill(0),
        backgroundColor: '#3b82f6',
        borderRadius: 6,
        barPercentage: 0.7,
        categoryPercentage: 0.7
      }]
    };
  }
};

// Call fetchMonthlyRatings when component is mounted
onMounted(() => {
  fetchTodaysAppointmentsCount();
  fetchTodaysCompletedAppointmentsCount();
  fetchMonthlyCompletedAppointmentsCount();
  fetchMonthlyRatings();
});
</script>

<template>
    <Head title="Barber Dashboard" />

    <SidebarLayout>
        <div class="min-h-screen bg-gray-50">
            <!-- Four summary cards: full width, single line on large screens -->
            <div class="w-full px-4 pt-8 pb-4">
                <h1 class="text-xl font-bold mb-6">Barber Dashboard</h1>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white shadow-lg p-6 w-full flex flex-col items-center h-64 justify-center relative">
                        <h3 class="text-lg font-semibold mb-2">Today's Appointments</h3>
                        <DoughnutChart :data="doughnutData1" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                        <div
                          class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-gray-700 pointer-events-none"
                          style="top: 40px; left: 0; right: 0; bottom: 0;"
                        >
                          {{ todaysAppointmentsCount }}
                        </div>
                    </div>
                    <div class="bg-white shadow-lg p-6 w-full flex flex-col items-center h-64 justify-center relative">
                        <h3 class="text-lg font-semibold mb-2">Today's Completed</h3>
                        <DoughnutChart :data="doughnutData2" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                        <div
                          class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-gray-700 pointer-events-none"
                          style="top: 40px; left: 0; right: 0; bottom: 0;"
                        >
                          {{ todaysCompletedAppointmentsCount }}
                        </div>
                    </div>
                    <div class="bg-white shadow-lg p-6 w-full flex flex-col items-center h-64 justify-center relative">
                        <h3 class="text-lg font-semibold mb-2">This Month's Completed</h3>
                        <DoughnutChart :data="doughnutData3" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                        <div
                          class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-gray-700 pointer-events-none"
                          style="top: 40px; left: 0; right: 0; bottom: 0;"
                        >
                          {{ monthlyCompletedAppointmentsCount }}
                        </div>
                    </div>
                    <div class="bg-white shadow-lg p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Monthly Ratings</h3>
                        <BarChart :data="barData" :options="barOptions" style="max-width:180px; height:128px;" />
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                  <!-- Left Column: Stack two cards vertically -->
                  <div class="flex flex-col gap-6">
                    <!-- Availability Card -->
                    <div class="bg-white shadow-lg p-0 flex flex-col items-center">
                      <div class="w-full bg-yellow-400 flex flex-col items-center pt-5 pb-3">
                        <img :src="$page.props.auth.user.profile_photo_url" class="w-16 h-16 rounded-full object-cover mb-2 border-4 border-white shadow" alt="Profile" />
                        <div class="font-semibold text-lg mb-1">{{$page.props.auth.user.name}}</div>
                        <div class="text-xs text-gray-700 mb-2">Barber</div>
                      </div>
                      <div class="w-full p-5">
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
                    <!-- Second Generic Card -->
                    <div class="bg-white shadow-lg p-0 flex flex-col items-center">
                      <div class="w-full bg-blue-400 flex flex-col items-center pt-5 pb-3">
                        <div class="font-semibold text-lg mb-1 text-white">Second Card</div>
                        <div class="text-xs text-blue-100 mb-2">Generic Content</div>
                      </div>
                      <div class="w-full p-5">
                        <p class="text-gray-700 text-center">This is a placeholder for your second card. Replace this with your desired content.</p>
                      </div>
                    </div>
                    <!-- End Second Generic Card -->
                  </div>
                  <!-- Right Column: Upcoming Appointments -->
                  <div class="lg:col-span-2">
                    <div class="space-y-4 bg-white shadow-lg p-6">
                        <h2 class="text-lg font-semibold">Upcoming Appointments</h2>
                        <div v-if="bookings.length">
                          <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                              <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    User
                                  </span>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><circle cx="12" cy="16" r="2"/></svg>
                                    Date & Time
                                  </span>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>
                                    Notes
                                  </span>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2l4-4"/></svg>
                                    Payment Status
                                </span>
                                </th>
                              </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                              <tr v-for="booking in bookings" :key="booking.id">
                                <td class="px-4 py-2 flex items-center">
                                  <img :src="booking.user.profile_photo_url" alt="User Image" class="w-8 h-8 rounded-full object-cover mr-2 border" />
                                  <span class="font-medium">{{ booking.user.name }}</span>
                                </td>
                                <td class="px-4 py-2">
                                  <div v-if="booking.booking_time">
                                    <div>
                                      {{
                                        new Date(
                                          booking.booking_time.replace(' ', 'T')
                                        ).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
                                      }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                      {{
                                        new Date(
                                          booking.booking_time.replace(' ', 'T')
                                        ).toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit', hour12: true })
                                      }}
                                    </div>
                            </div>
                                  <span v-else>N/A</span>
                                </td>
                                <td class="px-4 py-2">
                                  <span>{{ booking.notes || '—' }}</span>
                                </td>
                                <td class="px-4 py-2">
                                  <span :class="[
                                    'text-xs px-2 py-1 rounded',
                                    booking.payment_status === 'fully_paid' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800'
                                  ]">
                                    {{ booking.payment_status ? booking.payment_status.replace('_', ' ') : 'N/A' }}
                                  </span>
                                </td>
                              </tr>
                            </tbody>
                          </table>
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
