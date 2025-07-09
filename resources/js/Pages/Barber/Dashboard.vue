<script setup>
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { defineProps } from 'vue'
import { Doughnut, Bar, Line } from 'vue-chartjs'
import { Chart, ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement } from 'chart.js'
import { ref, computed, watch, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import {
  CalendarIcon,
  CheckCircleIcon,
  ChartBarIcon,
  StarIcon,
  ClockIcon,
  CalendarDaysIcon,
  CurrencyPoundIcon,
  ArrowTrendingUpIcon,
  EyeIcon,
  PencilIcon,
  PlusIcon,
  BanknotesIcon,
  UserIcon
} from '@heroicons/vue/24/outline'

Chart.register(ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement)

const DoughnutChart = Doughnut
const BarChart = Bar
const LineChart = Line

const todaysAppointmentsCount = ref(0)
const todaysCompletedAppointmentsCount = ref(0)
const monthlyCompletedAppointmentsCount = ref(0)
const todaysEarnings = ref(0)
const monthlyEarnings = ref(0)

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

// Simulate earnings data (you can replace with real API calls)
const fetchTodaysEarnings = async () => {
  // Simulate earnings calculation: completed appointments * average price
  todaysEarnings.value = todaysCompletedAppointmentsCount.value * 25;
}

const fetchMonthlyEarnings = async () => {
  // Simulate monthly earnings
  monthlyEarnings.value = monthlyCompletedAppointmentsCount.value * 25;
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

// Call fetch functions when component is mounted
onMounted(async () => {
  await fetchTodaysAppointmentsCount();
  await fetchTodaysCompletedAppointmentsCount();
  await fetchMonthlyCompletedAppointmentsCount();
  await fetchTodaysEarnings();
  await fetchMonthlyEarnings();
  fetchMonthlyRatings();
});

// Add computed property for average monthly rating
const averageMonthlyRating = computed(() => {
  if (!barData.value.datasets[0] || !barData.value.datasets[0].data) return 0;
  const ratings = barData.value.datasets[0].data.filter(rating => rating > 0);
  if (ratings.length === 0) return 0;
  const sum = ratings.reduce((acc, rating) => acc + rating, 0);
  return (sum / ratings.length).toFixed(1);
});

// Quick actions
const quickActions = [
  {
    name: 'View Appointments',
    icon: CalendarIcon,
    color: 'blue',
    href: '/barber/appointments'
  },
  {
    name: 'Update Availability',
    icon: ClockIcon,
    color: 'green',
    action: () => showModal.value = true
  },
  {
    name: 'View Bookings',
    icon: EyeIcon,
    color: 'purple',
    href: '/barber/bookings'
  }
]
</script>

<template>
    <Head title="Barber Dashboard">
      <meta name="description" content="Barber dashboard for Mickyes Coiffure. Manage your appointments, clients, and profile." />
      <meta property="og:title" content="Barber Dashboard - Mickyes Coiffure" />
      <meta property="og:description" content="Access your barber dashboard to manage appointments and clients at Mickyes Coiffure." />
      <meta property="og:type" content="website" />
      <meta property="og:url" content="https://mickyes.com/barber/dashboard" />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content="Barber Dashboard - Mickyes Coiffure" />
      <meta name="twitter:description" content="Barber dashboard for managing appointments and clients at Mickyes Coiffure." />
    </Head>

    <SidebarLayout>
        <div class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
            <!-- Enhanced Header Section -->
            <div class="mb-8">
                <!-- Professional Header Banner -->
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-xl p-8 text-white mb-6 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 100 100">
                            <pattern id="barber-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="1"/>
                                <circle cx="18" cy="18" r="1"/>
                                <circle cx="10" cy="10" r="0.5"/>
                            </pattern>
                            <rect width="100" height="100" fill="url(#barber-pattern)"/>
                        </svg>
                    </div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4a4 4 0 118 0v4a6 6 0 01-12 0V7z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-4xl font-bold mb-2">Welcome back, {{ $page.props.auth.user.name }}!</h1>
                                <p class="text-blue-100 text-lg">Professional Barber Dashboard • Mickyes Coiffure</p>
                                <p class="text-blue-200 text-sm mt-1">Manage your appointments, track earnings, and grow your business</p>
                            </div>
                        </div>

                        <!-- Professional Status Badge -->
                        <div class="hidden lg:block">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/30">
                                <div class="text-center">
                                    <div class="text-2xl font-bold">{{ todaysAppointmentsCount + todaysCompletedAppointmentsCount }}</div>
                                    <div class="text-xs text-blue-200 uppercase tracking-wider">Today's Activity</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Bar -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="h-2 w-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-gray-700">Dashboard Active</span>
                        <span class="text-xs text-gray-500">• Last updated just now</span>
                    </div>
                                         <div class="hidden md:flex items-center space-x-3">
                        <div v-for="action in quickActions" :key="action.name"
                             class="group relative">
                            <button v-if="action.action"
                                    @click="action.action"
                                    :class="[
                                        'flex items-center space-x-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1 hover:scale-105 border-2 border-transparent',
                                        action.color === 'blue' ? 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white shadow-blue-200' :
                                        action.color === 'green' ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow-green-200' :
                                        action.color === 'purple' ? 'bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white shadow-purple-200' :
                                        'bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white shadow-gray-200'
                                    ]">
                                <component :is="action.icon" class="w-5 h-5" />
                                <span class="text-sm font-medium">{{ action.name }}</span>
                            </button>
                            <a v-else
                               :href="action.href"
                               :class="[
                                   'flex items-center space-x-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1 hover:scale-105 border-2 border-transparent',
                                   action.color === 'blue' ? 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white shadow-blue-200' :
                                   action.color === 'green' ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow-green-200' :
                                   action.color === 'purple' ? 'bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white shadow-purple-200' :
                                   'bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white shadow-gray-200'
                               ]">
                                <component :is="action.icon" class="w-5 h-5" />
                                <span class="text-sm font-medium">{{ action.name }}</span>
                            </a>
                            <!-- Tooltip -->
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap">
                                {{ action.name }}
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Today's Appointments -->
                <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <!-- Subtle background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider">Today's Appointments</p>
                            <p class="text-4xl font-bold text-blue-600 mb-2">{{ todaysAppointmentsCount }}</p>
                            <p class="text-xs text-gray-500">
                                <span class="inline-flex items-center bg-blue-50 text-blue-700 px-2 py-1 rounded-full">
                                    <ArrowTrendingUpIcon class="w-3 h-3 mr-1" />
                                    Ready to serve
                                </span>
                            </p>
                        </div>
                        <div class="h-14 w-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <CalendarIcon class="h-7 w-7 text-white" />
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="mt-4 bg-gray-100 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full" :style="`width: ${Math.min((todaysAppointmentsCount / 10) * 100, 100)}%`"></div>
                    </div>
                </div>

                <!-- Today's Completed -->
                <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <!-- Subtle background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider">Completed Today</p>
                            <p class="text-4xl font-bold text-green-600 mb-2">{{ todaysCompletedAppointmentsCount }}</p>
                            <p class="text-xs text-gray-500">
                                <span class="inline-flex items-center bg-green-50 text-green-700 px-2 py-1 rounded-full">
                                    <CheckCircleIcon class="w-3 h-3 mr-1" />
                                    Great work!
                                </span>
                            </p>
                        </div>
                        <div class="h-14 w-14 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <CheckCircleIcon class="h-7 w-7 text-white" />
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="mt-4 bg-gray-100 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" :style="`width: ${Math.min((todaysCompletedAppointmentsCount / 8) * 100, 100)}%`"></div>
                    </div>
                </div>

                <!-- Today's Earnings -->
                <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <!-- Subtle background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider">Today's Earnings</p>
                            <p class="text-4xl font-bold text-purple-600 mb-2">£{{ todaysEarnings }}</p>
                            <p class="text-xs text-gray-500">
                                <span class="inline-flex items-center bg-purple-50 text-purple-700 px-2 py-1 rounded-full">
                                    <BanknotesIcon class="w-3 h-3 mr-1" />
                                    From {{ todaysCompletedAppointmentsCount }} cuts
                                </span>
                            </p>
                        </div>
                        <div class="h-14 w-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <CurrencyPoundIcon class="h-7 w-7 text-white" />
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="mt-4 bg-gray-100 rounded-full h-2">
                        <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" :style="`width: ${Math.min((todaysEarnings / 200) * 100, 100)}%`"></div>
                    </div>
                </div>

                <!-- Average Rating -->
                <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer relative overflow-hidden">
                    <!-- Subtle background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider">Average Rating</p>
                            <div class="flex items-center space-x-2 mb-2">
                                <p class="text-4xl font-bold text-yellow-600">{{ averageMonthlyRating }}</p>
                                <div class="flex">
                                    <span v-for="star in 5" :key="star"
                                          :class="star <= Math.round(averageMonthlyRating) ? 'text-yellow-400' : 'text-gray-300'"
                                          class="text-lg">★</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">
                                <span class="inline-flex items-center bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full">
                                    <StarIcon class="w-3 h-3 mr-1" />
                                    Excellent service
                                </span>
                            </p>
                        </div>
                        <div class="h-14 w-14 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <StarIcon class="h-7 w-7 text-white" />
                        </div>
                    </div>

                    <!-- Star rating progress bar -->
                    <div class="mt-4 bg-gray-100 rounded-full h-2">
                        <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 h-2 rounded-full" :style="`width: ${(averageMonthlyRating / 5) * 100}%`"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Barber Profile & Availability -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Profile Header -->
                        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-8 text-center">
                            <img :src="$page.props.auth.user.profile_photo_url"
                                 class="w-20 h-20 rounded-full object-cover mx-auto mb-4 border-4 border-white shadow-lg"
                                 alt="$page.props.auth.user.name ? 'Profile photo of ' + $page.props.auth.user.name : 'Barber profile photo'" />
                            <h3 class="text-xl font-semibold text-white mb-1">{{ $page.props.auth.user.name }}</h3>
                            <p class="text-gray-300 text-sm">Professional Barber</p>
                            <div class="mt-4 flex justify-center space-x-4 text-sm text-gray-300">
                                <div class="text-center">
                                    <div class="font-semibold text-white">{{ monthlyCompletedAppointmentsCount }}</div>
                                    <div>This Month</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-white">£{{ monthlyEarnings }}</div>
                                    <div>Earned</div>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Schedule -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-gray-900 flex items-center">
                                    <ClockIcon class="w-5 h-5 mr-2 text-gray-600" />
                                    Weekly Schedule
                                </h4>
                                <button @click="showModal = true"
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                    <PencilIcon class="w-4 h-4 mr-1" />
                                    Edit
                                </button>
                            </div>
                            <div class="space-y-3">
                                <div v-for="day in weekDays" :key="day"
                                     class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-50">
                                    <span :class="[
                                        'capitalize font-medium',
                                        ['monday','tuesday','wednesday','thursday','friday'].includes(day) ? 'text-blue-600' : 'text-purple-600'
                                    ]">{{ day }}</span>
                                    <span v-if="availabilityMap[day] && availabilityMap[day].is_available"
                                          class="text-sm text-gray-900 font-medium">
                                        {{ availabilityMap[day].start_time }} - {{ availabilityMap[day].end_time }}
                                    </span>
                                    <span v-else class="text-sm text-gray-400">Closed</span>
                                </div>
                            </div>
                            <div v-if="successMessage"
                                 class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-green-700 text-sm font-medium">{{ successMessage }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                                <CalendarIcon class="w-6 h-6 mr-3 text-blue-600" />
                                Upcoming Appointments
                            </h3>
                            <a href="/barber/appointments"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                <EyeIcon class="w-4 h-4 mr-2" />
                                View All
                            </a>
                        </div>

                        <div v-if="bookings.length" class="space-y-4">
                            <div v-for="booking in bookings.slice(0, 5)" :key="booking.id"
                                 class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                <img :src="booking.user.profile_photo_url"
                                     alt="Customer"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm" />
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-gray-900">{{ booking.user.name }}</h4>
                                        <span :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            booking.payment_status === 'fully_paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                        ]">
                                            {{ booking.payment_status ? booking.payment_status.replace('_', ' ') : 'pending' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center mt-1 text-sm text-gray-600">
                                        <CalendarIcon class="w-4 h-4 mr-1" />
                                        <span v-if="booking.booking_time">
                                            {{ new Date(booking.booking_time.replace(' ', 'T')).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }}
                                            at
                                            {{ new Date(booking.booking_time.replace(' ', 'T')).toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit', hour12: true }) }}
                                        </span>
                                        <span v-else>Time TBD</span>
                                    </div>
                                    <p v-if="booking.notes" class="text-sm text-gray-500 mt-1">{{ booking.notes }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <CalendarIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No upcoming appointments</h3>
                            <p class="text-gray-500 mb-6">Your schedule is clear for now. Time to promote your services!</p>
                            <a href="/barber/appointments"
                               class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                View Schedule
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Availability Modal -->
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-white flex items-center">
                                <CalendarDaysIcon class="w-6 h-6 mr-3" />
                                Update Weekly Availability
                            </h2>
                            <button @click="showModal = false"
                                    class="text-blue-100 hover:text-white hover:bg-blue-800 rounded-full p-2 transition-colors duration-200">
                                <span class="text-2xl">&times;</span>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="submitAvailability" class="p-6">
                        <div class="space-y-4">
                            <div v-for="day in weekDays" :key="day"
                                 class="group p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <span :class="[
                                            'capitalize font-medium text-lg w-24',
                                            ['monday','tuesday','wednesday','thursday','friday'].includes(day) ? 'text-blue-600' : 'text-purple-600'
                                        ]">{{ day }}</span>
                                        <!-- Toggle Switch -->
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" v-model="form[day].is_available" class="sr-only peer">
                                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:bg-blue-600 transition-all duration-300"></div>
                                            <div class="absolute left-1 top-1 bg-white w-5 h-5 rounded-full shadow-lg transition-all duration-300 peer-checked:translate-x-7"></div>
                                        </label>
                                    </div>
                                    <div v-if="form[day].is_available" class="flex items-center space-x-3">
                                        <input type="time" v-model="form[day].start_time"
                                               class="border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
                                        <span class="text-gray-400 font-medium">to</span>
                                        <input type="time" v-model="form[day].end_time"
                                               class="border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" />
                                    </div>
                                    <div v-else class="text-gray-400 font-medium">Closed</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                            <button type="button" @click="showModal = false"
                                    class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-lg hover:shadow-xl">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>
