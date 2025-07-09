<template>
  <Head title="My Transformations">
    <meta name="description" content="See real customer hair transformations at Mickyes Coiffure. Get inspired and book your own transformation today!" />
    <meta property="og:title" content="Transformations Gallery - Mickyes Coiffure" />
    <meta property="og:description" content="Browse before and after photos of real customers at Mickyes Coiffure, Newcastle's top barbershop." />
    <meta property="og:image" content="/images/transformations/sample.jpg" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://mickyes.com/transformations" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Transformations Gallery - Mickyes Coiffure" />
    <meta name="twitter:description" content="See real customer hair transformations and get inspired for your next cut!" />
    <meta name="twitter:image" content="/images/transformations/sample.jpg" />
  </Head>
  <SidebarLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <!-- Modern Header with Gradient -->
      <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-pink-600 text-white py-8 lg:py-12 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-20">
          <svg class="w-full h-full" fill="currentColor" viewBox="0 0 100 100">
            <pattern id="transformation-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="10" cy="10" r="1.5"/>
              <circle cx="2" cy="2" r="0.5"/>
              <circle cx="18" cy="18" r="0.5"/>
            </pattern>
            <rect width="100" height="100" fill="url(#transformation-pattern)"/>
          </svg>
        </div>
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <div class="flex justify-center mb-6">
              <div class="h-20 w-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                </svg>
              </div>
            </div>
            <h1 class="text-4xl lg:text-5xl font-bold mb-4">My Transformations</h1>
            <p class="text-xl text-purple-100 max-w-2xl mx-auto">Share your amazing hair transformation and help other customers discover their perfect style</p>
          </div>
        </div>
      </div>
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Status Messages -->
        <div v-if="Object.keys(form.errors).length" class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
          <div class="flex items-center mb-2">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-red-800 font-semibold">Please fix the following errors:</h3>
          </div>
          <div class="space-y-1">
            <div v-for="(error, key) in form.errors" :key="key" class="text-red-700 text-sm">{{ error }}</div>
          </div>
        </div>
        <div v-if="showSuccess" class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-green-800 font-semibold">Transformation submitted successfully and is pending approval!</span>
          </div>
        </div>
        <!-- Conditional Status Messages -->
        <div v-if="!props.latestBookingId" class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-6">
          <div class="flex items-center justify-center text-center">
            <div class="max-w-md">
              <svg class="w-12 h-12 text-amber-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <h3 class="text-lg font-semibold text-amber-800 mb-2">No Completed Bookings</h3>
              <p class="text-amber-700">You must have a completed booking before submitting a transformation. Book your first appointment to get started!</p>
              <Link :href="route('booking.create')" class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl font-medium transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Book Appointment
              </Link>
            </div>
          </div>
        </div>
        <div v-else-if="props.alreadySubmitted" class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
          <div class="flex items-center justify-center text-center">
            <div class="max-w-md">
              <svg class="w-12 h-12 text-blue-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <h3 class="text-lg font-semibold text-blue-800 mb-2">Already Submitted</h3>
              <p class="text-blue-700">You have already submitted a transformation for your latest completed booking. Thank you for sharing your experience!</p>
            </div>
          </div>
        </div>
        <!-- Transformation Submission Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
          <!-- Form Header -->
          <div class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 p-4 sm:p-8">
            <div class="flex flex-col sm:flex-row items-center justify-center text-center gap-3">
              <div class="h-14 w-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mr-0 sm:mr-4 shadow-lg mx-auto sm:mx-0">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
              </div>
              <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">Submit Your Transformation</h2>
                <p class="text-gray-600 text-sm sm:text-base">Share your before & after photos and help inspire others</p>
              </div>
            </div>
          </div>
          <!-- Form Content -->
          <form @submit.prevent="submitForm" enctype="multipart/form-data"
                :class="{ 'opacity-50 pointer-events-none': !props.latestBookingId || props.alreadySubmitted }"
                class="p-4 sm:p-8 space-y-6 sm:space-y-8">
            <input type="hidden" v-model="form.booking_id" />
            <!-- Barber Selection -->
            <div class="space-y-3">
              <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Your Barber
              </label>
              <div class="relative" ref="barberDropdownRef">
                <select v-model="form.barber_id"
                        class="hidden lg:block w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900 font-medium">
                  <option value="">Select your barber</option>
                  <option v-for="barber in props.barbers" :key="barber.id" :value="barber.id">
                    {{ barber.user ? barber.user.name : 'Barber #' + barber.id }}
                  </option>
                </select>
                <!-- Mobile Dropdown -->
                <div class="lg:hidden">
                  <button type="button" @click="toggleBarberDropdown"
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-left flex items-center justify-between">
                    <span class="text-gray-900 font-medium">{{ getSelectedBarberName() || 'Select your barber' }}</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </button>
                  <div v-if="showBarberDropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg">
                    <div v-for="barber in props.barbers" :key="barber.id"
                         @click="selectBarber(barber)"
                         class="px-4 py-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0 text-gray-900 font-medium">
                      {{ barber.user ? barber.user.name : 'Barber #' + barber.id }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Photo Upload Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
              <!-- Before Photo -->
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  Before Photo
                </label>
                <div class="relative group">
                  <input type="file"
                         @change="(e) => form.before_photo = e.target.files[0]"
                         accept="image/*"
                         class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                         id="before-photo">
                  <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 group-hover:scale-[1.02]">
                    <div v-if="!form.before_photo" class="space-y-3">
                      <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      <div>
                        <p class="text-gray-600 font-medium text-center">Upload your before photo</p>
                        <p class="text-xs sm:text-sm text-gray-400 mt-1 text-center">Drag & drop or click to browse</p>
                      </div>
                    </div>
                    <div v-else class="space-y-2">
                      <svg class="w-12 h-12 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <p class="text-green-600 font-medium text-center">{{ form.before_photo.name }}</p>
                      <p class="text-xs sm:text-sm text-gray-500 text-center">Click to change photo</p>
                    </div>
                  </div>
                </div>
              </div>
              <!-- After Photo -->
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                  <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  After Photo
                </label>
                <div class="relative group">
                  <input type="file"
                         @change="(e) => form.after_photo = e.target.files[0]"
                         accept="image/*"
                         class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                         id="after-photo">
                  <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-400 hover:bg-green-50 transition-all duration-200 group-hover:scale-[1.02]">
                    <div v-if="!form.after_photo" class="space-y-3">
                      <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      <div>
                        <p class="text-gray-600 font-medium text-center">Upload your after photo</p>
                        <p class="text-xs sm:text-sm text-gray-400 mt-1 text-center">Drag & drop or click to browse</p>
                      </div>
                    </div>
                    <div v-else class="space-y-2">
                      <svg class="w-12 h-12 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <p class="text-green-600 font-medium text-center">{{ form.after_photo.name }}</p>
                      <p class="text-xs sm:text-sm text-gray-500 text-center">Click to change photo</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Haircut Style -->
            <div class="space-y-3">
              <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5.236V9a2 2 0 00-2-2H6a2 2 0 00-2 2v10.236"/>
                </svg>
                Haircut Style
              </label>
              <input type="text"
                     v-model="form.style"
                     placeholder="e.g., Fade, Buzz Cut, Layered, etc."
                     class="w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500 text-sm sm:text-base">
            </div>
            <!-- Review -->
            <div class="space-y-3">
              <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Your Review
                <span class="text-gray-400 font-normal">(optional)</span>
              </label>
              <textarea v-model="form.review"
                        rows="4"
                        placeholder="Share your experience! How was the service? What did you love about your new look?"
                        class="w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none text-sm sm:text-base"></textarea>
            </div>
            <!-- Enhanced Star Rating -->
            <div class="space-y-4">
              <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/>
                </svg>
                Rating
              </label>
              <div class="flex items-center justify-center bg-gray-50 rounded-xl p-4 sm:p-6 space-x-1 sm:space-x-2">
                <template v-for="star in 5" :key="star">
                  <button type="button"
                          @click="setRating(star)"
                          @mouseenter="hoverRating = star"
                          @mouseleave="hoverRating = 0"
                          :class="[
                            'w-8 h-8 sm:w-12 sm:h-12 transition-all duration-200 transform hover:scale-110',
                            (hoverRating >= star || form.rating >= star)
                              ? 'text-yellow-400 drop-shadow-lg'
                              : 'text-gray-300 hover:text-yellow-300'
                          ]">
                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"/>
                    </svg>
                  </button>
                </template>
              </div>
              <p class="text-center text-xs sm:text-sm text-gray-600">
                {{ form.rating > 0 ? `You rated this service ${form.rating} out of 5 stars` : 'Please rate your experience' }}
              </p>
            </div>
            <!-- Submit Button -->
            <div class="pt-4 sm:pt-6 flex justify-center">
              <button type="submit"
                      :disabled="submitting || !props.latestBookingId || props.alreadySubmitted"
                      class="w-full sm:w-auto px-6 sm:px-12 py-3 sm:py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl font-bold text-base sm:text-lg transition-all duration-200 transform hover:scale-105 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:shadow-none flex items-center justify-center gap-3">
                <svg v-if="submitting" class="animate-spin w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span v-if="submitting">Submitting...</span>
                <span v-else>Submit Transformation</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </SidebarLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

const props = defineProps({
  barbers: Array,
  latestBookingId: {
    type: [Number, String],
    default: null,
  },
  alreadySubmitted: {
    type: Boolean,
    required: true,
  },
})

const showSuccess = ref(false)
const submitting = ref(false)
const showBarberDropdown = ref(false)
const barberDropdownRef = ref(null)
const hoverRating = ref(0)

const form = useForm({
  before_photo: null,
  after_photo: null,
  style: '',
  review: '',
  rating: 0,
  barber_id: '',
  booking_id: props.latestBookingId || '',
})

const setRating = (star) => {
  form.rating = star
}

const resetForm = () => {
  form.before_photo = null
  form.after_photo = null
  form.style = ''
  form.review = ''
  form.rating = 0
  form.barber_id = ''
  hoverRating.value = 0
  document.querySelectorAll('input[type=file]').forEach(input => input.value = '')
}

const submitForm = () => {
  submitting.value = true
  form.post(route('transformations.store'), {
    forceFormData: true,
    onSuccess: () => {
      showSuccess.value = true
      resetForm()
      setTimeout(() => showSuccess.value = false, 6000)
    },
    onFinish: () => {
      submitting.value = false
    },
  })
}

const getSelectedBarberName = () => {
  const barber = props.barbers.find(b => b.id === form.barber_id)
  return barber ? (barber.user ? barber.user.name : 'Barber #' + barber.id) : ''
}

const selectBarber = (barber) => {
  form.barber_id = barber.id
  showBarberDropdown.value = false
}

const toggleBarberDropdown = () => {
  showBarberDropdown.value = !showBarberDropdown.value
}

const handleClickOutside = (event) => {
  if (barberDropdownRef.value && !barberDropdownRef.value.contains(event.target)) {
    showBarberDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
