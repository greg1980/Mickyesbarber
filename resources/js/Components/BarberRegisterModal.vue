<template>
  <Modal :show="show" @close="emit('close')" maxWidth="lg">
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
          <div class="h-10 w-10 bg-gradient-to-br from-gray-400 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
          </div>
          Add New Barber
        </h2>
        <button @click="emit('close')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
          <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- User Type Selection -->
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Select Registration Type</label>
        <div class="grid grid-cols-2 gap-3">
          <button
            @click="userType = 'existing'"
            :class="[
              'p-4 border-2 rounded-xl text-left transition-all duration-200',
              userType === 'existing'
                ? 'border-gray-500 bg-gray-50 shadow-md'
                : 'border-gray-200 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-3">
              <div :class="[
                'w-4 h-4 rounded-full border-2 transition-all duration-200',
                userType === 'existing'
                  ? 'border-gray-500 bg-gray-500'
                  : 'border-gray-300'
              ]">
                <div v-if="userType === 'existing'" class="w-2 h-2 bg-white rounded-full m-0.5"></div>
              </div>
              <div>
                <div class="font-semibold text-gray-900">Existing User</div>
                <div class="text-sm text-gray-600">User already has an account</div>
              </div>
            </div>
          </button>

          <button
            @click="userType = 'new'"
            :class="[
              'p-4 border-2 rounded-xl text-left transition-all duration-200',
              userType === 'new'
                ? 'border-gray-500 bg-gray-50 shadow-md'
                : 'border-gray-200 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-3">
              <div :class="[
                'w-4 h-4 rounded-full border-2 transition-all duration-200',
                userType === 'new'
                  ? 'border-gray-500 bg-gray-500'
                  : 'border-gray-300'
              ]">
                <div v-if="userType === 'new'" class="w-2 h-2 bg-white rounded-full m-0.5"></div>
              </div>
              <div>
                <div class="font-semibold text-gray-900">New User</div>
                <div class="text-sm text-gray-600">Create account + barber profile</div>
              </div>
            </div>
          </button>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-5">
        <!-- New User Fields -->
        <div v-if="userType === 'new'" class="space-y-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
          <h3 class="font-semibold text-blue-900 mb-3">User Account Information</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
              placeholder="Enter full name"
            />
            <div v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
              placeholder="Enter email address"
            />
            <div v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
            <input
              v-model="form.password"
              type="password"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
              placeholder="Enter password (min 8 characters)"
              minlength="8"
            />
            <div v-if="errors.password" class="text-red-600 text-sm mt-1">{{ errors.password }}</div>
          </div>
        </div>

        <!-- Existing User Fields -->
        <div v-if="userType === 'existing'" class="space-y-4 p-4 bg-green-50 rounded-xl border border-green-200">
          <h3 class="font-semibold text-green-900 mb-3">Find Existing User</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">User Email *</label>
            <input
              v-model="form.existing_email"
              type="email"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
              placeholder="Enter user's email address"
            />
            <div v-if="errors.existing_email" class="text-red-600 text-sm mt-1">{{ errors.existing_email }}</div>
          </div>
        </div>

        <!-- Barber Profile Information (Common for both types) -->
        <div class="space-y-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
          <h3 class="font-semibold text-gray-900 mb-3">Barber Profile Information</h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bio *</label>
            <textarea
              v-model="form.bio"
              rows="3"
              required
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
              placeholder="Tell us about their experience and expertise..."
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
            <span v-if="loading">Adding Barber...</span>
            <span v-else>Add Barber</span>
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
  show: Boolean
})

const userType = ref('existing')
const loading = ref(false)
const errors = ref({})

const form = reactive({
  // New user fields
  name: '',
  email: '',
  password: '',

  // Existing user fields
  existing_email: '',

  // Barber profile fields
  bio: '',
  years_of_experience: '',
  mobile_contact: ''
})

const submit = async () => {
  loading.value = true
  errors.value = {}

  const formData = {
    user_type: userType.value,
    bio: form.bio,
    years_of_experience: form.years_of_experience,
    mobile_contact: form.mobile_contact
  }

  if (userType.value === 'new') {
    formData.name = form.name
    formData.email = form.email
    formData.password = form.password
  } else {
    formData.existing_email = form.existing_email
  }

  try {
    await router.post(route('admin.barber.add'), formData, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        emit('success')
        emit('close')
        // Reset form
        Object.keys(form).forEach(key => form[key] = '')
        userType.value = 'existing'
      },
      onError: (formErrors) => {
        errors.value = formErrors
      },
      onFinish: () => {
        loading.value = false
      }
    })
  } catch (error) {
    console.error('Error adding barber:', error)
    loading.value = false
  }
}
</script>
