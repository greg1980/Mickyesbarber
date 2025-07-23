<template>
  <Modal :show="show" @close="emit('close')" maxWidth="lg">
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
          <div class="h-10 w-10 bg-gradient-to-br from-gray-400 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          Register as Barber
        </h2>
        <button @click="emit('close')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
          <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- User Information Display -->
      <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
        <h3 class="font-semibold text-blue-900 mb-3">Your Account Information</h3>
        <div class="space-y-2">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div>
              <div class="font-semibold text-gray-900">{{ user.name }}</div>
              <div class="text-sm text-gray-600">{{ user.email }}</div>
            </div>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-5">
        <!-- Barber Profile Information -->
        <div class="space-y-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
          <h3 class="font-semibold text-gray-900 mb-3">Barber Profile Information</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bio *</label>
            <textarea
              v-model="form.bio"
              rows="3"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
              placeholder="Tell us about your experience and expertise..."
            ></textarea>
            <div v-if="errors.bio" class="text-red-600 text-sm mt-1">{{ errors.bio }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Years of Experience *</label>
            <input
              v-model="form.years_of_experience"
              type="number"
              min="0"
              max="80"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
              placeholder="Enter years of experience"
            />
            <div v-if="errors.years_of_experience" class="text-red-600 text-sm mt-1">{{ errors.years_of_experience }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Contact *</label>
            <input
              v-model="form.mobile_contact"
              type="text"
              maxlength="30"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
              placeholder="Enter mobile contact number"
            />
            <div v-if="errors.mobile_contact" class="text-red-600 text-sm mt-1">
              <div v-for="err in [].concat(errors.mobile_contact)" :key="err">{{ err }}</div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="emit('close')"
            class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition-all duration-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-8 py-2 bg-gradient-to-r from-gray-400 to-gray-600 text-white font-semibold rounded-lg hover:from-gray-500 hover:to-gray-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="loading">Submitting...</span>
            <span v-else>Submit Application</span>
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from './Modal.vue'

const emit = defineEmits(['close', 'success'])

defineProps({
  show: Boolean,
  user: {
    type: Object,
    required: true
  }
})

const loading = ref(false)
const errors = ref({})

const form = reactive({
  bio: '',
  years_of_experience: '',
  mobile_contact: ''
})

const submit = async () => {
  loading.value = true
  errors.value = {}

  try {
    await router.post('/register/barber', form, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        emit('success')
        emit('close')
        // Reset form
        Object.keys(form).forEach(key => form[key] = '')
      },
      onError: (formErrors) => {
        errors.value = formErrors
      },
      onFinish: () => {
        loading.value = false
      }
    })
  } catch (error) {
    console.error('Error submitting barber application:', error)
    loading.value = false
  }
}
</script>
