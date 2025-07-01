<template>
  <div class="flex min-h-screen">
    <!-- Mobile overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-30 bg-black bg-opacity-50 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed lg:static inset-y-0 left-0 z-40 w-64 bg-gray-700 text-white p-4 space-y-4 transform transition-transform duration-300 ease-in-out lg:transform-none',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <div class="flex items-center justify-between">
        <div class="flex flex-col">
          <h2 class="text-lg font-bold">Mickyes Coiffure</h2>
          <!-- Role-specific badge -->
          <div v-if="user.role === 'barber'" class="flex items-center space-x-1 mt-1">
            <div class="flex items-center space-x-1 bg-blue-600 px-2 py-0.5 rounded-full">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4a4 4 0 118 0v4a6 6 0 01-12 0V7z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18"/>
              </svg>
              <span class="text-xs font-medium text-white">Barber</span>
            </div>
          </div>
          <div v-else-if="user.role === 'admin'" class="flex items-center space-x-1 mt-1">
            <div class="flex items-center space-x-1 bg-red-600 px-2 py-0.5 rounded-full">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
              <span class="text-xs font-medium text-white">Admin</span>
            </div>
          </div>
          <div v-else-if="user.role === 'customer'" class="flex items-center space-x-1 mt-1">
            <div class="flex items-center space-x-1 bg-green-600 px-2 py-0.5 rounded-full">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <span class="text-xs font-medium text-white">Customer</span>
            </div>
          </div>
        </div>
        <button
          @click="sidebarOpen = false"
          class="lg:hidden text-white hover:text-gray-300"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="border-b border-gray-500 my-4"></div>
      <ul class="space-y-0">
        <template v-if="user.role === 'customer'">
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('customer.dashboard')" :class="isActive('/customer/dashboard') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <HomeIcon class="w-5 h-5" />
              <span>Dashboard</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('booking.create')" :class="isActive('/booking/create') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <CalendarIcon class="w-5 h-5" />
              <span>Book Appointment</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('customer.bookings')" :class="isActive('/customer/bookings') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <UsersIcon class="w-5 h-5" />
              <span>View Bookings</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('transformations.index')" :class="isActive('/transformations') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <SparklesIcon class="w-5 h-5" />
              <span>My Transformations</span>
            </Link>
          </li>
        </template>
        <template v-if="user.role === 'barber'">
          <li class="border-b border-gray-500 last:border-b-0">
            <Link href="/barber/dashboard" :class="isActive('/barber/dashboard') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <HomeIcon class="w-5 h-5" />
              <span>Dashboard</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link href="/barber/appointments" :class="isActive('/barber/appointments') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <CalendarIcon class="w-5 h-5" />
              <span>Appointments</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link href="/barber/bookings" :class="isActive('/barber/bookings') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <UsersIcon class="w-5 h-5" />
              <span>View Bookings</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('barber.transformations')" :class="isActive('/barber/transformations') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <SparklesIcon class="w-5 h-5" />
              <span>Transformations</span>
            </Link>
          </li>
        </template>
        <template v-if="user.role === 'admin'">
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('admin.dashboard')" :class="isActive('/admin/dashboard') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <HomeIcon class="w-5 h-5" />
              <span>Dashboard</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('admin.barbers.manage')" :class="isActive('/admin/barbers') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <UserPlusIcon class="w-5 h-5" />
              <span>Manage Barbers</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('admin.users.index')" :class="isActive('/admin/users') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <UsersIcon class="w-5 h-5" />
              <span>Manage Users</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('admin.services.index')" :class="isActive('/admin/services') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <SparklesIcon class="w-5 h-5" />
              <span>Manage Services</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('booking.create')" :class="isActive('/booking/create') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <CalendarIcon class="w-5 h-5" />
              <span>Bookings</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('admin.transformations')" :class="isActive('/admin/transformations') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <SparklesIcon class="w-5 h-5" />
              <span>Transformations</span>
            </Link>
          </li>
          <li class="border-b border-gray-500 last:border-b-0">
            <Link :href="route('admin.finances')" :class="isActive('/admin/finances') ? 'flex items-center space-x-2 text-yellow-300 bg-gray-600 px-3 py-3 whitespace-nowrap border-b border-gray-500' : 'flex items-center space-x-2 hover:text-yellow-300 px-3 py-3 whitespace-nowrap'" @click="sidebarOpen = false">
              <CurrencyDollarIcon class="w-5 h-5" />
              <span>Finances</span>
            </Link>
          </li>
        </template>
      </ul>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col lg:ml-0">
      <!-- Enhanced Premium Top Navbar -->
      <nav class="flex items-center justify-between h-20 lg:h-24 px-4 lg:px-8 bg-white border-b border-gray-200 shadow-lg relative backdrop-blur-sm z-40">
        <div class="flex items-center space-x-3 lg:space-x-6">
          <!-- Enhanced Mobile menu button -->
          <button
            @click="sidebarOpen = true"
            class="lg:hidden text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <div class="flex items-center space-x-4 lg:space-x-6">
            <Link :href="route('home')" class="text-lg lg:text-2xl font-bold text-gray-900 hover:text-gray-700 transition-colors duration-200 truncate">Mickyes Coiffure</Link>
            <!-- Enhanced Role-specific dashboard indicator -->
            <div v-if="user.role === 'barber'" class="hidden sm:flex items-center space-x-3">
              <div class="w-px h-8 bg-gray-300"></div>
              <div class="flex items-center space-x-2 bg-gradient-to-r from-blue-50 to-blue-100 px-4 py-2 rounded-xl border border-blue-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4a4 4 0 118 0v4a6 6 0 01-12 0V7z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18"/>
                </svg>
                <span class="text-sm font-semibold text-blue-700">Barber Dashboard</span>
              </div>
            </div>
            <div v-else-if="user.role === 'admin'" class="hidden sm:flex items-center space-x-3">
              <div class="w-px h-8 bg-gray-300"></div>
              <div class="flex items-center space-x-2 bg-gradient-to-r from-red-50 to-red-100 px-4 py-2 rounded-xl border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-sm font-semibold text-red-700">Admin Dashboard</span>
              </div>
            </div>
            <div v-else-if="user.role === 'customer'" class="hidden sm:flex items-center space-x-3">
              <div class="w-px h-8 bg-gray-300"></div>
              <div class="flex items-center space-x-2 bg-gradient-to-r from-green-50 to-green-100 px-4 py-2 rounded-xl border border-green-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm font-semibold text-green-700">Customer Dashboard</span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center space-x-3 lg:space-x-6">
          <!-- Enhanced Notification Bell -->
          <div class="relative notification-dropdown z-[9999]">
            <button
              @click="toggleNotifications"
              class="relative focus:outline-none p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200"
            >
              <BellIcon class="w-6 h-6 text-gray-600 hover:text-gray-800 transition-colors duration-200" />
              <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse border-2 border-white shadow-lg"
              >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
              </span>
            </button>

            <!-- Enhanced Notifications Dropdown -->
            <div
              v-show="showNotifications"
              class="absolute right-0 mt-3 w-80 lg:w-96 bg-white shadow-xl z-[9999] border border-gray-200 rounded-xl overflow-hidden"
              @click.stop
            >
              <div class="p-5 border-b border-gray-100 bg-gray-50">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-bold text-gray-900">Notifications</h3>
                  <div class="flex space-x-3">
                    <button
                      v-if="unreadCount > 0"
                      @click="markAllAsRead"
                      class="text-sm font-medium text-blue-600 hover:text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-50 transition-colors duration-200"
                    >
                      Mark all as read
                    </button>
                    <button
                      v-if="readCount > 0"
                      @click="deleteAllRead"
                      class="text-sm font-medium text-red-600 hover:text-red-700 px-3 py-1 rounded-lg hover:bg-red-50 transition-colors duration-200"
                    >
                      Delete all read
                    </button>
                  </div>
                </div>
              </div>

              <div class="max-h-96 overflow-y-auto">
                <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500">
                  <div class="text-4xl mb-3">🔔</div>
                  <p class="font-medium">No notifications</p>
                  <p class="text-sm text-gray-400 mt-1">You're all caught up!</p>
                </div>
                <div v-else class="divide-y divide-gray-100">
                  <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="p-4 hover:bg-gray-50 group cursor-pointer transition-colors duration-200"
                    :class="{ 'bg-blue-50 border-l-4 border-blue-500': !notification.is_read }"
                  >
                                          <div class="flex justify-between items-start">
                        <div
                          class="flex items-start space-x-3 flex-1"
                          @click="markAsRead(notification)"
                        >
                          <div class="flex-shrink-0 mt-1">
                            <component
                              :is="getNotificationStyle(notification.type).icon"
                              :class="['w-6 h-6', getNotificationStyle(notification.type).color]"
                            />
                          </div>
                          <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm">{{ notification.title }}</p>
                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ notification.message }}</p>
                            <p class="text-xs text-gray-400 mt-2 flex items-center">
                              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                              </svg>
                              {{ new Date(notification.created_at).toLocaleString() }}
                            </p>
                          </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-3 flex-shrink-0">
                          <span v-if="!notification.is_read" class="w-3 h-3 bg-blue-500 rounded-full shadow-sm"></span>
                          <button
                            @click.stop="deleteNotification(notification)"
                            class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all duration-200 p-1 rounded hover:bg-red-50"
                            title="Delete notification"
                          >
                            <TrashIcon class="w-4 h-4" />
                          </button>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Enhanced User Dropdown -->
          <div class="relative user-dropdown z-[9999]">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 lg:space-x-3 focus:outline-none p-2 rounded-xl hover:bg-gray-100 transition-colors duration-200">
              <img v-if="user.profile_photo_url" :src="user.profile_photo_url" alt="avatar" class="w-8 h-8 lg:w-10 lg:h-10 rounded-full object-cover border-2 border-gray-200 shadow-sm" />
              <span class="text-gray-700 font-semibold text-sm lg:text-base hidden sm:block max-w-24 lg:max-w-none truncate">{{ user.name }}</span>
              <svg class="w-4 h-4 text-gray-500 flex-shrink-0 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <transition name="fade">
              <div v-if="dropdownOpen" class="absolute right-0 mt-3 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-[9999] overflow-hidden">
                <div class="p-2">
                  <Link :href="route('profile.edit')" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200" @click="dropdownOpen = false">
                    <UserIcon class="w-5 h-5 text-blue-500" />
                    <span class="font-medium">Profile Settings</span>
                  </Link>
                  <div class="border-t border-gray-100 my-2"></div>
                  <button @click="handleLogout" :disabled="isSubmitting" class="flex items-center space-x-3 w-full text-left px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600 disabled:opacity-50 rounded-lg transition-colors duration-200">
                    <ArrowLeftOnRectangleIcon class="w-5 h-5 text-red-500" />
                    <span class="font-medium">{{ isSubmitting ? 'Logging out...' : 'Logout' }}</span>
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </nav>
      <!-- Main Content -->
      <main class="flex-1 bg-white p-3 lg:p-6">
        <slot />
      </main>
      <!-- Footer -->
      <Footer />
    </div>
  </div>
</template>

<script setup>
import { usePage, Link, router } from '@inertiajs/vue3'
import { ref, onMounted, computed, onBeforeUnmount } from 'vue'
import axios from 'axios'
import Footer from '@/Components/Footer.vue'
import {
  HomeIcon,
  CalendarIcon,
  SparklesIcon,
  UserPlusIcon,
  UsersIcon,
  UserIcon,
  ArrowLeftOnRectangleIcon,
  BellIcon,
  ClockIcon,
  CurrencyDollarIcon,
  MegaphoneIcon,
  CheckCircleIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'

const user = usePage().props.auth.user
const dropdownOpen = ref(false)
const showNotifications = ref(false)
const notifications = ref([])
const sidebarOpen = ref(false)
const isSubmitting = ref(false)

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.is_read).length
})

const readCount = computed(() => {
  return notifications.value.filter(n => n.is_read).length
})

// Toggle notifications dropdown
const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  // Don't interfere with form submissions, button clicks, or Inertia links
  if (event.target.closest('form') ||
      event.target.closest('button') ||
      event.target.closest('a[href]') ||
      event.target.closest('[data-inertia]')) {
    return
  }

  const notificationDropdown = event.target.closest('.notification-dropdown')
  const userDropdown = event.target.closest('.user-dropdown')

  if (showNotifications.value && !notificationDropdown) {
    showNotifications.value = false
  }

  if (dropdownOpen.value && !userDropdown) {
    dropdownOpen.value = false
  }
}

onMounted(async () => {
  try {
    await fetchNotifications()
    document.addEventListener('click', handleClickOutside)
  } catch (err) {
    console.error('Failed to fetch notifications', err)
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

const fetchNotifications = async () => {
  try {
    const { data } = await axios.get('/notifications')
    notifications.value = data.notifications
  } catch (err) {
    console.error('Failed to fetch notifications', err)
  }
}

const markAsRead = async (notification) => {
  try {
    await axios.post(`/notifications/${notification.id}/read`)
    notification.is_read = true
  } catch (err) {
    console.error('Failed to mark notification as read', err)
  }
}

const markAllAsRead = async () => {
  try {
    await axios.post('/notifications/read-all')
    notifications.value.forEach(n => n.is_read = true)
  } catch (err) {
    console.error('Failed to mark all notifications as read', err)
  }
}

const deleteNotification = async (notification) => {
  try {
    await axios.delete(`/notifications/${notification.id}`)
    // Remove the notification from the local array
    const index = notifications.value.findIndex(n => n.id === notification.id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  } catch (err) {
    console.error('Failed to delete notification', err)
  }
}

const deleteAllRead = async () => {
  try {
    const response = await axios.delete('/notifications/read/all')
    // Remove all read notifications from the local array
    notifications.value = notifications.value.filter(n => !n.is_read)
    console.log(`Deleted ${response.data.deleted_count} read notifications`)
  } catch (err) {
    console.error('Failed to delete all read notifications', err)
  }
}

// Get icon and color for notification type
const getNotificationStyle = (type) => {
  switch (type) {
    case 'appointment':
      return {
        icon: ClockIcon,
        color: 'text-blue-500'
      }
    case 'payment':
      return {
        icon: CurrencyDollarIcon,
        color: 'text-green-500'
      }
    case 'system':
      return {
        icon: MegaphoneIcon,
        color: 'text-purple-500'
      }
    case 'check_in':
      return {
        icon: CheckCircleIcon,
        color: 'text-yellow-500'
      }
    default:
      return {
        icon: BellIcon,
        color: 'text-gray-500'
      }
  }
}

const handleLogout = async () => {
  if (isSubmitting.value) return

  isSubmitting.value = true
  dropdownOpen.value = false

  try {
    // Get a fresh CSRF token from our endpoint
    const response = await fetch('/refresh-csrf', {
      method: 'GET',
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error('Failed to refresh CSRF token')
    }

    const data = await response.json()
    const freshToken = data.csrf_token

    // Update the meta tag with the fresh token
    const metaTag = document.head.querySelector('meta[name="csrf-token"]')
    if (metaTag) {
      metaTag.content = freshToken
    }

    // Create a form and submit it directly
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = route('logout')

    const tokenInput = document.createElement('input')
    tokenInput.type = 'hidden'
    tokenInput.name = '_token'
    tokenInput.value = freshToken
    form.appendChild(tokenInput)

    document.body.appendChild(form)
    form.submit()

  } catch (error) {
    console.error('Logout error:', error)
    // Fallback: try direct form submission with current token
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = route('logout')

    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content
    if (csrfToken) {
      const tokenInput = document.createElement('input')
      tokenInput.type = 'hidden'
      tokenInput.name = '_token'
      tokenInput.value = csrfToken
      form.appendChild(tokenInput)
    }

    document.body.appendChild(form)
    form.submit()
  }
}

const isActive = (route) => {
  const currentUrl = usePage().url
  // For exact matches
  if (currentUrl === route) {
    return true
  }
  // For partial matches (e.g., /admin/users should match /admin/users and /admin/users/*)
  if (currentUrl.startsWith(route) && route !== '/') {
    return true
  }
  return false
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.15s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
