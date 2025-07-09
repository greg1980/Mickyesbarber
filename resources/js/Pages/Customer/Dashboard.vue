<template>
    <Head title="Customer Dashboard">
      <meta name="description" content="Customer dashboard for Mickyes Coiffure. View your appointments, favorite barbers, and spending stats." />
      <meta property="og:title" content="Customer Dashboard - Mickyes Coiffure" />
      <meta property="og:description" content="Access your customer dashboard to manage appointments, barbers, and more at Mickyes Coiffure." />
      <meta property="og:type" content="website" />
      <meta property="og:url" content="https://mickyes.com/customer/dashboard" />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content="Customer Dashboard - Mickyes Coiffure" />
      <meta name="twitter:description" content="Customer dashboard for managing appointments and barbers at Mickyes Coiffure." />
    </Head>

    <SidebarLayout>
        <div class="p-3 lg:p-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="mb-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 100 100">
                        <pattern id="dashboard-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1"/>
                            <circle cx="18" cy="18" r="1"/>
                            <circle cx="10" cy="10" r="0.5"/>
                        </pattern>
                        <rect width="100" height="100" fill="url(#dashboard-pattern)"/>
                    </svg>
                </div>
                <div class="relative z-10 flex items-center space-x-4">
                    <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold mb-2">Customer Dashboard</h1>
                        <p class="text-blue-100 text-lg">Your personal hub for appointments, barbers, and spending</p>
                    </div>
                </div>
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
                    <div class="bg-white shadow-lg border border-gray-300 rounded p-4 lg:p-6 w-full flex flex-col transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
                        <h3 class="text-base lg:text-lg font-semibold mb-2 lg:mb-4 text-gray-600 flex items-center gap-2">
                            <div class="h-5 w-5 lg:h-6 lg:w-6 bg-purple-100 rounded-full flex items-center justify-center">
                                <ChartPieIcon class="w-3 h-3 lg:w-4 lg:h-4 text-purple-600" />
                            </div>
                            <span class="text-sm lg:text-base">Bookings per Barber</span>
                        </h3>
                        <div class="flex-1 flex flex-col xs:flex-row items-center justify-center w-full gap-2 xs:gap-4">
                            <!-- Chart Container -->
                            <div class="flex justify-center items-center mb-2 lg:mb-3 w-full xs:w-auto">
                                <DoughnutChart :data="doughnutData3" :options="doughnutOptions" style="max-width:80px; height:80px; min-width:60px;" class="mx-auto xs:mx-0 lg:max-w-[100px] lg:h-24" />
                            </div>
                            <!-- Legend below chart on mobile, right on larger screens -->
                            <div v-if="doughnutData3.labels.length" class="w-full xs:w-auto overflow-x-auto px-2">
                                <ul class="text-xs text-gray-700 text-center xs:text-left space-y-0.5 w-full">
                                    <li v-for="(label, idx) in doughnutData3.labels" :key="label" class="flex items-center justify-center xs:justify-start whitespace-nowrap w-full">
                                        <div
                                            class="w-2 h-2 rounded-full mr-2 flex-shrink-0"
                                            :style="{ backgroundColor: doughnutData3.datasets[0].backgroundColor[idx] }"
                                        ></div>
                                        <span class="font-medium break-words truncate" style="max-width: 110px; display: inline-block;">
                                            {{ label }}
                                        </span>
                                        <span class="font-medium ml-1">: {{ doughnutData3.datasets[0].data[idx] }}</span>
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
                    <!-- Quick Actions Row -->
                    <div class="mb-4 flex flex-wrap gap-2">
                        <!-- Book New Appointment -->
                        <Link :href="route('booking.create')"
                              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 border border-green-200 rounded-lg transition-all duration-200 group text-sm font-medium">
                            <div class="h-6 w-6 bg-green-500 rounded-md flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <CalendarIcon class="w-3 h-3 text-white" />
                            </div>
                            <span class="text-green-900">Book Appointment</span>
                        </Link>

                        <!-- View All Bookings -->
                        <Link :href="route('customer.bookings')"
                              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 border border-blue-200 rounded-lg transition-all duration-200 group text-sm font-medium">
                            <div class="h-6 w-6 bg-blue-500 rounded-md flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span class="text-blue-900">View All Bookings</span>
                        </Link>

                        <!-- Rebook with Favorite -->
                        <button v-if="favouriteBarber"
                                @click="rebookWithFavorite"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 border border-purple-200 rounded-lg transition-all duration-200 group text-sm font-medium">
                            <div class="h-6 w-6 bg-purple-500 rounded-md flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <span class="text-purple-900">Rebook Favorite</span>
                        </button>
                    </div>

                    <div class="space-y-4 bg-white shadow-lg border border-gray-300 rounded p-4 lg:p-6">
                        <h2 class="text-base lg:text-lg font-semibold text-gray-600 flex items-center gap-2">
                            <div class="h-5 w-5 lg:h-6 lg:w-6 bg-blue-100 rounded-full flex items-center justify-center">
                                <CalendarIcon class="w-3 h-3 lg:w-4 lg:h-4 text-blue-600" />
                            </div>
                            Your Appointments
                        </h2>
                        <div v-if="bookings.length" class="space-y-3">
                            <div v-for="booking in bookings" :key="booking.id" class="bg-gray-50 border border-gray-200 p-4 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-semibold text-gray-900 text-sm lg:text-base">
                                                {{ booking.service_name || booking.service?.name || 'Service N/A' }}
                                            </h4>
                                            <span class="text-xs uppercase px-2 py-1 rounded-full font-medium" :class="getStatusClass(booking.status)">
                                                {{ booking.status || 'N/A' }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs lg:text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <UserIcon class="w-4 h-4 text-gray-400" />
                                                <span>{{ booking.barber?.user?.name || 'Barber N/A' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <CalendarIcon class="w-4 h-4 text-gray-400" />
                                                <span>{{ booking.booking_time ? formatDate(booking.booking_time) : 'Date N/A' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ booking.booking_time ? formatTime(booking.booking_time) : 'Time N/A' }}</span>
                                            </div>
                                            <div v-if="booking.service?.price" class="flex items-center gap-1">
                                                <CurrencyDollarIcon class="w-4 h-4 text-gray-400" />
                                                <span>£{{ booking.service.price }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div v-if="booking.status !== 'completed' && booking.status !== 'cancelled'" class="ml-4">
                                        <div class="flex flex-col gap-2">
                                            <button v-if="booking.status === 'confirmed'"
                                                    class="px-3 py-1 text-xs bg-green-100 text-green-700 hover:bg-green-200 rounded-md transition-colors duration-200">
                                                Check In
                                            </button>
                                            <button v-if="booking.status === 'confirmed'"
                                                    class="px-3 py-1 text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-md transition-colors duration-200">
                                                Reschedule
                                            </button>
                                        </div>
                                    </div>
                                </div>
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

function rebookWithFavorite() {
  if (favouriteBarber.value) {
    // Navigate to booking page with pre-selected barber
    window.location.href = route('booking.create') + '?barber=' + favouriteBarber.value.id
  }
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
