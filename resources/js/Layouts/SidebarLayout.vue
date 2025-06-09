<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-52 bg-gray-800 text-white p-4 space-y-4">
      <h2 class="text-lg font-bold">Mickyes</h2>
      <div class="border-b border-gray-700 my-4"></div>
      <ul class="space-y-4">
        <template v-if="user.role === 'customer'">
          <li><Link :href="route('customer.dashboard')" class="flex items-center space-x-2 hover:text-yellow-300"><HomeIcon class="w-5 h-5" /><span>Dashboard</span></Link></li>
          <li><Link :href="route('booking.create')" class="flex items-center space-x-2 hover:text-yellow-300"><CalendarIcon class="w-5 h-5" /><span>Book Appointment</span></Link></li>
          <li><Link :href="route('customer.bookings')" class="flex items-center space-x-2 hover:text-yellow-300"><UsersIcon class="w-5 h-5" /><span>View Bookings</span></Link></li>
          <li><Link :href="route('transformations.index')" class="flex items-center space-x-2 hover:text-yellow-300"><SparklesIcon class="w-5 h-5" /><span>My Transformations</span></Link></li>
        </template>
        <template v-if="user.role === 'barber'">
          <li><Link href="/barber/dashboard" class="flex items-center space-x-2 hover:text-yellow-300"><HomeIcon class="w-5 h-5" /><span>Dashboard</span></Link></li>
          <li><Link href="/barber/appointments" class="flex items-center space-x-2 hover:text-yellow-300"><CalendarIcon class="w-5 h-5" /><span>Appointments</span></Link></li>
          <li><Link href="/barber/bookings" class="flex items-center space-x-2 hover:text-yellow-300"><UsersIcon class="w-5 h-5" /><span>View Bookings</span></Link></li>
          <li><Link href="/barber/transformations" class="flex items-center space-x-2 hover:text-yellow-300"><SparklesIcon class="w-5 h-5" /><span>Transformations</span></Link></li>
        </template>
        <template v-if="user.role === 'admin'">
          <li><Link href="/admin/dashboard" class="flex items-center space-x-2 hover:text-yellow-300"><HomeIcon class="w-5 h-5" /><span>Dashboard</span></Link></li>
          <li><Link href="/admin/barbers/pending" class="flex items-center space-x-2 hover:text-yellow-300"><UserPlusIcon class="w-5 h-5" /><span>Approve Barbers</span></Link></li>
          <li><Link href="/admin/users" class="flex items-center space-x-2 hover:text-yellow-300"><UsersIcon class="w-5 h-5" /><span>Manage Users</span></Link></li>
        </template>
      </ul>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
      <!-- Top Navbar -->
      <nav class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-300 shadow-md relative">
        <div class="flex items-center space-x-4">
          <Link :href="route('home')" class="text-xl font-bold text-gray-900">MickyesBarber</Link>
        </div>
        <div class="flex items-center space-x-6">
          <!-- Notification Bell -->
          <div class="relative notification-dropdown">
            <button
              @click="toggleNotifications"
              class="focus:outline-none"
            >
              <BellIcon class="w-6 h-6 text-gray-500 cursor-pointer" />
              <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"
              ></span>
            </button>

            <!-- Notifications Dropdown -->
            <div
              v-show="showNotifications"
              class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50"
              @click.stop
            >
              <div class="p-4 border-b">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg font-semibold">Notifications</h3>
                  <button
                    v-if="unreadCount > 0"
                    @click="markAllAsRead"
                    class="text-sm text-blue-600 hover:text-blue-800"
                  >
                    Mark all as read
                  </button>
                </div>
              </div>

              <div class="max-h-96 overflow-y-auto">
                <div v-if="notifications.length === 0" class="p-4 text-center text-gray-500">
                  No notifications
                </div>
                <div v-else class="divide-y">
                  <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="p-4 hover:bg-gray-50 cursor-pointer"
                    :class="{ 'bg-blue-50': !notification.is_read }"
                    @click="markAsRead(notification)"
                  >
                    <div class="flex justify-between items-start">
                      <div class="flex items-start space-x-4">
                        <component
                          :is="getNotificationStyle(notification.type).icon"
                          :class="['w-7 h-7 mt-1', getNotificationStyle(notification.type).color]"
                        />
                        <div>
                          <p class="font-medium">{{ notification.title }}</p>
                          <p class="text-sm text-gray-600">{{ notification.message }}</p>
                          <p class="text-xs text-gray-400 mt-1">
                            {{ new Date(notification.created_at).toLocaleString() }}
                          </p>
                        </div>
                      </div>
                      <span v-if="!notification.is_read" class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- User Dropdown -->
          <div class="relative" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 focus:outline-none">
              <img v-if="user.profile_photo_url" :src="user.profile_photo_url" alt="avatar" class="w-8 h-8 rounded-full object-cover border" />
              <span class="text-gray-700 font-medium">{{ user.name }}</span>
              <svg class="w-4 h-4 ml-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <transition name="fade">
              <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg z-50">
                <Link :href="route('profile.edit')" class="flex items-center space-x-2 px-4 py-2 text-gray-700 hover:bg-gray-100">
                  <UserIcon class="w-5 h-5" />
                  <span>Profile</span>
                </Link>
                <Link :href="route('logout')" method="post" as="button" class="flex items-center space-x-2 w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                  <ArrowLeftOnRectangleIcon class="w-5 h-5" />
                  <span>Logout</span>
                </Link>
              </div>
            </transition>
          </div>
        </div>
      </nav>
      <!-- Main Content -->
      <main class="flex-1 bg-white p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import { ref, onMounted, computed, onBeforeUnmount } from 'vue'
import axios from 'axios'
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
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const user = usePage().props.auth.user
const dropdownOpen = ref(false)
const showNotifications = ref(false)
const notifications = ref([])

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.is_read).length
})

// Toggle notifications dropdown
const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  const notificationDropdown = event.target.closest('.notification-dropdown')
  if (showNotifications.value && !notificationDropdown) {
    showNotifications.value = false
  }
}

onMounted(async () => {
  try {
    await axios.get('/sanctum/csrf-cookie')
    await fetchNotifications()
    document.addEventListener('click', handleClickOutside)
  } catch (err) {
    console.error('Failed to init Sanctum CSRF or fetch notifications', err)
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
