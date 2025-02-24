<template>
  <Head title="About Us" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="relative bg-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            About MickyesBarber
          </h1>
          <p class="mt-4 text-xl text-gray-600">
            Your trusted destination for professional grooming
          </p>
        </div>

        <!-- About Content -->
        <div class="mt-16 prose prose-lg mx-auto">
          <p>
            At MickyesBarber, we believe in providing exceptional grooming services
            that make our clients look and feel their best. With years of experience
            and a passion for our craft, we've built a reputation for excellence in
            the community.
          </p>
          <!-- Add more content as needed -->
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
</script>
