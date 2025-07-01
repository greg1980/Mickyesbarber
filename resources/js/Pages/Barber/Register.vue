<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-8 px-4">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold mb-6 flex items-center gap-2 text-gray-600">
        <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <span>Register as a Barber</span>
      </h1>
      <form @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block text-gray-700 font-semibold mb-1">Bio</label>
          <textarea v-model="form.bio" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" required></textarea>
          <div v-if="errors.bio" class="text-red-600 text-sm mt-1">{{ errors.bio }}</div>
        </div>
        <div>
          <label class="block text-gray-700 font-semibold mb-1">Years of Experience</label>
          <input type="number" v-model="form.years_of_experience" min="0" max="80" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          <div v-if="errors.years_of_experience" class="text-red-600 text-sm mt-1">{{ errors.years_of_experience }}</div>
        </div>
        <div>
          <label class="block text-gray-700 font-semibold mb-1">Mobile Contact</label>
          <input type="text" v-model="form.mobile_contact" maxlength="30" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required />
          <div v-if="errors.mobile_contact" class="text-red-600 text-sm mt-1">{{ errors.mobile_contact }}</div>
        </div>
        <button type="submit" :disabled="loading" class="w-full py-2 px-4 bg-gray-800 text-white font-semibold rounded hover:bg-gray-900 transition-all disabled:opacity-50">
          <span v-if="loading">Submitting...</span>
          <span v-else>Submit Application</span>
        </button>
      </form>
      <div v-if="serverError" class="text-red-600 text-center mt-4">{{ serverError }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const form = ref({
  bio: '',
  years_of_experience: '',
  mobile_contact: ''
})
const errors = ref({})
const loading = ref(false)
const serverError = ref('')

function submit() {
  loading.value = true
  errors.value = {}
  serverError.value = ''
  router.post(route('barber.register.store'), form.value, {
    onError: (e) => {
      errors.value = e
      loading.value = false
    },
    onSuccess: () => {
      loading.value = false
    },
    onFinish: () => {
      loading.value = false
    },
    onFailure: (e) => {
      serverError.value = 'Submission failed. Please try again.'
      loading.value = false
    }
  })
}
</script>
