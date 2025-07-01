<template>
  <header class="bg-gray-200 shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Main navigation">
      <div class="flex justify-between h-16 items-center">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <Link :href="route('home')" class="flex items-center hover:opacity-75 transition-opacity duration-150">
            <span class="text-xl font-bold text-gray-900">Mickyes Coiffure</span>
          </Link>
        </div>

        <!-- Navigation Links -->
        <div class="hidden md:flex md:items-center md:space-x-6">
          <!-- Always visible links -->
          <Link
            :href="route('home')"
            :class="[$page.component === 'HomePage' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700']"
          >
            Home
          </Link>
          <Link
            :href="route('services')"
            :class="[$page.component === 'ServicePage' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700']"
          >
            Services
          </Link>
          <Link
            :href="route('about')"
            :class="[$page.component === 'AboutPage' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700']"
          >
            About
          </Link>
          <Link
            :href="route('contact')"
            :class="[$page.component === 'ContactPage' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700']"
          >
            Contact
          </Link>

          <!-- Conditional auth links -->
          <template v-if="!auth.user">
            <Link
              :href="route('login')"
              :class="[$page.component === 'LoginPage' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700']"
            >
              Log in
            </Link>
            <Link
              :href="route('register')"
              :class="[$page.component === 'RegisterPage' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700']"
            >
              Register
            </Link>
          </template>
          <template v-else>
            <Link
              :href="route('dashboard')"
              :class="[$page.component === 'DashboardPage' ? 'text-gray-900 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700']"
            >
              Dashboard
            </Link>
          </template>
        </div>

        <!-- Mobile menu button -->
        <button
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden rounded-md p-2 text-gray-600 hover:bg-gray-300"
          aria-expanded="false"
          :aria-label="mobileMenuOpen ? 'Close menu' : 'Open menu'"
        >
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Mobile menu -->
      <div
        v-show="mobileMenuOpen"
        class="md:hidden"
      >
        <div class="px-2 pt-2 pb-3 space-y-1">
          <!-- Mobile: Always visible links -->
          <Link
            :href="route('home')"
            :class="[
              'block px-3 py-2 rounded-md transition-colors duration-200',
              $page.component === 'HomePage'
                ? 'bg-green-50 text-green-600 font-semibold'
                : 'text-gray-700 hover:text-gray-900 hover:bg-gray-300'
            ]"
          >
            Home
          </Link>
          <Link
            :href="route('services')"
            :class="[
              'block px-3 py-2 rounded-md transition-colors duration-200',
              $page.component === 'ServicePage'
                ? 'bg-green-50 text-green-600 font-semibold'
                : 'text-gray-700 hover:text-gray-900 hover:bg-gray-300'
            ]"
          >
            Services
          </Link>
          <Link
            :href="route('about')"
            :class="[
              'block px-3 py-2 rounded-md transition-colors duration-200',
              $page.component === 'AboutPage'
                ? 'bg-green-50 text-green-600 font-semibold'
                : 'text-gray-700 hover:text-gray-900 hover:bg-gray-300'
            ]"
          >
            About
          </Link>
          <Link
            :href="route('contact')"
            :class="[
              'block px-3 py-2 rounded-md transition-colors duration-200',
              $page.component === 'ContactPage'
                ? 'bg-green-50 text-green-600 font-semibold'
                : 'text-gray-700 hover:text-gray-900 hover:bg-gray-300'
            ]"
          >
            Contact
          </Link>

          <!-- Mobile: Conditional auth links -->
          <template v-if="!auth.user">
            <Link
              :href="route('login')"
              :class="[
                'block px-3 py-2 rounded-md transition-colors duration-200',
                $page.component === 'LoginPage'
                  ? 'bg-green-50 text-green-600 font-semibold'
                  : 'text-gray-700 hover:text-gray-900 hover:bg-gray-300'
              ]"
            >
              Log in
            </Link>
            <Link
              :href="route('register')"
              :class="[
                'block px-3 py-2 rounded-md transition-colors duration-200',
                $page.component === 'RegisterPage'
                  ? 'bg-green-50 text-green-600 font-semibold'
                  : 'text-gray-700 hover:text-gray-900 hover:bg-gray-300'
              ]"
            >
              Register
            </Link>
          </template>
          <template v-else>
            <Link
              :href="route('dashboard')"
              :class="[
                'block px-3 py-2 rounded-md transition-colors duration-200',
                $page.component === 'DashboardPage'
                  ? 'bg-green-50 text-green-600 font-semibold'
                  : 'text-gray-700 hover:text-gray-900 hover:bg-gray-300'
              ]"
            >
              Dashboard
            </Link>
          </template>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  auth: {
    type: Object,
    required: true
  }
});

const mobileMenuOpen = ref(false)

// Helper function to determine if a route is active
const isRoute = (name) => {
  return route().current(name)
}

// Helper function to determine if a hash link is active
const isActiveHash = (hash) => {
  if (typeof window !== 'undefined') {
    return window.location.hash === hash
  }
  return false
}

// Computed property for nav link classes
const navLinkClasses = (isActive) => {
  return [
    'transition-colors duration-200',
    isActive
      ? 'text-green-600 font-semibold'
      : 'text-gray-700 hover:text-gray-900'
  ]
}
</script>
