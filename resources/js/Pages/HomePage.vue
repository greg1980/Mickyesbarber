<template>
  <Head title="Home" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <main>
      <section class="relative bg-white overflow-hidden h-[50vh] md:h-[65vh] lg:h-[75vh] xl:h-[80vh]">
        <div class="absolute inset-0">
          <div class="h-full w-full relative overflow-hidden">
            <img
              src="/images/hero/barber-hero.jpg"
              alt="Barber Shop Background"
              class="w-full h-full object-cover object-center sticky top-0 scale-100"
              style="object-position: center 40%;"
            />
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
          </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
          <div class="flex items-center h-full">
            <div class="text-center lg:text-left w-full py-12">
              <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
                <span class="block">Welcome to</span>
                                        <span class="block">Mickyes Coiffure</span>
              </h1>
              <p class="mt-3 text-base text-gray-100 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                Professional barbering services with style and precision.
              </p>
              <div class="mt-4 flex flex-col sm:flex-row sm:justify-center lg:justify-start space-y-2 sm:space-y-0 sm:space-x-6">
                <div class="flex items-center text-gray-200 text-sm">
                  <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  4.9/5 Rating
                </div>
                <div class="flex items-center text-gray-200 text-sm">
                  <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  500+ Happy Customers
                </div>
                <div class="flex items-center text-gray-200 text-sm">
                  <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Open Today
                </div>
              </div>
              <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                <div class="rounded-md shadow">
                  <Link
                    :href="route('booking.create')"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 md:py-4 md:text-lg md:px-10 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                    </svg>
                    Book Appointment
                  </Link>
                </div>
                <div class="mt-3 sm:mt-0 sm:ml-3">
                  <Link
                    :href="route('services')"
                    class="w-full flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-md text-white bg-transparent hover:bg-white hover:text-gray-900 md:py-4 md:text-lg md:px-10 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Our Services
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Book Appointment Section -->
      <section class="bg-gradient-to-r from-green-50 to-blue-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mb-3">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Available Today
            </div>
            <p class="text-lg text-gray-700 mb-2 font-medium">
                                Experience the difference at Mickyes Coiffure
            </p>
            <p class="text-sm text-gray-600 mb-6">
              Walk-ins welcome • Same-day appointments • Professional service guaranteed
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
              <Link
                :href="route('booking.create')"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                </svg>
                Book Your Appointment
              </Link>
              <a href="tel:+1234567890" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Call Now: (123) 456-7890
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- Transformations Carousel -->
      <section class="bg-white py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-4">
              Our Amazing Transformations
            </h2>
            <p class="text-xl text-gray-600">
              See the incredible work our talented barbers do
            </p>
          </div>
          <div class="relative">
            <div v-if="slides && slides.length > 0">
              <div class="relative h-[280px] overflow-hidden rounded-lg shadow-xl">
                <div class="flex h-full gap-4">
                  <!-- Before Image -->
                  <div class="w-1/2 relative">
                    <img :src="slides[currentSlide].before"
                         :alt="'Before ' + slides[currentSlide].description"
                         class="absolute inset-0 w-full h-full object-cover rounded-l-lg"
                         @error="console.log('Error loading before image:', slides[currentSlide].before)">
                    <div class="absolute inset-0 bg-black bg-opacity-30">
                      <span class="absolute top-4 left-4 text-white text-xl font-bold">Before</span>
                    </div>
                  </div>
                  <!-- After Image -->
                  <div class="w-1/2 relative">
                    <img :src="slides[currentSlide].after"
                         :alt="'After ' + slides[currentSlide].description"
                         class="absolute inset-0 w-full h-full object-cover rounded-r-lg"
                         @error="console.log('Error loading after image:', slides[currentSlide].after)">
                    <div class="absolute inset-0 bg-black bg-opacity-30">
                      <span class="absolute top-4 right-4 text-white text-xl font-bold">After</span>
                    </div>
                  </div>
                </div>
                <div class="absolute bottom-0 inset-x-0 bg-black bg-opacity-50 text-white p-4 text-center">
                  <p class="text-lg font-semibold">{{ slides[currentSlide].description }}</p>
                </div>
              </div>

              <!-- Navigation Buttons -->
              <button
                @click="prevSlide"
                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-12 bg-green-600 text-white p-2 rounded-full shadow-lg hover:bg-green-700 transition-colors duration-200"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>

              <button
                @click="nextSlide"
                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 bg-green-600 text-white p-2 rounded-full shadow-lg hover:bg-green-700 transition-colors duration-200"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </div>
            <div v-else class="text-center py-12">
              <p class="text-gray-500 text-lg">
                No transformations available at the moment. Check back soon to see our amazing work!
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Services Section -->
      <section class="relative bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
              <ScissorsIcon class="h-8 w-8 text-green-600" />
              <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Professional Barber Services
              </h2>
            </div>
            <p class="mt-4 text-xl text-gray-600">
              Expert grooming services tailored to your style
            </p>
          </div>

          <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            <!-- Classic Cuts -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/classic-cut.jpg"
                  alt="Classic Haircut Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <ScissorsIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Classic Haircuts</h3>
                </div>
                <p class="text-gray-600">
                  From fades to tapers, textured crops to classic gentleman's cuts. Our experienced barbers deliver precision cuts that enhance your natural style.
                </p>
              </div>
            </div>

            <!-- Kids Cuts -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/kids-cut.jpg"
                  alt="Kids Haircut Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <UserGroupIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Kids Haircuts</h3>
                </div>
                <p class="text-gray-600">
                  Specialized in making children feel comfortable and confident. We create fun, age-appropriate styles in a welcoming environment.
                </p>
              </div>
            </div>

            <!-- Hair Coloring -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/hair-color.jpg"
                  alt="Hair Coloring Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <SwatchIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Hair Coloring & Dyeing</h3>
                </div>
                <p class="text-gray-600">
                  Express yourself with our professional coloring services. From subtle highlights to bold fashion colors, we help you achieve your desired look.
                </p>
              </div>
            </div>

            <!-- Beard Grooming -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/beard-grooming.jpg"
                  alt="Beard Grooming Service"
                  class="object-cover w-full h-48 object-center"
                  style="object-position: center center;"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <SparklesIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Beard Grooming</h3>
                </div>
                <p class="text-gray-600">
                  Complete beard care including trimming, shaping, and hot towel treatments. Keep your facial hair looking sharp and well-maintained.
                </p>
              </div>
            </div>

            <!-- Home Service -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/home-service.jpg"
                  alt="Mobile Barber Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <HomeIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Mobile Barber Service</h3>
                </div>
                <p class="text-gray-600">
                  Can't make it to the shop? Our professional barbers come to you. Enjoy the same high-quality service in the comfort of your home.
                </p>
              </div>
            </div>

            <!-- Special Treatments -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/special-treatment.jpg"
                  alt="Special Treatment Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <SparklesIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Special Treatments</h3>
                </div>
                <p class="text-gray-600">
                  Pamper yourself with our premium treatments. From hot towel treatments to scalp massages, we offer services that go beyond the basics.
                </p>
              </div>
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
import { ref, onMounted } from 'vue'
import Navigation from '@/Components/Navigation.vue'
import Footer from '@/Components/Footer.vue'
import {
  ScissorsIcon,
  UserGroupIcon,
  SwatchIcon,
  SparklesIcon,
  HomeIcon
} from '@heroicons/vue/24/outline'
import axios from 'axios'

const slides = ref([])

onMounted(async () => {
  try {
    const res = await axios.get('/api/transformations/approved')
    slides.value = res.data.map(t => ({
      before: t.before,
      after: t.after,
      description: t.style || 'Transformation',
    }))
  } catch (error) {
    console.log('Failed to load transformations:', error)
    // Set some default slides or leave empty
    slides.value = []
  }
})

const currentSlide = ref(0)

const nextSlide = () => {
  if (slides.value.length > 0) {
    currentSlide.value = (currentSlide.value + 1) % slides.value.length
  }
}

const prevSlide = () => {
  if (slides.value.length > 0) {
    currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length
  }
}
</script>
