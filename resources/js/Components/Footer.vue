<template>
  <footer class="bg-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- About Section -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold">Mickyes Coiffure</h3>
          <p class="text-gray-400">
            Professional barbering services with style and precision. Your trusted destination for quality haircuts and grooming in Newcastle upon Tyne.
          </p>
        </div>

        <!-- Quick Links -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold">Quick Links</h3>
          <ul class="space-y-2">
            <li>
              <Link :href="route('home')" class="text-gray-400 hover:text-white transition-colors duration-200">
                Home
              </Link>
            </li>
            <li>
              <Link :href="route('services')" class="text-gray-400 hover:text-white transition-colors duration-200">
                Services
              </Link>
            </li>
            <li>
              <Link :href="route('about')" class="text-gray-400 hover:text-white transition-colors duration-200">
                About
              </Link>
            </li>
            <li>
              <Link :href="route('contact')" class="text-gray-400 hover:text-white transition-colors duration-200">
                Contact
              </Link>
            </li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold">Contact Info</h3>
          <ul class="space-y-2">
            <li class="flex items-start">
              <MapPinIcon class="h-6 w-6 text-green-500 mt-1 flex-shrink-0" />
              <span class="ml-2 text-gray-400">
                Liddle Rd<br />
                Newcastle upon Tyne NE4 5JE<br />
                United Kingdom
              </span>
            </li>
            <li class="flex items-center">
              <PhoneIcon class="h-6 w-6 text-green-500 flex-shrink-0" />
              <a href="tel:+447961572568" class="ml-2 text-gray-400 hover:text-white transition-colors">
                +44 7961 572568
              </a>
            </li>
            <li class="flex items-center">
              <EnvelopeIcon class="h-6 w-6 text-green-500 flex-shrink-0" />
              <a href="mailto:info@mickyes.com" class="ml-2 text-gray-400 hover:text-white transition-colors">
                info@mickyes.com
              </a>
            </li>
          </ul>
        </div>

        <!-- Hours -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold">Opening Hours</h3>
          <ul class="space-y-1 text-gray-400 text-sm">
            <li class="flex justify-between"><span>Monday:</span> <span>10:00 am - 6:00 pm</span></li>
            <li class="flex justify-between"><span>Tuesday:</span> <span>10:00 am - 6:00 pm</span></li>
            <li class="flex justify-between"><span>Wednesday:</span> <span>10:00 am - 6:00 pm</span></li>
            <li class="flex justify-between"><span>Thursday:</span> <span>10:00 am - 6:00 pm</span></li>
            <li class="flex justify-between"><span>Friday:</span> <span>10:00 am - 7:00 pm</span></li>
            <li class="flex justify-between"><span>Saturday:</span> <span>10:00 am - 7:00 pm</span></li>
            <li class="flex justify-between"><span>Sunday:</span> <span class="text-red-400">Closed</span></li>
          </ul>
        </div>
      </div>

      <!-- Social Media & Copyright -->
      <div class="mt-12 pt-8 border-t border-gray-800">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <!-- Social Media Links -->
          <div class="flex space-x-4 mb-4 md:mb-0">
            <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-all duration-200 transform hover:scale-110">
              <span class="sr-only">Facebook</span>
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-gradient-to-br from-purple-600 via-pink-600 to-orange-500 text-white rounded-full flex items-center justify-center hover:from-purple-700 hover:via-pink-700 hover:to-orange-600 transition-all duration-200 transform hover:scale-110">
              <span class="sr-only">Instagram</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-all duration-200 transform hover:scale-110">
              <span class="sr-only">X (formerly Twitter)</span>
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
              </svg>
            </a>
          </div>

          <!-- Copyright -->
          <p class="text-gray-400 text-sm">
            © {{ new Date().getFullYear() }} Mickyes Coiffure. All rights reserved.
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import {
  MapPinIcon,
  PhoneIcon,
  EnvelopeIcon
} from '@heroicons/vue/24/outline'
</script>
