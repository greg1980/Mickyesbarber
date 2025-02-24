<template>
  <Head title="Services" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="relative bg-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <div class="flex items-center justify-center gap-3 mb-4">
            <ScissorsIcon class="h-8 w-8 text-green-600" />
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
              Our Services
            </h1>
          </div>
          <p class="text-gray-600 text-center max-w-3xl mx-auto mt-6">
            At MickyesBarber, we pride ourselves on delivering exceptional grooming services tailored to your individual style.
            Our experienced team of barbers combines traditional techniques with modern trends to ensure you leave our shop
            looking and feeling your best. Whether you're seeking a classic cut or a contemporary style, we're here to exceed
            your expectations.
          </p>
        </div>
      </div>
    </section>

    <!-- Service Grid -->
    <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
      <!-- Service Cards (similar to your HomePage) -->
      <!-- You can reuse your service cards from HomePage -->
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import { ScissorsIcon } from '@heroicons/vue/24/outline';
</script>
