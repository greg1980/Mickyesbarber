<template>
    <Head title="My Bookings" />
    <SidebarLayout>
        <div class="p-3 lg:p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
            <!-- Modern Header with Gradient -->
            <div class="mb-6 bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 100 100">
                        <pattern id="booking-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1"/>
                            <circle cx="18" cy="18" r="1"/>
                            <circle cx="10" cy="10" r="0.5"/>
                        </pattern>
                        <rect width="100" height="100" fill="url(#booking-pattern)"/>
                    </svg>
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold mb-2">My Bookings</h1>
                            <p class="text-blue-100 text-lg">Manage your appointments and services</p>
                        </div>
                    </div>

                    <!-- Stats Badge -->
                    <div class="hidden lg:block">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/30">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ totalBookings }}</div>
                                <div class="text-xs text-blue-200 uppercase tracking-wider">Total Bookings</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ successMessage }}</span>
                </div>
            </div>

            <!-- Enhanced Search and Filter Bar -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Search Bar -->
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search by service, barber, or date..."
                            class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        >
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Status Filter -->
                        <select v-model="statusFilter" class="px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <!-- Time Filter -->
                        <select v-model="timeFilter" class="px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Time</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="past">Past</option>
                            <option value="this-month">This Month</option>
                            <option value="last-month">Last Month</option>
                        </select>

                        <!-- Clear Filters -->
                        <button @click="clearFilters" class="px-4 py-3 text-gray-600 hover:text-gray-800 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-2xl font-bold text-gray-900">{{ upcomingCount }}</p>
                            <p class="text-sm text-gray-600">Upcoming</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-2xl font-bold text-gray-900">{{ pendingCount }}</p>
                            <p class="text-sm text-gray-600">Pending</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-2xl font-bold text-gray-900">{{ completedCount }}</p>
                            <p class="text-sm text-gray-600">Completed</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-2xl font-bold text-gray-900">£{{ totalSpent }}</p>
                            <p class="text-sm text-gray-600">Total Spent</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Booking Cards -->
            <div class="space-y-6">
                <div v-for="booking in filteredBookings" :key="booking.id"
                     class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300">

                    <!-- Card Header -->
                    <div class="relative p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                                <!-- Service Icon -->
                                <div class="h-10 w-10 sm:h-12 sm:w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mx-auto sm:mx-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5.236V9a2 2 0 00-2-2H6a2 2 0 00-2 2v10.236"/>
                                    </svg>
                                </div>

                                <div class="text-center sm:text-left">
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">
                                        {{ booking.service?.name || 'Service N/A' }}
                                    </h3>
                                    <p class="text-gray-600 flex items-center gap-2 mt-1 justify-center sm:justify-start text-xs sm:text-base">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        Booking #{{ booking.id }}
                                    </p>
                                </div>
                            </div>

                            <!-- Status Badge and Price -->
                            <div class="flex flex-col sm:items-end gap-2 sm:gap-3 text-center sm:text-right">
                                <span :class="[
                                    'px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-semibold rounded-full',
                                    getStatusClass(booking.status)
                                ]">
                                    {{ getStatusText(booking.status) }}
                                </span>
                                <div>
                                    <div class="text-lg sm:text-2xl font-bold text-gray-900">£{{ formatPrice(booking.service_price) }}</div>
                                    <div class="text-xs sm:text-sm text-gray-500">Total Price</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            <!-- Date & Time -->
                            <div class="space-y-2 sm:space-y-3">
                                <h4 class="text-sm sm:text-base font-semibold text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Schedule
                                </h4>
                                <div class="space-y-1 sm:space-y-2">
                                    <div class="flex items-center gap-1 text-gray-700 text-xs sm:text-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-medium">{{ formatDate(booking.booking_date) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-gray-700 text-xs sm:text-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="font-medium">{{ formatTime(booking.booking_time) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Barber Info -->
                            <div class="space-y-2 sm:space-y-3">
                                <h4 class="text-sm sm:text-base font-semibold text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Your Barber
                                </h4>
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <img v-if="booking.barber?.user?.profile_photo"
                                         :src="booking.barber.user.profile_photo"
                                         :alt="'Barber photo for ' + (booking.barber.user.name || 'Barber')"
                                         class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover border" />
                                    <div v-else class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-bold shadow-md">
                                        {{ booking.barber?.user?.name ? booking.barber.user.name.charAt(0).toUpperCase() : 'B' }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 text-xs sm:text-base">
                                            {{ booking.barber?.user?.name || 'Barber N/A' }}
                                        </div>
                                        <div class="text-xs sm:text-sm text-gray-500">Professional Barber</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div class="space-y-2 sm:space-y-3">
                                <h4 class="text-sm sm:text-base font-semibold text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    Payment
                                </h4>
                                <div class="flex flex-col gap-1 sm:gap-2 text-xs sm:text-sm text-gray-700">
                                    <div>
                                        <span class="font-medium">Amount Paid:</span> £{{ formatPrice(booking.amount_paid) }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Balance:</span> £{{ formatPrice(booking.service_price - booking.amount_paid) }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Status:</span> {{ booking.payment_status || 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div v-if="booking.status !== 'cancelled' && !isPastBooking(booking)" class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-100">
                            <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                                <button v-if="booking.service_price - booking.amount_paid > 0"
                                        @click="openPaymentModal(booking)"
                                        class="flex items-center gap-2 justify-center px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md text-xs sm:text-base">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    Pay Balance (£{{ formatPrice(booking.service_price - booking.amount_paid) }})
                                </button>

                                <button @click="openRescheduleModal(booking)"
                                        class="flex items-center gap-2 justify-center px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md text-xs sm:text-base">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Reschedule
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredBookings.length === 0" class="text-center py-16">
                    <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No bookings found</h3>
                    <p class="text-gray-500 mb-6">{{ searchQuery || statusFilter || timeFilter ? 'Try adjusting your filters' : 'You haven\'t made any bookings yet' }}</p>
                    <Link :href="route('booking.create')" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Book Your First Appointment
                    </Link>
                </div>
            </div>

            <!-- Payment Modal -->
            <PaymentModal
                :show="showPaymentModal"
                :booking="selectedBooking"
                @close="showPaymentModal = false"
                @payment-success="handlePaymentSuccess"
            />

            <!-- Enhanced Reschedule Modal -->
            <div v-if="showRescheduleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full relative">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-100 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                                <div class="h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                Reschedule Booking
                            </h3>
                            <button @click="closeRescheduleModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-6">
                        <!-- Current Booking Details -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-xl">
                            <h4 class="font-medium text-gray-700 mb-2">Current Booking</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p><span class="font-medium">Service:</span> {{ rescheduleBooking?.service?.name || 'N/A' }}</p>
                                <p><span class="font-medium">Date:</span> {{ formatDate(rescheduleBooking?.booking_date) }}</p>
                                <p><span class="font-medium">Time:</span> {{ formatTime(rescheduleBooking?.booking_time) }}</p>
                                <p><span class="font-medium">Barber:</span> {{ rescheduleBooking?.barber?.user?.name || 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- New Date Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select New Date</label>
                            <input type="date" v-model="rescheduleDate" :min="minDate"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   @change="fetchAvailableSlots" />
                        </div>

                        <!-- New Time Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select New Time</label>
                            <div v-if="loadingSlots" class="text-gray-500 text-sm py-4 text-center">Loading available slots...</div>
                            <div v-else-if="availableSlots.length" class="grid grid-cols-3 gap-2">
                                <button v-for="slot in availableSlots" :key="slot"
                                        @click="rescheduleTime = slot"
                                        :class="[
                                            'px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                            rescheduleTime === slot
                                                ? 'bg-blue-500 text-white shadow-md'
                                                : 'bg-gray-100 text-gray-800 hover:bg-blue-100'
                                        ]">
                                    {{ slot }}
                                </button>
                            </div>
                            <div v-else-if="rescheduleDate" class="text-gray-500 text-sm py-4 text-center">No available slots for this date.</div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex gap-3">
                            <button @click="closeRescheduleModal"
                                    class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200">
                                Cancel
                            </button>
                            <button @click="confirmReschedule"
                                    :disabled="!rescheduleDate || !rescheduleTime || rescheduling"
                                    class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ rescheduling ? 'Rescheduling...' : 'Confirm Reschedule' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import PaymentModal from '@/Components/PaymentModal.vue'
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
    bookings: Array,
    successMessage: String
})

// Reactive data
const showPaymentModal = ref(false)
const selectedBooking = ref(null)
const showRescheduleModal = ref(false)
const rescheduleBooking = ref(null)
const rescheduleDate = ref('')
const rescheduleTime = ref('')
const availableSlots = ref([])
const availableBarbers = ref([])
const selectedBarberId = ref(null)
const loadingSlots = ref(false)
const loadingBarbers = ref(false)
const rescheduling = ref(false)
const rescheduleError = ref('')

// New filtering data
const searchQuery = ref('')
const statusFilter = ref('')
const timeFilter = ref('')

// Computed properties
const totalBookings = computed(() => props.bookings.length)

const upcomingCount = computed(() => {
    return props.bookings.filter(booking =>
        booking.status !== 'cancelled' &&
        booking.status !== 'completed' &&
        !isPastBooking(booking)
    ).length
})

const pendingCount = computed(() => {
    return props.bookings.filter(booking => booking.status === 'pending').length
})

const completedCount = computed(() => {
    return props.bookings.filter(booking => booking.status === 'completed').length
})

const totalSpent = computed(() => {
    return props.bookings
        .filter(booking => booking.status === 'completed')
        .reduce((total, booking) => total + parseFloat(booking.service_price || 0), 0)
        .toFixed(2)
})

const filteredBookings = computed(() => {
    let filtered = [...props.bookings]

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(booking =>
            (booking.service?.name || '').toLowerCase().includes(query) ||
            (booking.barber?.user?.name || '').toLowerCase().includes(query) ||
            formatDate(booking.booking_date).toLowerCase().includes(query) ||
            formatTime(booking.booking_time).toLowerCase().includes(query)
        )
    }

    // Status filter
    if (statusFilter.value) {
        filtered = filtered.filter(booking => booking.status === statusFilter.value)
    }

    // Time filter
    if (timeFilter.value) {
        const now = new Date()
        const currentMonth = now.getMonth()
        const currentYear = now.getFullYear()

        filtered = filtered.filter(booking => {
            const bookingDate = new Date(booking.booking_date)

            switch (timeFilter.value) {
                case 'upcoming':
                    return !isPastBooking(booking) && booking.status !== 'cancelled' && booking.status !== 'completed'
                case 'past':
                    return isPastBooking(booking) || booking.status === 'completed'
                case 'this-month':
                    return bookingDate.getMonth() === currentMonth && bookingDate.getFullYear() === currentYear
                case 'last-month':
                    const lastMonth = currentMonth === 0 ? 11 : currentMonth - 1
                    const lastMonthYear = currentMonth === 0 ? currentYear - 1 : currentYear
                    return bookingDate.getMonth() === lastMonth && bookingDate.getFullYear() === lastMonthYear
                default:
                    return true
            }
        })
    }

    // Sort by date - upcoming first, then most recent
    return filtered.sort((a, b) => {
        const dateA = new Date(a.booking_date + ' ' + a.booking_time)
        const dateB = new Date(b.booking_date + ' ' + b.booking_time)
        const now = new Date()

        // If both are future or both are past, sort chronologically
        if ((dateA >= now && dateB >= now) || (dateA < now && dateB < now)) {
            return dateA - dateB
        }

        // Future dates come first
        return dateA >= now ? -1 : 1
    })
})

const minDate = computed(() => {
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    return tomorrow.toISOString().split('T')[0]
})

// Methods
function clearFilters() {
    searchQuery.value = ''
    statusFilter.value = ''
    timeFilter.value = ''
}

function getStatusClass(status) {
    switch (status) {
        case 'confirmed':
            return 'bg-green-100 text-green-800'
        case 'pending':
            return 'bg-yellow-100 text-yellow-800'
        case 'completed':
            return 'bg-blue-100 text-blue-800'
        case 'cancelled':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

function getStatusText(status) {
    switch (status) {
        case 'confirmed':
            return 'Confirmed'
        case 'pending':
            return 'Pending'
        case 'completed':
            return 'Completed'
        case 'cancelled':
            return 'Cancelled'
        default:
            return 'Unknown'
    }
}

function isPastBooking(booking) {
    if (!booking.booking_date || !booking.booking_time) return false
    const bookingDateTime = new Date(booking.booking_date + ' ' + booking.booking_time)
    return bookingDateTime < new Date()
}

function openPaymentModal(booking) {
    selectedBooking.value = booking
    showPaymentModal.value = true
}

function handlePaymentSuccess() {
    showPaymentModal.value = false
    // Optionally refresh page or show success message
    window.location.reload()
}

function openRescheduleModal(booking) {
    rescheduleBooking.value = booking
    showRescheduleModal.value = true
    rescheduleDate.value = ''
    rescheduleTime.value = ''
    availableSlots.value = []
    rescheduleError.value = ''
}

function closeRescheduleModal() {
    showRescheduleModal.value = false
    rescheduleBooking.value = null
    rescheduleDate.value = ''
    rescheduleTime.value = ''
    availableSlots.value = []
    rescheduleError.value = ''
    rescheduling.value = false
}

async function fetchAvailableSlots() {
    if (!rescheduleDate.value || !rescheduleBooking.value) return

    loadingSlots.value = true
    availableSlots.value = []
    rescheduleTime.value = ''

    try {
        const response = await fetch('/api/available-slots', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                date: rescheduleDate.value,
                barber_id: rescheduleBooking.value.barber_id,
                service_id: rescheduleBooking.value.service_id,
                exclude_booking_id: rescheduleBooking.value.id
            })
        })

        if (response.ok) {
            const data = await response.json()
            availableSlots.value = data.slots || []
        } else {
            console.error('Failed to fetch available slots')
            availableSlots.value = []
        }
    } catch (error) {
        console.error('Error fetching available slots:', error)
        availableSlots.value = []
    } finally {
        loadingSlots.value = false
    }
}

async function confirmReschedule() {
    if (!rescheduleDate.value || !rescheduleTime.value || !rescheduleBooking.value) {
        rescheduleError.value = 'Please select both date and time'
        return
    }

    rescheduling.value = true
    rescheduleError.value = ''

    try {
        const response = await fetch(`/booking/${rescheduleBooking.value.id}/reschedule`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                booking_date: rescheduleDate.value,
                booking_time: rescheduleTime.value,
                barber_id: rescheduleBooking.value.barber_id
            })
        })

        if (response.ok) {
            closeRescheduleModal()
            window.location.reload() // Refresh to show updated booking
        } else {
            const errorData = await response.json()
            rescheduleError.value = errorData.message || 'Failed to reschedule booking'
        }
    } catch (error) {
        console.error('Error rescheduling booking:', error)
        rescheduleError.value = 'Network error. Please try again.'
    } finally {
        rescheduling.value = false
    }
}

async function handleCancel(booking) {
    if (!confirm('Are you sure you want to cancel this booking?')) {
        return
    }

    try {
        const response = await fetch(`/booking/${booking.id}/cancel`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })

        if (response.ok) {
            window.location.reload() // Refresh to show updated booking status
        } else {
            const errorData = await response.json()
            alert(errorData.message || 'Failed to cancel booking')
        }
    } catch (error) {
        console.error('Error cancelling booking:', error)
        alert('Network error. Please try again.')
    }
}

function formatDate(date) {
    if (!date) return 'N/A'
    const options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }
    return new Date(date).toLocaleDateString('en-US', options)
}

function formatTime(time) {
    if (!time) return ''

    // If time is in HH:mm:ss or HH:mm format
    if (/^\d{2}:\d{2}(:\d{2})?$/.test(time)) {
        const [h, m] = time.split(':')
        const hour = parseInt(h, 10)
        const minute = m
        const ampm = hour >= 12 ? 'PM' : 'AM'
        const hour12 = hour % 12 === 0 ? 12 : hour % 12
        return `${hour12}:${minute} ${ampm}`
    }

    // If time is a full ISO string, extract time part in local time
    if (time.length > 5) {
        const d = new Date(time)
        const hour = d.getHours()
        const minute = d.getMinutes().toString().padStart(2, '0')
        const ampm = hour >= 12 ? 'PM' : 'AM'
        const hour12 = hour % 12 === 0 ? 12 : hour % 12
        return `${hour12}:${minute} ${ampm}`
    }

    return time
}

function formatPrice(value) {
    const number = parseFloat(value) || 0
    return number.toFixed(2)
}
</script>
