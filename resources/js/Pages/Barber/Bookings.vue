<template>
  <SidebarLayout>
    <div class="p-4 sm:p-6 lg:p-8 space-y-6 bg-gray-50 min-h-screen">
      <!-- Modern Header with Gradient -->
      <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4a4 4 0 118 0v4a6 6 0 01-12 0V7z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Booking Management</h1>
              <p class="text-gray-600">View and manage all your customer bookings</p>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="hidden lg:flex items-center space-x-4">
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
              <div class="text-sm text-gray-500">Total Bookings</div>
              <div class="text-lg font-semibold text-gray-900">{{ bookings.total || 0 }}</div>
            </div>

            <!-- PDF Export Button -->
            <button
              @click="exportToPDF"
              class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>Export PDF</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Enhanced Filter Section -->
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <!-- Filter Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
              </svg>
              <h3 class="text-lg font-semibold text-gray-900">Filter Bookings</h3>
            </div>

            <!-- Active Filter Indicator -->
            <div v-if="hasFilters" class="flex items-center space-x-2">
              <span class="text-sm text-blue-600 font-medium">{{ Object.keys(props.filters).length }} filter(s) active</span>
              <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            </div>
          </div>
        </div>

        <!-- Desktop Filters -->
        <div class="p-6 hidden sm:block">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Search Customer
              </label>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by name or email..."
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              />
            </div>

            <!-- Status Filter -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Status
              </label>
              <select
                v-model="statusFilter"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              >
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="rescheduled">Rescheduled</option>
              </select>
            </div>

            <!-- Date Filter -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Filter by Date
              </label>
              <input
                v-model="dateFilter"
                type="date"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              />
            </div>

            <!-- Filter Actions -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
              <div class="flex flex-col space-y-2">
                <button
                  @click="applyFilters"
                  class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center space-x-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                  <span>Apply Filters</span>
                </button>
                <button
                  @click="clearFilters"
                  :disabled="!hasFilters"
                  :class="[
                    'w-full px-4 py-2.5 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center space-x-2',
                    hasFilters
                      ? 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500'
                      : 'bg-gray-200 text-gray-400 cursor-not-allowed'
                  ]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  <span>Clear All</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Filters -->
        <div class="p-4 sm:hidden space-y-4">
          <!-- Search -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Search Customer</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by name or email..."
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Status Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <div class="relative">
              <button
                @click="showStatusDropdown = !showStatusDropdown"
                class="w-full px-3 py-2.5 text-left border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white flex justify-between items-center"
              >
                <span>{{ statusFilter || 'All Statuses' }}</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              <div v-if="showStatusDropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg">
                <button
                  @click="selectStatus('')"
                  class="w-full px-3 py-2.5 text-left hover:bg-gray-50 border-b border-gray-100 rounded-t-lg"
                >
                  All Statuses
                </button>
                <button
                  @click="selectStatus('pending')"
                  class="w-full px-3 py-2.5 text-left hover:bg-gray-50 border-b border-gray-100"
                >
                  Pending
                </button>
                <button
                  @click="selectStatus('confirmed')"
                  class="w-full px-3 py-2.5 text-left hover:bg-gray-50 border-b border-gray-100"
                >
                  Confirmed
                </button>
                <button
                  @click="selectStatus('completed')"
                  class="w-full px-3 py-2.5 text-left hover:bg-gray-50 border-b border-gray-100"
                >
                  Completed
                </button>
                <button
                  @click="selectStatus('cancelled')"
                  class="w-full px-3 py-2.5 text-left hover:bg-gray-50 border-b border-gray-100"
                >
                  Cancelled
                </button>
                <button
                  @click="selectStatus('rescheduled')"
                  class="w-full px-3 py-2.5 text-left hover:bg-gray-50 rounded-b-lg"
                >
                  Rescheduled
                </button>
              </div>
            </div>
          </div>

          <!-- Date Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Filter by Date</label>
            <input
              v-model="dateFilter"
              type="date"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Mobile Filter Actions -->
          <div class="space-y-3">
            <div class="flex space-x-3">
              <button
                @click="applyFilters"
                class="flex-1 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
              >
                Apply Filters
              </button>
              <button
                @click="clearFilters"
                :disabled="!hasFilters"
                :class="[
                  'flex-1 px-4 py-2.5 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 transition-all duration-200',
                  hasFilters
                    ? 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'
                ]"
              >
                Clear All
              </button>
            </div>

            <!-- Mobile Export Button -->
            <button
              @click="exportToPDF"
              class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>Export PDF</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Modern Mobile Card View -->
      <div v-if="bookings.data.length > 0" class="sm:hidden space-y-4">
        <div
          v-for="booking in bookings.data"
          :key="booking.id"
          class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200"
        >
          <!-- Card Header -->
          <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <img
                  :src="booking.user.profile_photo_url || '/images/default-avatar.png'"
                  :alt="booking.user.name"
                  class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100"
                />
                <div>
                  <div class="text-sm font-semibold text-gray-900">{{ booking.user.name }}</div>
                  <div class="text-xs text-gray-500">{{ booking.user.email }}</div>
                </div>
              </div>
              <div class="flex flex-col items-end space-y-1">
                <span :class="getStatusClass(booking.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ formatStatus(booking.status) }}
                </span>
                <span :class="getPaymentStatusClass(booking.payment_status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ formatPaymentStatus(booking.payment_status) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-4 space-y-3">
            <div class="grid grid-cols-2 gap-4">
              <!-- Date & Time -->
              <div class="space-y-1">
                <div class="flex items-center space-x-1">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</span>
                </div>
                <div class="text-sm font-semibold text-gray-900">{{ formatDate(booking.booking_date) }}</div>
                <div class="text-sm text-gray-600">{{ booking.booking_time }}</div>
              </div>

              <!-- Service & Price -->
              <div class="space-y-1">
                <div class="flex items-center space-x-1">
                  <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4l-2 6h14l-2-6"/>
                  </svg>
                  <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Service</span>
                </div>
                <div class="text-sm font-semibold text-gray-900">{{ booking.service_name }}</div>
                <div class="text-sm font-bold text-green-600">£{{ booking.service_price }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modern Desktop Table -->
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hidden sm:block">
        <div v-if="bookings.data.length > 0">
          <!-- Table Header -->
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Booking Records</h3>
            <p class="text-sm text-gray-600 mt-1">Manage and review all your customer bookings</p>
          </div>

          <!-- Table -->
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Customer</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Date & Time</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2M7 4h10M7 4l-2 6h14l-2-6"/>
                    </svg>
                    <span>Service</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Status</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    <span>Price</span>
                  </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span>Payment</span>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="booking in bookings.data" :key="booking.id" class="hover:bg-gray-50 transition-colors duration-150">
                <!-- Customer -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-3">
                    <img
                      :src="booking.user.profile_photo_url || '/images/default-avatar.png'"
                      :alt="booking.user.name"
                      class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100"
                    />
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ booking.user.name }}</div>
                      <div class="text-sm text-gray-500">{{ booking.user.email }}</div>
                    </div>
                  </div>
                </td>

                <!-- Date & Time -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ formatDate(booking.booking_date) }}</div>
                  <div class="text-sm text-gray-500">{{ booking.booking_time }}</div>
                </td>

                <!-- Service -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ booking.service_name }}</div>
                </td>

                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(booking.status)" class="px-3 py-1 text-xs font-semibold rounded-full">
                    {{ formatStatus(booking.status) }}
                  </span>
                </td>

                <!-- Price -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-bold text-green-600">£{{ booking.service_price }}</div>
                </td>

                <!-- Payment Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getPaymentStatusClass(booking.payment_status)" class="px-3 py-1 text-xs font-semibold rounded-full">
                    {{ formatPaymentStatus(booking.payment_status) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Enhanced Empty State -->
      <div v-if="bookings.data.length === 0" class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <div class="mx-auto h-24 w-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
          <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4a4 4 0 118 0v4a6 6 0 01-12 0V7z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No bookings found</h3>
        <p class="text-gray-500 mb-6 max-w-md mx-auto">
          {{ hasFilters ? 'Try adjusting your filters to see more results.' : 'You don\'t have any customer bookings yet. Once customers start booking your services, they will appear here.' }}
        </p>
        <div v-if="hasFilters" class="flex justify-center">
          <button
            @click="clearFilters"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>Clear All Filters</span>
          </button>
        </div>
      </div>

      <!-- Enhanced Pagination -->
      <div v-if="bookings.data.length > 0" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex items-center justify-between">
          <!-- Mobile Pagination -->
          <div class="flex justify-between sm:hidden w-full">
            <a
              v-if="bookings.prev_page_url"
              :href="bookings.prev_page_url"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200 space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              <span>Previous</span>
            </a>
            <a
              v-if="bookings.next_page_url"
              :href="bookings.next_page_url"
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200 space-x-2"
            >
              <span>Next</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </a>
          </div>

          <!-- Desktop Pagination -->
          <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
            <div>
              <p class="text-sm text-gray-700">
                Showing <span class="font-medium text-gray-900">{{ bookings.from || 0 }}</span> to
                <span class="font-medium text-gray-900">{{ bookings.to || 0 }}</span> of
                <span class="font-medium text-gray-900">{{ bookings.total }}</span> results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
                <template v-for="link in bookings.links" :key="link.label">
                  <component
                    :is="link.url ? 'a' : 'span'"
                    :href="link.url"
                    v-html="link.label"
                    :class="[
                      'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-200',
                      link.active
                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                      !link.url ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
                    ]"
                  >
                  </component>
                </template>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SidebarLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

// Props
const props = defineProps({
  bookings: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Reactive filter state
const searchQuery = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')
const dateFilter = ref(props.filters.date || '')
const showStatusDropdown = ref(false)

// Computed
const hasFilters = computed(() => {
  return searchQuery.value || statusFilter.value || dateFilter.value
})

// Methods
const selectStatus = (status) => {
  statusFilter.value = status
  showStatusDropdown.value = false
}

const applyFilters = () => {
  const params = {}

  if (searchQuery.value) params.search = searchQuery.value
  if (statusFilter.value) params.status = statusFilter.value
  if (dateFilter.value) params.date = dateFilter.value

  router.get(route('bookings'), params, {
    preserveState: true,
    preserveScroll: true
  })
}

const clearFilters = () => {
  // Reset all filter values
  searchQuery.value = ''
  statusFilter.value = ''
  dateFilter.value = ''
  showStatusDropdown.value = false

  // Navigate to the route without any parameters
  router.get(route('bookings'), {}, {
    preserveState: true,
    preserveScroll: true
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')
}

const formatPaymentStatus = (status) => {
  if (!status) return 'N/A'
  return status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    'confirmed': 'bg-blue-100 text-blue-800 border border-blue-200',
    'completed': 'bg-green-100 text-green-800 border border-green-200',
    'cancelled': 'bg-red-100 text-red-800 border border-red-200',
    'rescheduled': 'bg-purple-100 text-purple-800 border border-purple-200'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
}

const getPaymentStatusClass = (status) => {
  const classes = {
    'paid': 'bg-green-100 text-green-800 border border-green-200',
    'partially_paid': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    'unpaid': 'bg-red-100 text-red-800 border border-red-200'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
}

const exportToPDF = () => {
  // Build query params from current filters
  const params = new URLSearchParams()

  if (searchQuery.value) params.append('search', searchQuery.value)
  if (statusFilter.value) params.append('status', statusFilter.value)
  if (dateFilter.value) params.append('date', dateFilter.value)

  // Create download URL
  const exportUrl = `/barber/bookings/export-pdf?${params.toString()}`

  // Create temporary link and trigger download
  const link = document.createElement('a')
  link.href = exportUrl
  link.download = `bookings-report-${new Date().toISOString().split('T')[0]}.pdf`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}
</script>
