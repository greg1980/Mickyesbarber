<template>
  <Head title="Home" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <main>
      <section class="relative bg-white overflow-hidden h-[60vh]">
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
                <span class="block">MickyesBarber</span>
              </h1>
              <p class="mt-3 text-base text-gray-100 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                Professional barbering services with style and precision.
              </p>
              <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                <div class="rounded-md shadow">
                  <Link
                    :href="route('booking.create')"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-black hover:bg-gray-800 md:py-4 md:text-lg md:px-10 transition-colors duration-300"
                  >
                    Book Now
                  </Link>
                </div>
                <div class="mt-3 sm:mt-0 sm:ml-3">
                  <Link
                    :href="route('services')"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 md:py-4 md:text-lg md:px-10 transition-colors duration-300"
                  >
                    Our Services
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Book Appointment Section -->
      <section class="bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <p class="text-base text-gray-600 mb-4">
              Experience the difference at MickyesBarber. Walk-ins welcome, appointments preferred.
            </p>
            <Link
              :href="route('booking.create')"
              class="inline-flex items-center justify-center px-4 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 md:py-2 md:text-sm md:px-6 transition-colors duration-300"
            >
              Book Your Appointment
            </Link>
          </div>
        </div>
      </section>

      <!-- Transformations Carousel -->
      <section class="bg-white py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
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
  const res = await axios.get('/admin/transformations/approved')
  slides.value = res.data.map(t => ({
    before: t.before,
    after: t.after,
    description: t.style || 'Transformation',
  }))
})

const currentSlide = ref(0)

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.value.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length
}
</script>
