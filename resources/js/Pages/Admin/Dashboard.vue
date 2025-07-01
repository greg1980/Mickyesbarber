<template>
    <Head title="Admin Dashboard" />

    <SidebarLayout>
        <div class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
            <!-- Enhanced Premium Admin Header Banner -->
            <div class="mb-8">
                <!-- Professional Admin Header Banner -->
                <div class="bg-gradient-to-r from-gray-700 via-gray-800 to-gray-900 rounded-2xl shadow-xl p-8 text-white mb-6 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" fill="currentColor" viewBox="0 0 100 100">
                            <pattern id="admin-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="1"/>
                                <circle cx="18" cy="18" r="1"/>
                                <circle cx="10" cy="10" r="0.5"/>
                            </pattern>
                            <rect width="100" height="100" fill="url(#admin-pattern)"/>
                        </svg>
                    </div>

                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-4xl font-bold mb-2">Admin Management Hub</h1>
                                <p class="text-gray-100 text-lg">Administrative Dashboard • Mickyes Coiffure</p>
                                <p class="text-gray-200 text-sm mt-1">Monitor operations, manage users, and oversee business performance</p>
                            </div>
                        </div>

                        <!-- Admin Status Badge -->
                        <div class="hidden lg:block">
                            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/30">
                                <div class="text-center">
                                    <div class="text-2xl font-bold">{{ props.stats.totalUsers }}</div>
                                    <div class="text-xs text-gray-200 uppercase tracking-wider">Total Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Activity Status -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="h-2 w-2 bg-gray-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-gray-700">Admin Panel Active</span>
                        <span class="text-xs text-gray-500">• Full system access</span>
                    </div>

                    <!-- Quick Admin Actions -->
                    <div class="hidden md:flex items-center space-x-3">
                        <a :href="route('admin.barbers.manage')" class="flex items-center space-x-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1 hover:scale-105 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white shadow-gray-200">
                            <UsersIcon class="w-5 h-5" />
                            <span class="text-sm font-medium">Manage Barbers</span>
                        </a>
                        <a :href="route('admin.users.index')" class="flex items-center space-x-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1 hover:scale-105 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white shadow-orange-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Manage Users</span>
                        </a>
                        <a :href="route('admin.services.index')" class="flex items-center space-x-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1 hover:scale-105 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white shadow-purple-200">
                            <ScissorsIcon class="w-5 h-5" />
                            <span class="text-sm font-medium">Manage Services</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="showSuccess" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-green-800 font-medium">Booking created successfully!</p>
                </div>
            </div>

                        <!-- Enhanced Premium Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Premium Top Barber Card -->
                <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer relative overflow-hidden h-80">
                    <!-- Subtle background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative z-10 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider flex items-center gap-3">
                                <div class="h-10 w-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <StarIcon class="w-6 h-6 text-white" />
                                </div>
                                Top Performer
                            </h3>
                        </div>

                        <!-- Content area with fixed height -->
                        <div class="flex-grow">
                            <div class="flex items-center space-x-3">
                                <img :src="props.topBarber.photo || '/images/default-avatar.png'"
                                     alt="Top Barber"
                                     class="w-16 h-16 rounded-2xl object-cover border-2 border-yellow-200 shadow-lg" />
                                <div>
                                    <div class="text-xl font-bold text-gray-900">{{ props.topBarber.name }}</div>
                                    <div class="text-sm text-gray-600 font-medium">{{ props.topBarber.haircuts }} services completed</div>
                                    <div class="flex items-center mt-1">
                                        <span class="text-lg font-bold text-yellow-600">{{ props.topBarber.rating }}</span>
                                        <span class="text-yellow-400 ml-1">⭐</span>
                                        <span class="text-xs text-gray-500 ml-2">Excellent rating</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress bar - positioned at bottom -->
                        <div class="mt-auto">
                            <div class="bg-gray-100 rounded-full h-2">
                                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 h-2 rounded-full" :style="`width: ${Math.min((props.topBarber.rating / 5) * 100, 100)}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium Pending Approvals Card -->
                <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer relative overflow-hidden h-80">
                    <!-- Subtle background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative z-10 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider flex items-center gap-3">
                                <div class="h-10 w-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <ClockIcon class="w-6 h-6 text-white" />
                                </div>
                                Pending Reviews
                            </h3>
                        </div>

                        <!-- Content area with fixed height -->
                        <div class="flex-grow">
                            <div v-if="pendingBarbers.length > 0" class="space-y-4">
                                <div v-if="pendingBarbers[0]" :key="pendingBarbers[0].id" class="bg-gray-50 rounded-xl p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-900 text-lg">{{ pendingBarbers[0].name }}</p>
                                            <p class="text-sm text-gray-600 font-medium">{{ pendingBarbers[0].email }}</p>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Applied: {{ pendingBarbers[0].applied_date }}
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            <button @click="handleSingleToggle(pendingBarbers[0].id, 'declined')"
                                                    class="px-3 py-2 text-sm font-medium bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200 border border-red-200">
                                                Decline
                                            </button>
                                            <button @click="handleSingleToggle(pendingBarbers[0].id, 'approved')"
                                                    class="px-3 py-2 text-sm font-medium bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 border border-green-200">
                                                Approve
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <div class="text-4xl mb-3">✅</div>
                                <p class="text-gray-500 font-medium">No pending approvals</p>
                                <p class="text-xs text-gray-400 mt-1">All applications reviewed!</p>
                            </div>
                        </div>

                        <!-- Progress indicator - positioned at bottom -->
                        <div class="mt-auto">
                            <div class="bg-gray-100 rounded-full h-2">
                                <div class="bg-gradient-to-r from-orange-400 to-orange-600 h-2 rounded-full" :style="`width: ${pendingBarbers.length > 0 ? 75 : 100}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium System Stats Card -->
                <div class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer relative overflow-hidden h-80">
                    <!-- Subtle background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="relative z-10 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-600 mb-2 uppercase tracking-wider flex items-center gap-3">
                                <div class="h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <UsersIcon class="w-6 h-6 text-white" />
                                </div>
                                System Overview
                            </h3>
                        </div>

                        <!-- Content area with fixed height -->
                        <div class="flex-grow">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between bg-blue-50 rounded-lg p-3">
                                    <span class="text-sm font-semibold text-gray-700">Total Users</span>
                                    <span class="text-2xl font-bold text-blue-600">{{ props.stats.totalUsers }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-green-50 rounded-lg p-3">
                                    <span class="text-sm font-semibold text-gray-700">Active Barbers</span>
                                    <span class="text-2xl font-bold text-green-600">{{ props.stats.activeBarbers }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-purple-50 rounded-lg p-3">
                                    <span class="text-sm font-semibold text-gray-700">Total Appointments</span>
                                    <span class="text-2xl font-bold text-purple-600">{{ props.stats.totalAppointments }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- System health indicator - positioned at bottom -->
                        <div class="mt-auto">
                            <div class="bg-gray-100 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" style="width: 95%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 text-center">System Health: Excellent</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Recent Activity Section -->
            <div class="mb-8">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <!-- Enhanced header -->
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center shadow-lg">
                                <BellIcon class="w-6 h-6 text-white" />
                            </div>
                            Recent Activity Monitor
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Live system activity and user interactions</p>
                    </div>

                    <div class="p-6">
                        <div v-if="recentActivity.length > 0" class="w-full">
                            <div class="flex w-full items-center justify-between">
                                <button @click="prevActivity"
                                        class="p-3 bg-gradient-to-r from-orange-100 to-orange-200 rounded-xl hover:from-orange-200 hover:to-orange-300 border border-orange-200 transition-all duration-200 group">
                                    <svg class="w-6 h-6 text-orange-600 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>

                                <div class="flex-1 text-center mx-6 bg-gray-50 rounded-xl p-4">
                                    <p class="text-lg font-semibold text-gray-900 mb-2">{{ recentActivity[currentActivity].description }}</p>
                                    <p class="text-sm text-gray-600 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ recentActivity[currentActivity].date }}
                                    </p>
                                </div>

                                <button @click="nextActivity"
                                        class="p-3 bg-gradient-to-r from-orange-100 to-orange-200 rounded-xl hover:from-orange-200 hover:to-orange-300 border border-orange-200 transition-all duration-200 group">
                                    <svg class="w-6 h-6 text-orange-600 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <div class="text-6xl mb-4">📊</div>
                            <p class="text-xl font-semibold text-gray-700 mb-2">No recent activity</p>
                            <p class="text-gray-500">System activity will appear here as it happens</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Appointment Slots Section -->
            <div class="max-w-7xl mx-auto">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <!-- Enhanced section header -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-blue-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <CalendarDaysIcon class="w-6 h-6 text-white" />
                            </div>
                            Today's Appointment Management
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Monitor and manage all appointment slots for {{ today }}</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            <div v-for="slot in slots" :key="slot.time"
                                 class="bg-gray-50 border-2 border-gray-200 hover:border-blue-300 rounded-xl p-4 transition-all duration-200 hover:shadow-lg">

                                <!-- Time header -->
                                <div class="flex items-center justify-center mb-4">
                                    <div class="flex items-center space-x-2 bg-white rounded-lg px-3 py-2 shadow-sm">
                                        <div class="h-6 w-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="text-lg font-bold text-gray-900">{{ slot.time }}</div>
                                    </div>
                                </div>

                                <!-- Slot content -->
                                <div v-if="slot.booking" class="text-center">
                                    <img v-if="slot.booking.user.photo"
                                         :src="slot.booking.user.photo"
                                         alt="Customer"
                                         class="w-16 h-16 rounded-xl object-cover border-2 border-blue-200 shadow-sm mx-auto mb-3" />
                                    <div class="font-bold text-gray-900 mb-1">{{ slot.booking.user.name }}</div>
                                    <div class="text-sm text-gray-600 mb-3">{{ slot.booking.user.email }}</div>
                                    <div :class="statusClass(slot.booking.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                        <template v-if="slot.booking.status === 'checked_in'">✅ Checked In</template>
                                        <template v-else-if="slot.booking.status === 'completed'">🎉 Completed</template>
                                        <template v-else-if="slot.booking.status === 'rescheduled'">📅 Rescheduled</template>
                                        <template v-else>⏳ {{ slot.booking.status }}</template>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4">
                                    <div class="mb-3 flex justify-center">
                                        <div class="h-12 w-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-gray-500 text-sm mb-3 font-medium">Available Slot</div>
                                    <a :href="`/booking/create?date=${today}&time=${slot.time.replace(/\s/g, '')}`"
                                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { ref, onMounted, onUnmounted } from 'vue'
import dayjs from 'dayjs'
import { BellIcon, CalendarDaysIcon, UsersIcon, ScissorsIcon, CalendarIcon, StarIcon, ClockIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalUsers: 0,
            activeBarbers: 0,
            totalAppointments: 0
        })
    },
    recentActivity: {
        type: Array,
        default: () => []
    },
    topBarber: {
        type: Object,
        default: () => ({
            name: 'N/A',
            photo: null,
            haircuts: 0,
            rating: 0
        })
    },
    pendingBarbers: {
        type: Array,
        default: () => []
    }
})

// Use real recentActivity from props
const recentActivity = props.recentActivity

// Use real pendingBarbers from props
const pendingBarbers = props.pendingBarbers

const currentActivity = ref(0)
let intervalId = null

const nextActivity = () => {
    currentActivity.value = (currentActivity.value + 1) % recentActivity.length
}
const prevActivity = () => {
    currentActivity.value = (currentActivity.value - 1 + recentActivity.length) % recentActivity.length
}

// Track approval status for each barber
const barberStatuses = ref({})
const feedbackMessages = ref({})

const handleSingleToggle = (barberId, action) => {
    barberStatuses.value[barberId] = 'processing'
    const routeName = action === 'approved' ? 'admin.barber.approve' : 'admin.barber.decline'
    router.post(route(routeName, barberId), {}, {
        onSuccess: () => {
            barberStatuses.value[barberId] = action
            feedbackMessages.value[barberId] = action === 'approved' ? 'Approved' : 'Declined'
            // Remove the first barber from the list
            pendingBarbers.value.splice(0, 1)
        },
        onError: () => {
            barberStatuses.value[barberId] = null
            feedbackMessages.value[barberId] = 'Error, try again'
        }
    })
}

const showSuccess = ref(false)
const slots = ref([])
const today = dayjs().format('YYYY-MM-DD')

onMounted(async () => {
  const res = await fetch('/admin/slots/today')
  const data = await res.json()
  slots.value = data.slots
  const params = new URLSearchParams(window.location.search)
  if (params.get('success') === '1') {
    showSuccess.value = true
    window.history.replaceState({}, document.title, window.location.pathname)
  }
  intervalId = setInterval(nextActivity, 10000)
})
onUnmounted(() => {
    clearInterval(intervalId)
})

function statusClass(status) {
  if (status === 'checked_in' || status === 'confirmed') return 'text-green-600 font-bold'
  if (status === 'completed') return 'text-blue-600 font-bold'
  if (status === 'rescheduled') return 'text-yellow-600 font-bold'
  return 'text-gray-500'
}
</script>
