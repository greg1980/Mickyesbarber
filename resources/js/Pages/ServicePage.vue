<template>
  <Head title="Services">
    <meta name="description" content="Explore our full range of barbering services at Mickyes Coiffure. Haircuts, beard grooming, and more in Newcastle." />
    <meta property="og:title" content="Barbering Services - Mickyes Coiffure Newcastle" />
    <meta property="og:description" content="Discover professional haircuts, beard grooming, and premium barbering services at Mickyes Coiffure, Newcastle." />
    <meta property="og:image" content="/images/hero/pexels-photo-2076932.webp" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://mickyesbarbers.com/services" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Barbering Services - Mickyes Coiffure Newcastle" />
    <meta name="twitter:description" content="See our full list of services and book your next appointment online." />
    <meta name="twitter:image" content="/images/hero/pexels-photo-2076932.webp" />
  </Head>
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <main>
      <!-- Hero Section -->
      <section class="relative bg-white overflow-hidden">
        <div class="absolute inset-0">
          <div class="h-full w-full relative overflow-hidden">
            <picture>
              <source srcset="/images/hero/pexels-photo-2076932.webp" type="image/webp" />
              <img
                src="/images/hero/pexels-photo-2076932.jpeg"
                alt="Barber Services"
                class="w-full h-full object-cover object-center"
              />
            </picture>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
          </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
              Our Services
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-100 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
              Professional barbering services tailored to your style
            </p>
          </div>
        </div>
      </section>

      <!-- Services Grid -->
      <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            <!-- Dynamic Service Cards -->
            <div v-for="service in services" :key="service.id" class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <div :class="getServiceIconStyle(service.slug)" class="w-12 h-12 rounded-full flex items-center justify-center mr-4">
                    <component :is="getServiceIcon(service.icon)" class="h-6 w-6 text-white" />
                  </div>
                  <h3 class="text-xl font-bold text-gray-900">{{ service.name }}</h3>
                </div>
                <p class="text-gray-600 mb-4">
                  {{ service.description }}
                </p>
                <div class="mb-4 space-y-2">
                  <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ getServiceDuration(service.slug) }}
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ getServiceIncludes(service.slug) }}
                  </div>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900">£{{ formatPrice(service.price) }}</span>
                  <Link
                    :href="route('booking.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                  >
                    Book Now
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Additional Value Section -->
          <div class="mt-16 text-center">
                          <h3 class="text-2xl font-bold text-gray-900 mb-8">Why Choose Mickyes Coiffure?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mb-4 shadow-lg shadow-blue-200 transform transition-all duration-300 hover:scale-110 hover:shadow-xl">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Expert Barbers</h4>
                <p class="text-gray-600 text-center">Licensed professionals with years of experience</p>
              </div>
              <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mb-4 shadow-lg shadow-purple-200 transform transition-all duration-300 hover:scale-110 hover:shadow-xl">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Flexible Scheduling</h4>
                <p class="text-gray-600 text-center">Same-day appointments and convenient booking</p>
              </div>
              <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center mb-4 shadow-lg shadow-emerald-200 transform transition-all duration-300 hover:scale-110 hover:shadow-xl">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Satisfaction Guaranteed</h4>
                <p class="text-gray-600 text-center">We're not happy unless you're happy with your cut</p>
              </div>
            </div>

            <div class="mt-12 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-8">
              <h4 class="text-xl font-bold text-gray-900 mb-4">Ready to Book Your Appointment?</h4>
              <p class="text-gray-600 mb-6">Join hundreds of satisfied customers who trust Mickyes Coiffure for their grooming needs.</p>
              <Link
                :href="route('booking.create')"
                class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                </svg>
                Book Your Appointment Today
              </Link>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
  <Footer />
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navigation from '@/Components/Navigation.vue'
import Footer from '@/Components/Footer.vue'
import {
  ScissorsIcon,
  UserGroupIcon,
  SwatchIcon,
  SparklesIcon,
  HomeIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  services: Array
})

// Icon mapping for dynamic icons
const getServiceIcon = (iconName) => {
  const iconMap = {
    'ScissorsIcon': ScissorsIcon,
    'UserGroupIcon': UserGroupIcon,
    'SwatchIcon': SwatchIcon,
    'SparklesIcon': SparklesIcon,
    'HomeIcon': HomeIcon
  }
  return iconMap[iconName] || ScissorsIcon // Default to scissors if no match
}

// Duration mapping based on service slug
const getServiceDuration = (slug) => {
  const durationMap = {
    'classic-cut': '45-60 minutes',
    'kids-cut': '30-45 minutes',
    'hair-color': '90-120 minutes',
    'beard-grooming': '20-30 minutes',
    'mobile-service': '60-90 minutes',
    'special-treatment': '45-75 minutes'
  }
  return durationMap[slug] || '30-60 minutes'
}

// Service includes mapping based on service slug
const getServiceIncludes = (slug) => {
  const includesMap = {
    'classic-cut': 'Includes wash & style',
    'kids-cut': 'Ages 2-12 • Fun environment',
    'hair-color': 'Consultation included',
    'beard-grooming': 'Hot towel treatment included',
    'mobile-service': '10-mile radius • Travel fee included',
    'special-treatment': 'Premium treatments & massage'
  }
  return includesMap[slug] || 'Professional service'
}

// Format price to handle both integers and decimals
const formatPrice = (price) => {
  const num = parseFloat(price)
  return num % 1 === 0 ? num.toString() : num.toFixed(2)
}

// Service icon color styling based on service slug
const getServiceIconStyle = (slug) => {
  const colorMap = {
    'classic-cut': 'bg-gradient-to-br from-blue-500 to-blue-600 shadow-blue-200', // Professional blue
    'kids-cut': 'bg-gradient-to-br from-orange-400 to-orange-500 shadow-orange-200', // Fun orange
    'hair-color': 'bg-gradient-to-br from-purple-500 to-purple-600 shadow-purple-200', // Creative purple
    'beard-grooming': 'bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-emerald-200', // Natural green
    'mobile-service': 'bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-indigo-200', // Mobility indigo
    'special-treatment': 'bg-gradient-to-br from-rose-500 to-rose-600 shadow-rose-200' // Luxury rose
  }
  const baseStyle = 'shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-xl'
  const serviceColor = colorMap[slug] || 'bg-gradient-to-br from-gray-500 to-gray-600 shadow-gray-200'
  return `${serviceColor} ${baseStyle}`
}
</script>
