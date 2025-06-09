<template>
  <Modal :show="show" @close="emit('close')" maxWidth="md">
    <div class="p-6">
      <h2 class="text-xl font-bold mb-4">Register as a Barber</h2>
      <form @submit.prevent="submit" class="space-y-4">
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
          <div v-if="errors.mobile_contact" class="text-red-600 text-sm mt-1">
            <div v-for="err in [].concat(errors.mobile_contact)" :key="err">{{ err }}</div>
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <button type="button" @click="emit('close')" class="px-4 py-2 rounded bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300">Cancel</button>
          <button type="submit" :disabled="loading" class="px-6 py-2 bg-gray-800 text-white font-semibold rounded hover:bg-gray-900 transition-all disabled:opacity-50">
            <span v-if="loading">Submitting...</span>
            <span v-else>Submit</span>
          </button>
        </div>
      </form>
      <div v-if="serverError" class="text-red-600 text-center mt-4">{{ serverError }}</div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from './Modal.vue'

const props = defineProps({
  show: Boolean
})
const emit = defineEmits(['close', 'success'])

const form = ref({
  bio: '',
  years_of_experience: '',
  mobile_contact: ''
})
const errors = ref({})
const loading = ref(false)
const serverError = ref('')

watch(() => props.show, (val) => {
  if (val) {
    // Reset form and errors when modal opens
    form.value = { bio: '', years_of_experience: '', mobile_contact: '' }
    errors.value = {}
    serverError.value = ''
    loading.value = false
  }
})

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
      emit('success')
      loading.value = false
    },
    onFinish: () => {
      loading.value = false
    },
    onFailure: () => {
      serverError.value = 'Submission failed. Please try again.'
      loading.value = false
    }
  })
}
</script>
