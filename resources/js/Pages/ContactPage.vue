<template>
  <Head title="Contact Us" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <!-- Contact Section -->
    <section class="relative bg-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            Contact Us
          </h1>
          <p class="mt-4 text-xl text-gray-600">
            Get in touch with us
          </p>
        </div>

        <div class="mt-16 grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Contact Form -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <form @submit.prevent="submitForm" class="grid grid-cols-1 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" v-model="form.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
              </div>

              <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea id="message" v-model="form.message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
              </div>

              <div>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                  Send Message
                </button>
              </div>
            </form>
          </div>

          <!-- Contact Info & Map -->
          <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Visit Us</h3>
              <p class="text-gray-600">123 Barber Street</p>
              <p class="text-gray-600">London, UK SW1A 1AA</p>
              <p class="text-gray-600 mt-4">Phone: +44 20 1234 5678</p>
              <p class="text-gray-600">Email: info@mickyesbarber.com</p>
            </div>

            <!-- Map -->
            <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2483.542356599117!2d-0.12985968422955556!3d51.50736731772737!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487604c38c8cd1d9%3A0xb78f2474b9a45aa9!2sBig%20Ben!5m2!1s0x487604c38c8cd1d9%3A0xb78f2474b9a45aa9"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import { reactive } from 'vue';

const form = reactive({
  name: '',
  email: '',
  message: ''
});

const submitForm = () => {
  // Add your form submission logic here
  console.log('Form submitted:', form);
};
</script>
