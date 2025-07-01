<template>
  <Head title="Contact" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <main>
      <!-- Hero Section -->
      <section class="relative bg-white overflow-hidden h-[40vh] md:h-[45vh] lg:h-[50vh]">
        <div class="absolute inset-0">
          <div class="h-full w-full relative overflow-hidden">
            <img
              src="/images/hero/pexels-photo-1813272.jpeg"
              alt="Contact Mickyes Coiffure"
              class="w-full h-full object-cover object-center"
            />
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
          </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-center">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
              Contact Us
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-100 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
              Get in touch with Mickyes Coiffure
            </p>
          </div>
        </div>
      </section>

      <!-- Contact Information -->
      <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            <!-- Contact Form -->
            <div class="bg-gray-50 rounded-lg shadow-lg p-6">
              <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a message</h2>
              <form @submit.prevent="submitForm" class="space-y-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                  <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required
                  />
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                  <input
                    type="email"
                    id="email"
                    v-model="form.email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required
                  />
                </div>

                <div>
                  <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                  <input
                    type="text"
                    id="subject"
                    v-model="form.subject"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required
                  />
                </div>

                <div>
                  <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                  <textarea
                    id="message"
                    v-model="form.message"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    required
                  ></textarea>
                </div>

                <div>
                  <button
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    :disabled="form.processing"
                  >
                    Send Message
                  </button>
                </div>
              </form>
            </div>

            <!-- Contact Information -->
            <div class="bg-gray-50 rounded-lg shadow-lg p-6">
              <h2 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h2>

              <div class="space-y-6">
                <!-- Address -->
                <div class="flex items-start">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center shadow-lg">
                    <MapPinIcon class="h-6 w-6" />
                  </div>
                  <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Address</h3>
                    <p class="mt-1 text-gray-600">
                      Liddle Rd<br />
                      Newcastle upon Tyne NE4 5JE<br />
                      United Kingdom
                    </p>
                  </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start">
                  <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-full flex items-center justify-center shadow-lg">
                    <PhoneIcon class="h-6 w-6" />
                  </div>
                  <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Phone</h3>
                    <p class="mt-1 text-gray-600">
                      <a href="tel:+447852482489" class="hover:text-green-600 transition-colors">
                        +44 7852 482489
                      </a>
                    </p>
                  </div>
                </div>

                <!-- Email -->
                <div class="flex items-start">
                  <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 text-white rounded-full flex items-center justify-center shadow-lg">
                    <EnvelopeIcon class="h-6 w-6" />
                  </div>
                  <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Email</h3>
                    <p class="mt-1 text-gray-600">
                      <a href="mailto:info@mickyescoiffure.com" class="hover:text-orange-600 transition-colors">
                        info@mickyescoiffure.com
                      </a>
                    </p>
                  </div>
                </div>

                <!-- Hours -->
                <div class="flex items-start">
                  <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-full flex items-center justify-center shadow-lg">
                    <ClockIcon class="h-6 w-6" />
                  </div>
                  <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Opening Hours</h3>
                    <div class="mt-1 text-gray-600 space-y-1">
                      <p class="flex justify-between"><span>Monday:</span> <span>10:00 am - 6:00 pm</span></p>
                      <p class="flex justify-between"><span>Tuesday:</span> <span>10:00 am - 6:00 pm</span></p>
                      <p class="flex justify-between"><span>Wednesday:</span> <span>10:00 am - 6:00 pm</span></p>
                      <p class="flex justify-between"><span>Thursday:</span> <span>10:00 am - 6:00 pm</span></p>
                      <p class="flex justify-between"><span>Friday:</span> <span>10:00 am - 7:00 pm</span></p>
                      <p class="flex justify-between"><span>Saturday:</span> <span>10:00 am - 7:00 pm</span></p>
                      <p class="flex justify-between"><span>Sunday:</span> <span class="text-red-500 font-medium">Closed</span></p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Social Media -->
              <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Follow Us</h3>
                <div class="flex space-x-4">
                  <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-all duration-200 transform hover:scale-110">
                    <span class="sr-only">Facebook</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                      <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                    </svg>
                  </a>
                  <a href="#" class="w-10 h-10 bg-gradient-to-br from-purple-600 via-pink-600 to-orange-500 text-white rounded-full flex items-center justify-center hover:from-purple-700 hover:via-pink-700 hover:to-orange-600 transition-all duration-200 transform hover:scale-110">
                    <span class="sr-only">Instagram</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                      <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 715.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                    </svg>
                  </a>
                  <a href="#" class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-all duration-200 transform hover:scale-110">
                    <span class="sr-only">X (formerly Twitter)</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Map Section -->
      <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="rounded-lg overflow-hidden shadow-lg">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2352.3158567321237!2d-1.6131!3d54.9738!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487e708ca6598af1%3A0x6b4b6c6a6b4b6c6a!2sLiddle%20Rd%2C%20Newcastle%20upon%20Tyne%20NE4%205JE%2C%20UK!5e0!3m2!1sen!2sus!4v1234567890"
              width="100%"
              height="450"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>
        </div>
      </section>
    </main>
  </div>
  <Footer />
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Navigation from '@/Components/Navigation.vue'
import Footer from '@/Components/Footer.vue'
import {
  MapPinIcon,
  PhoneIcon,
  EnvelopeIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: '',
  processing: false
})

const submitForm = () => {
  form.value.processing = true
  // Handle form submission here
  setTimeout(() => {
    form.value.processing = false
    // Reset form
    form.value = {
      name: '',
      email: '',
      subject: '',
      message: '',
      processing: false
    }
  }, 1000)
}
</script>
