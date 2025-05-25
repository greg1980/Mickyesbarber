<template>
  <Head title="Services" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <main>
      <!-- Hero Section -->
      <section class="relative bg-white overflow-hidden">
        <div class="absolute inset-0">
          <div class="h-full w-full relative overflow-hidden">
            <img
              src="/images/hero/pexels-photo-2076932.jpeg"
              alt="Barber Services"
              class="w-full h-full object-cover object-center"
            />
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
            <!-- Classic Haircuts -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <ScissorsIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Classic Haircuts</h3>
                </div>
                <p class="text-gray-600 mb-4">
                  From fades to tapers, textured crops to classic gentleman's cuts. Our experienced barbers deliver precision cuts that enhance your natural style.
                </p>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900">$30</span>
                  <Link
                    :href="route('booking.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                  >
                    Book Now
                  </Link>
                </div>
              </div>
            </div>

            <!-- Kids Haircuts -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <UserGroupIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Kids Haircuts</h3>
                </div>
                <p class="text-gray-600 mb-4">
                  Specialized in making children feel comfortable and confident. We create fun, age-appropriate styles in a welcoming environment.
                </p>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900">$25</span>
                  <Link
                    :href="route('booking.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                  >
                    Book Now
                  </Link>
                </div>
              </div>
            </div>

            <!-- Hair Coloring -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <SwatchIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Hair Coloring</h3>
                </div>
                <p class="text-gray-600 mb-4">
                  Express yourself with our professional coloring services. From subtle highlights to bold fashion colors, we help you achieve your desired look.
                </p>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900">$45</span>
                  <Link
                    :href="route('booking.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                  >
                    Book Now
                  </Link>
                </div>
              </div>
            </div>

            <!-- Beard Grooming -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <SparklesIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Beard Grooming</h3>
                </div>
                <p class="text-gray-600 mb-4">
                  Complete beard care including trimming, shaping, and hot towel treatments. Keep your facial hair looking sharp and well-maintained.
                </p>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900">$20</span>
                  <Link
                    :href="route('booking.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                  >
                    Book Now
                  </Link>
                </div>
              </div>
            </div>

            <!-- Mobile Service -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <HomeIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Mobile Service</h3>
                </div>
                <p class="text-gray-600 mb-4">
                  Can't make it to the shop? Our professional barbers come to you. Enjoy the same high-quality service in the comfort of your home.
                </p>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900">$50</span>
                  <Link
                    :href="route('booking.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                  >
                    Book Now
                  </Link>
                </div>
              </div>
            </div>

            <!-- Special Treatments -->
            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <SparklesIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Special Treatments</h3>
                </div>
                <p class="text-gray-600 mb-4">
                  Pamper yourself with our premium treatments. From hot towel treatments to scalp massages, we offer services that go beyond the basics.
                </p>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900">$35</span>
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
</script>
