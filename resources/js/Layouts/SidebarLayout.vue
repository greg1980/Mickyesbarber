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
          <div class="relative">
            <BellIcon class="w-6 h-6 text-gray-500" />
            <span class="absolute -top-1 -right-1 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
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
import { ref } from 'vue'
import { HomeIcon, CalendarIcon, SparklesIcon, UserPlusIcon, UsersIcon, UserIcon, ArrowLeftOnRectangleIcon, BellIcon } from '@heroicons/vue/24/outline'

const user = usePage().props.auth.user
const dropdownOpen = ref(false)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.15s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
