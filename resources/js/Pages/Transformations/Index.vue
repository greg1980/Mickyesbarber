<template>
  <SidebarLayout>
    <div class="p-6 bg-gray-50 min-h-screen">
      <div class="bg-white shadow rounded px-6 py-4 mb-6">
        <h1 class="text-xl font-bold flex items-center gap-2 text-gray-600">
          <div class="h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
            </svg>
          </div>
          My Transformations
        </h1>
      </div>
      <div class="max-w-3xl mx-auto py-12">
        <h1 class="text-xl font-bold mb-6 flex items-center gap-2 text-gray-600">
          <div class="h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
            </svg>
          </div>
          My Transformations
        </h1>

        <div v-if="Object.keys(form.errors).length" class="mb-4 p-3 bg-red-100 text-red-800 rounded border border-red-300">
          <div v-for="(error, key) in form.errors" :key="key">{{ error }}</div>
        </div>

        <div v-if="showSuccess" class="mb-4 p-3 bg-green-100 text-green-800 rounded border border-green-300">
          Transformation submitted successfully!
        </div>

        <div class="bg-white rounded shadow-lg p-6 mb-8">
          <form @submit.prevent="submitForm" enctype="multipart/form-data" :class="{ 'opacity-50 pointer-events-none': !props.latestBookingId || props.alreadySubmitted }">
            <div v-if="!props.latestBookingId" class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded border border-yellow-300">
              You must have a completed booking before submitting a transformation.
            </div>
            <div v-else-if="props.alreadySubmitted" class="mb-4 p-3 bg-blue-100 text-blue-800 rounded border border-blue-300">
              You have already submitted a transformation for your latest completed booking.
            </div>
            <h2 class="text-lg font-bold mb-4 flex items-center gap-2 text-gray-600">
              <div class="h-6 w-6 bg-green-100 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-green-600">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
              </div>
              Submit Transformation
            </h2>
            <input type="hidden" v-model="form.booking_id" />
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">Barber</label>

              <!-- Mobile Custom Dropdown -->
              <div class="block md:hidden">
                <div class="relative" ref="barberDropdownRef">
                  <button
                    type="button"
                    @click="toggleBarberDropdown"
                    class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-left shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                  >
                    <span v-if="!form.barber_id" class="text-gray-500">Select a barber</span>
                    <span v-else class="text-gray-900">{{ getSelectedBarberName() }}</span>
                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </button>

                  <div v-if="showBarberDropdown" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded shadow-lg max-h-60 overflow-auto">
                    <button
                      type="button"
                      v-for="barber in barbers"
                      :key="barber.id"
                      @click="selectBarber(barber)"
                      class="w-full text-left px-3 py-1.5 text-sm font-normal text-gray-900 hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
                    >
                      {{ barber.user ? barber.user.name : 'Barber #' + barber.id }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Desktop Standard Dropdown -->
              <select v-model="form.barber_id" required class="hidden md:block w-full border rounded px-2 py-1">
                <option value="" disabled>Select a barber</option>
                <option v-for="barber in barbers" :key="barber.id" :value="barber.id">
                  {{ barber.user ? barber.user.name : 'Barber #' + barber.id }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">Before Photo</label>
              <input type="file" class="block w-full" @change="e => form.before_photo = e.target.files[0]" />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">After Photo</label>
              <input type="file" class="block w-full" @change="e => form.after_photo = e.target.files[0]" />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">Haircut Style</label>
              <input v-model="form.style" type="text" class="block w-full border rounded px-2 py-1" />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">Review</label>
              <textarea v-model="form.review" class="block w-full border rounded px-2 py-1"></textarea>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">Rating</label>
              <div class="flex items-center space-x-1">
                <template v-for="star in 5" :key="star">
                  <svg
                    @click="setRating(star)"
                    :class="star <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
                    class="w-7 h-7 cursor-pointer transition-colors"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z" />
                  </svg>
                </template>
              </div>
            </div>
            <div class="flex justify-end gap-2">
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" :disabled="submitting">
                <span v-if="submitting">Submitting...</span>
                <span v-else>Submit</span>
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
import { useForm } from '@inertiajs/vue3'
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
  // Reset file inputs visually
  document.querySelectorAll('input[type=file]').forEach(input => input.value = '')
}

const submitForm = () => {
  submitting.value = true
  console.log('Submitting form:', form.style, form)
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

// Mobile dropdown functions
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

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (barberDropdownRef.value && !barberDropdownRef.value.contains(event.target)) {
    showBarberDropdown.value = false
  }
}

// Add click outside listener
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

// Clean up listener
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<script>
export default {
  props: {
    barbers: Array,
    latestBookingId: {
      type: [Number, String],
      required: true,
    },
    alreadySubmitted: {
      type: Boolean,
      required: true,
    },
  },
}
</script>
