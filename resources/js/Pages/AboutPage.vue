<template>
  <Head title="About" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <main>
      <!-- Hero Section -->
      <section class="relative bg-white overflow-hidden">
        <div class="absolute inset-0">
          <div class="h-full w-full relative overflow-hidden">
            <img
              src="/images/hero/pexels-photo-2881253.jpeg"
              alt="About MickyesBarber"
              class="w-full h-full object-cover object-center"
            />
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
          </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
              About Us
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-100 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
              Your trusted destination for professional barbering services
            </p>
          </div>
        </div>
      </section>

      <!-- Our Story -->
      <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:text-center">
            <h2 class="text-base text-green-600 font-semibold tracking-wide uppercase">Our Story</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              A Legacy of Excellence
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
              Founded with a passion for traditional barbering and modern style, MickyesBarber has been serving our community with pride since 2010.
            </p>
          </div>

          <div class="mt-10">
            <div class="prose prose-lg text-gray-500 mx-auto">
              <p>
                At MickyesBarber, we believe that a great haircut is more than just a service – it's an experience. Our team of skilled barbers combines traditional techniques with contemporary styles to create looks that are uniquely yours.
              </p>
              <p>
                We take pride in our attention to detail and commitment to customer satisfaction. Every visit to MickyesBarber is an opportunity to relax, refresh, and leave feeling confident and looking your best.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Our Values -->
      <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:text-center">
            <h2 class="text-base text-green-600 font-semibold tracking-wide uppercase">Our Values</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              What Sets Us Apart
            </p>
          </div>

          <div class="mt-10">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
              <!-- Quality -->
              <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                  <SparklesIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Quality</h3>
                </div>
                <p class="text-gray-600">
                  We use only the finest products and maintain the highest standards in our services to ensure you receive the best possible experience.
                </p>
              </div>

              <!-- Expertise -->
              <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                  <AcademicCapIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Expertise</h3>
                </div>
                <p class="text-gray-600">
                  Our barbers are highly trained professionals with years of experience in creating the perfect look for every client.
                </p>
              </div>

              <!-- Community -->
              <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center mb-4">
                  <UserGroupIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Community</h3>
                </div>
                <p class="text-gray-600">
                  We're more than just a barbershop – we're a gathering place for our community, where relationships are built and maintained.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Team Section -->
      <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:text-center">
            <h2 class="text-base text-green-600 font-semibold tracking-wide uppercase">Our Team</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              Meet the Professionals
            </p>
          </div>

          <div class="mt-10">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
              <!-- Team Member 1 -->
              <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
                <img
                  src="/images/services/pexels-photo-3993293.jpeg"
                  alt="Senior Barber"
                  class="w-full h-64 object-cover"
                />
                <div class="p-6">
                  <h3 class="text-xl font-bold text-gray-900">Mike Johnson</h3>
                  <p class="text-green-600 mb-2">Senior Barber</p>
                  <p class="text-gray-600">
                    With over 15 years of experience, Mike specializes in classic cuts and beard grooming.
                  </p>
                </div>
              </div>

              <!-- Team Member 2 -->
              <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
                <img
                  src="/images/services/pexels-photo-3998414.webp"
                  alt="Style Expert"
                  class="w-full h-64 object-cover"
                />
                <div class="p-6">
                  <h3 class="text-xl font-bold text-gray-900">Sarah Chen</h3>
                  <p class="text-green-600 mb-2">Style Expert</p>
                  <p class="text-gray-600">
                    Sarah brings creativity and precision to every cut, specializing in modern styles and coloring.
                  </p>
                </div>
              </div>

              <!-- Team Member 3 -->
              <div class="bg-gray-50 rounded-lg overflow-hidden shadow-lg">
                <img
                  src="/images/services/pexels-photo-19664871.webp"
                  alt="Master Barber"
                  class="w-full h-64 object-cover"
                />
                <div class="p-6">
                  <h3 class="text-xl font-bold text-gray-900">David Martinez</h3>
                  <p class="text-green-600 mb-2">Master Barber</p>
                  <p class="text-gray-600">
                    David's expertise in traditional techniques and contemporary styles makes him a favorite among our clients.
                  </p>
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
import { Head } from '@inertiajs/vue3'
import Navigation from '@/Components/Navigation.vue'
import Footer from '@/Components/Footer.vue'
import {
  SparklesIcon,
  AcademicCapIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline'
</script>
