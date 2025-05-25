<template>
  <SidebarLayout>
    <div class="min-h-screen bg-gray-50">
      <div class="max-w-3xl mx-auto py-12">
        <h1 class="text-3xl font-bold mb-4">My Transformations</h1>

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
            <h2 class="text-lg font-bold mb-4">Submit Transformation</h2>
            <input type="hidden" v-model="form.booking_id" />
            <div class="mb-4">
              <label class="block text-sm font-medium mb-1">Barber</label>
              <select v-model="form.barber_id" required class="block w-full border rounded px-2 py-1">
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
import { ref } from 'vue'
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
