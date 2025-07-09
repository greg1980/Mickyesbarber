<template>
  <SidebarLayout>
    <div class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
      <!-- Success Message -->
      <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span class="font-medium">{{ $page.props.flash.success }}</span>
        </div>
      </div>
      <!-- Modern Premium Header -->
      <div class="mb-8">
        <div class="bg-gradient-to-r from-gray-700 via-gray-800 to-gray-900 rounded-2xl shadow-xl p-4 sm:p-8 text-white relative overflow-hidden">
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

          <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3 sm:space-x-4">
              <div class="h-12 w-12 sm:h-16 sm:w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                <UserGroupIcon class="w-7 h-7 sm:w-8 sm:h-8 text-white" />
              </div>
              <div>
                <h1 class="text-2xl sm:text-4xl font-bold mb-1 sm:mb-2 leading-tight">Barber Management</h1>
                <p class="text-gray-100 text-base sm:text-lg">Professional Team Administration</p>
                <p class="text-gray-200 text-xs sm:text-sm mt-1">Manage your barber team, schedules, and performance</p>
              </div>
            </div>

            <!-- Stats Badge -->
            <div class="hidden lg:block">
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/30">
                <div class="text-center">
                  <div class="text-2xl font-bold">{{ barbers.length }}</div>
                  <div class="text-xs text-gray-200 uppercase tracking-wider">Active Barbers</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modern Search and Filter Bar -->
      <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0 gap-2">
          <!-- Search Bar -->
          <div class="relative flex-1 max-w-md w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search barbers by name or email..."
              class="block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base"
            >
          </div>

          <!-- Filter and Actions -->
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <select v-model="statusFilter" class="px-3 py-2 sm:px-4 sm:py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="pending">Pending</option>
              <option value="declined">Declined</option>
              <option value="deleted">Deleted Users</option>
            </select>

            <button @click="showAddBarberModal = true" class="flex items-center justify-center space-x-2 px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md w-full sm:w-auto text-sm sm:text-base">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span>Add Barber</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Premium Barber Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div v-for="barber in filteredBarbers" :key="barber.id"
             class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300">

          <!-- Card Header with Profile -->
          <div class="relative p-3 sm:p-4 md:p-6 bg-gradient-to-br from-gray-50 to-white">
            <div class="flex flex-col xs:flex-row items-start xs:items-center space-y-2 xs:space-y-0 xs:space-x-3 sm:space-x-4 w-full">
              <div class="relative flex-shrink-0">
                <img :src="barber.user?.profile_photo_url || 'https://ui-avatars.com/api/?name=' + (barber.user?.name || 'Barber')"
                     :alt="barber.user?.name ? 'Profile photo of ' + barber.user.name : 'Barber profile photo'"
                     class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border mr-2 sm:mr-3" />
                <!-- Dynamic Status Indicator -->
                <div class="absolute -top-1 -right-1 h-5 w-5 border-2 border-white rounded-full"
                     :class="{
                       'bg-green-500': barber.is_approved === 1 || barber.is_approved === true,
                       'bg-yellow-500': barber.is_approved === null,
                       'bg-red-500': barber.is_approved === 0 || barber.is_approved === false
                     }"></div>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="text-base sm:text-lg md:text-xl font-bold transition-colors duration-200 truncate"
                    :class="barber.user?.deleted_at ? 'text-red-600 line-through' : 'text-gray-900 group-hover:text-gray-700'">
                  {{ barber.user?.name || 'N/A' }}
                  <span v-if="barber.user?.deleted_at" class="text-xs font-normal text-red-500 ml-2">(User Deleted)</span>
                </h3>
                <p class="font-medium text-xs sm:text-base break-all" :class="barber.user?.deleted_at ? 'text-red-500' : 'text-gray-600'">
                  {{ barber.user?.email || 'N/A' }}
                </p>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mt-2 gap-1 sm:gap-0 w-full">
                  <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-xs sm:text-sm font-bold text-gray-700">4.8</span>
                    <span class="text-xs text-gray-500 ml-1">(127 reviews)</span>
                  </div>
                  <!-- Status Badge -->
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': barber.is_approved === 1 || barber.is_approved === true,
                          'bg-yellow-100 text-yellow-800': barber.is_approved === null,
                          'bg-red-100 text-red-800': barber.is_approved === 0 || barber.is_approved === false
                        }">
                    <span v-if="barber.is_approved === 1 || barber.is_approved === true">Active</span>
                    <span v-else-if="barber.is_approved === null">Pending</span>
                    <span v-else>Declined</span>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card Stats -->
          <div class="px-4 py-3 sm:px-6 sm:py-4 bg-gray-50/50">
            <div class="grid grid-cols-3 gap-2 sm:gap-4 text-center">
              <div>
                <div class="text-base sm:text-lg font-bold text-blue-600">42</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider">This Month</div>
              </div>
              <div>
                <div class="text-base sm:text-lg font-bold text-green-600">287</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider">Total Cuts</div>
              </div>
              <div>
                <div class="text-base sm:text-lg font-bold text-purple-600">£2.1k</div>
                <div class="text-xs text-gray-500 uppercase tracking-wider">Revenue</div>
              </div>
            </div>
          </div>

          <!-- Card Actions -->
          <div class="p-4 sm:p-6 bg-white">
            <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full">
              <button @click="openScheduleModal(barber)"
                      class="w-full sm:flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Schedule
              </button>
              <button @click="openBookingsModal(barber)"
                      class="w-full sm:flex-1 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Bookings
              </button>
            </div>
            <button v-if="!barber.user?.deleted_at" @click="revokeApproval(barber)"
                    class="w-full mt-2 px-4 py-2 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
              </svg>
              Revoke Approval
            </button>
            <div v-else class="w-full mt-2 px-4 py-2 bg-gray-100 text-gray-500 rounded-lg font-medium text-sm text-center">
              User Account Deleted
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredBarbers.length === 0" class="text-center py-16">
        <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
          <UserGroupIcon class="h-12 w-12 text-gray-400" />
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No barbers found</h3>
        <p class="text-gray-500 mb-6">{{ searchQuery ? 'Try adjusting your search criteria' : 'Get started by adding your first barber' }}</p>
                         <button @click="showAddBarberModal = true" class="inline-block px-6 py-3 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md">
          Add First Barber
        </button>
      </div>

      <!-- Enhanced Schedule Modal -->
      <div v-if="showScheduleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <!-- Modal Header -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100 rounded-t-2xl">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                <div class="h-10 w-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                Weekly Schedule
              </h3>
              <button @click="closeScheduleModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Modal Content -->
          <div class="p-6">
            <div class="space-y-3">
              <div v-for="s in schedule" :key="s.day_of_week"
                   class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                <div class="flex items-center space-x-3">
                  <div class="h-3 w-3 rounded-full" :class="s.is_available ? 'bg-green-500' : 'bg-red-500'"></div>
                  <span class="font-semibold text-gray-900 capitalize">{{ s.day_of_week }}</span>
                </div>
                <div class="text-right">
                  <div v-if="s.is_available" class="text-sm font-medium text-gray-900">
                    {{ s.start_time }} - {{ s.end_time }}
                  </div>
                  <div v-else class="text-sm text-red-600 font-medium">Unavailable</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Manage Bookings Modal -->
      <div v-if="showBookingsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
          <!-- Modal Header -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-100 rounded-t-2xl">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                <div class="h-10 w-10 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                </div>
                Booking Management
              </h3>
              <button @click="closeBookingsModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Modal Content -->
          <div class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
              <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">This Week</p>
                    <p class="text-3xl font-bold text-blue-900">24</p>
                    <p class="text-sm text-blue-700">Bookings</p>
                  </div>
                  <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                </div>
              </div>

              <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-green-600 uppercase tracking-wider">Revenue</p>
                    <p class="text-3xl font-bold text-green-900">£2,150</p>
                    <p class="text-sm text-green-700">This month</p>
                  </div>
                  <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                  </div>
                </div>
              </div>

              <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-purple-600 uppercase tracking-wider">Completion</p>
                    <p class="text-3xl font-bold text-purple-900">94%</p>
                    <p class="text-sm text-purple-700">Success rate</p>
                  </div>
                  <div class="h-12 w-12 bg-purple-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Bookings Table -->
            <div class="bg-gray-50 rounded-xl p-6">
              <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Recent Bookings
              </h4>
              <div class="block md:hidden space-y-4">
                <div v-for="booking in bookings" :key="booking.id" class="rounded-lg bg-white shadow p-4 flex flex-col gap-2">
                  <div class="flex items-center gap-3">
                    <div class="h-8 w-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                      {{ booking.customer_name.charAt(0) }}
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ booking.customer_name }}</div>
                      <div class="text-xs text-gray-500">{{ formatDate(booking.date) }} • {{ formatTime(booking.time) }}</div>
                    </div>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="text-sm font-semibold text-green-600">£{{ booking.price }}</div>
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                  </div>
                </div>
              </div>
              <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-sm">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100" @click="toggleSort">
                        Date
                        <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                      </th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr v-for="booking in bookings" :key="booking.id" class="hover:bg-gray-50 transition-colors duration-200">
                      <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="h-8 w-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ booking.customer_name.charAt(0) }}
                          </div>
                          <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">{{ booking.customer_name }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ formatDate(booking.date) }}</td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatTime(booking.time) }}</td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">£{{ booking.price }}</td>
                      <td class="px-4 py-4 whitespace-nowrap">
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                          Completed
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Pagination -->
              <div class="mt-6 flex justify-center">
                <nav class="flex items-center space-x-2">
                  <button v-for="page in totalPages" :key="page" @click="changePage(page)"
                          class="px-4 py-2 text-sm rounded-lg transition-all duration-200"
                          :class="currentPage === page
                            ? 'bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-md'
                            : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'">
                    {{ page }}
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Barber Modal -->
      <BarberRegisterModal
        :show="showAddBarberModal"
        @close="showAddBarberModal = false"
        @success="handleBarberAdded"
      />
    </div>
  </SidebarLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import BarberRegisterModal from '@/Components/BarberRegisterModal.vue'
import axios from 'axios'
import { UserGroupIcon } from '@heroicons/vue/24/outline'

const barbers = ref([])
const searchQuery = ref('')
const statusFilter = ref('')
const showScheduleModal = ref(false)
const schedule = ref([])
const showAddBarberModal = ref(false)

// Manage Bookings modal state
const showBookingsModal = ref(false)
const selectedBarber = ref(null)
const bookings = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const sortOrder = ref('desc')

// Computed property for filtered barbers
const filteredBarbers = computed(() => {
  let filtered = barbers.value

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(barber =>
      barber.user?.name?.toLowerCase().includes(query) ||
      barber.user?.email?.toLowerCase().includes(query)
    )
  }

  // Filter by status (based on is_approved field and user deletion status)
  if (statusFilter.value) {
    filtered = filtered.filter(barber => {
      if (statusFilter.value === 'active') {
        return (barber.is_approved === 1 || barber.is_approved === true) && !barber.user?.deleted_at
      } else if (statusFilter.value === 'pending') {
        return barber.is_approved === null && !barber.user?.deleted_at
      } else if (statusFilter.value === 'declined') {
        return (barber.is_approved === 0 || barber.is_approved === false) && !barber.user?.deleted_at
      } else if (statusFilter.value === 'deleted') {
        return barber.user?.deleted_at
      }
      return true
    })
  }

  return filtered
})

onMounted(async () => {
  const res = await axios.get('/admin/barbers-manage')
  barbers.value = res.data.barbers || []
})

async function openScheduleModal(barber) {
  const res = await axios.get(`/admin/barbers/${barber.id}/schedule`)
  schedule.value = res.data
  showScheduleModal.value = true
}

function closeScheduleModal() {
  showScheduleModal.value = false
  schedule.value = []
}

async function openBookingsModal(barber) {
  selectedBarber.value = barber
  showBookingsModal.value = true

  // Fetch bookings data for the current page
  const res = await axios.get(`/admin/barbers/${barber.id}/bookings?page=${currentPage.value}`)
  bookings.value = res.data.data || []
  totalPages.value = res.data.last_page || 1
}

function closeBookingsModal() {
  showBookingsModal.value = false
  selectedBarber.value = null
  bookings.value = []
}

function toggleSort() {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  // Add sorting logic here
}

async function changePage(page) {
  currentPage.value = page
  if (!selectedBarber.value) return
  // Fetch bookings data for the new page
  const res = await axios.get(`/admin/barbers/${selectedBarber.value.id}/bookings?page=${page}`)
  bookings.value = res.data.data || []
  totalPages.value = res.data.last_page || 1
}

async function revokeApproval(barber) {
  if (confirm('Are you sure you want to revoke approval for this barber?')) {
    try {
      await axios.post(`/admin/barbers/${barber.id}/revoke-approval`)
      // Refresh the barbers list
      await refreshBarbersList()
    } catch (error) {
      console.error('Error revoking approval:', error)
    }
  }
}

async function handleBarberAdded() {
  // Refresh the barbers list when a new barber is added
  await refreshBarbersList()
}

async function refreshBarbersList() {
  try {
    const res = await axios.get('/admin/barbers-manage')
    barbers.value = res.data.barbers || []
  } catch (error) {
    console.error('Error refreshing barbers list:', error)
  }
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-GB', { year: 'numeric', month: 'short', day: 'numeric' })
}

function formatTime(timeStr) {
  if (!timeStr) return ''
  // If timeStr is a full ISO string, extract time part
  const t = new Date(timeStr)
  if (!isNaN(t)) {
    return t.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' })
  }
  // If it's just HH:MM:SS, format manually
  return timeStr.slice(0,5)
}
</script>

<style scoped>
@media (max-width: 640px) {
  .modal-content {
    padding: 1rem !important;
    max-width: 98vw !important;
  }
}
</style>
