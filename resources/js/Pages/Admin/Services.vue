<template>
  <SidebarLayout>
    <Head title="Admin - Services" />
    <div class="max-w-7xl mx-auto px-4 py-8 bg-gray-50 min-h-screen">
            <h1 class="text-2xl font-bold mb-6 text-gray-600 flex items-center gap-2">
            <div class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center">
                <CogIcon class="w-5 h-5 text-gray-600" />
            </div>
            Service Management
        </h1>

      <!-- Success Message -->
      <div v-if="showSuccessMessage" class="mb-4 p-3 bg-green-100 text-green-800 rounded border border-green-200 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ successMessage }}
      </div>

      <!-- Error Message -->
      <div v-if="showErrorMessage" class="mb-4 p-3 bg-red-100 text-red-800 rounded border border-red-200 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ errorMessage }}
      </div>

      <!-- Service Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Services</p>
              <p class="text-3xl font-bold text-gray-600">{{ totalServices }}</p>
            </div>
            <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white shadow border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Active Services</p>
              <p class="text-3xl font-bold text-green-600">{{ activeServices }}</p>
            </div>
            <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white shadow border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Inactive Services</p>
              <p class="text-3xl font-bold text-red-600">{{ inactiveServices }}</p>
            </div>
            <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white shadow border border-gray-200 rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold flex items-center gap-2">
            <div class="h-6 w-6 bg-purple-100 rounded-full flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-purple-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
              </svg>
            </div>
            Service List
          </h2>
          <button
            @click="openCreateModal"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium"
          >
            Add New Service
          </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2 sm:gap-0">
          <div class="flex items-center space-x-2">
            <label for="entriesPerPage" class="text-sm text-gray-700">Show</label>
            <select id="entriesPerPage" class="border rounded px-2 py-1 text-sm w-20">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
            </select>
            <span class="text-sm text-gray-700">entries per page</span>
          </div>
          <div class="w-full sm:w-auto mt-2 sm:mt-0">
            <input
              v-model="searchTerm"
              @input="performSearch"
              type="text"
              placeholder="Search services..."
              class="border rounded px-3 py-1 text-sm w-full sm:w-64"
            />
          </div>
          <div class="flex items-center gap-2 mt-2 sm:mt-0">
            <button
              :class="['px-3 py-1 rounded', statusFilter === '' ? 'bg-gray-500 text-white' : 'bg-gray-200 text-gray-700']"
              @click="setStatusFilter('')"
            >
              All Status
            </button>
            <button
              :class="['px-3 py-1 rounded', statusFilter === 'active' ? 'bg-gray-500 text-white' : 'bg-gray-200 text-gray-700']"
              @click="setStatusFilter('active')"
            >
              Active
            </button>
            <button
              :class="['px-3 py-1 rounded', statusFilter === 'inactive' ? 'bg-gray-500 text-white' : 'bg-gray-200 text-gray-700']"
              @click="setStatusFilter('inactive')"
            >
              Inactive
            </button>
          </div>
        </div>

        <!-- Services Table -->
        <div class="overflow-x-auto relative">
          <div class="sm:hidden text-xs text-gray-400 mb-2">Swipe left/right to see more columns</div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Service</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Duration</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Status</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Order</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="service in services.data" :key="service.id">
                <td class="px-2 py-2 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full flex items-center justify-center shadow-lg" :class="getServiceIconBg(service.name)">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="service.name.toLowerCase().includes('classic') || service.name.toLowerCase().includes('haircut')" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM7 3V1m10 20a4 4 0 004-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4zM17 3V1M9 7h6m-6 4h6m-6 4h6" />
                          <path v-else-if="service.name.toLowerCase().includes('kids')" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H9M15 10h-1.5A1.5 1.5 0 0112 8.5V6a1.5 1.5 0 011.5-1.5H15m-3 7a2 2 0 100-4 2 2 0 000 4z" />
                          <path v-else-if="service.name.toLowerCase().includes('color')" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM7 3V1m0 18v2m8-2a4 4 0 004-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4z" />
                          <path v-else-if="service.name.toLowerCase().includes('beard')" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          <path v-else-if="service.name.toLowerCase().includes('mobile')" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.516l2.257-1.13a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
                        </svg>
                      </div>
                    </div>
                    <div class="ml-2 sm:ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ service.name }}</div>
                      <div class="text-xs sm:text-sm text-gray-500">{{ service.slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-2 py-2 whitespace-nowrap font-medium text-gray-900">
                  £{{ service.price }}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-gray-700 hidden sm:table-cell">
                  {{ service.duration ? service.duration + ' mins' : getServiceDuration(service.name) }}
                </td>
                <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell">
                  <span
                    :class="service.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  >
                    {{ service.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-gray-700 hidden md:table-cell">
                  {{ service.sort_order }}
                </td>
                <td class="px-2 py-2 whitespace-nowrap flex space-x-2 hidden md:table-cell">
                  <button
                    @click="editService(service)"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteService(service)"
                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4">
          <div class="text-sm text-gray-600">
            Showing {{ services.from }} to {{ services.to }} of {{ services.total }} entries
          </div>
          <div class="flex space-x-1" v-if="services.links">
            <template v-for="link in services.links" :key="link.label">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'px-3 py-1 rounded border',
                  link.active ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700'
                ]"
                v-html="link.label"
              />
              <span
                v-else
                :class="[
                  'px-3 py-1 rounded border opacity-50 cursor-not-allowed',
                  'bg-gray-100 text-gray-700'
                ]"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Service Modal -->
    <Modal :show="showModal" @close="closeModal">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingService ? 'Edit Service' : 'Add New Service' }}
        </h3>

        <form @submit.prevent="saveService" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
            ></textarea>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Price (£)</label>
              <input
                v-model="form.price"
                type="number"
                step="0.01"
                min="0"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Duration (mins)</label>
              <input
                v-model="form.duration"
                type="number"
                min="5"
                placeholder="e.g., 30"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Sort Order</label>
              <input
                v-model="form.sort_order"
                type="number"
                min="0"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Icon</label>
              <input
                v-model="form.icon"
                type="text"
                placeholder="e.g., ScissorsIcon"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Image Path</label>
              <input
                v-model="form.image"
                type="text"
                placeholder="e.g., /images/services/service.jpg"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
            />
            <label class="ml-2 block text-sm text-gray-900">Active</label>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
            >
              {{ processing ? 'Saving...' : 'Save Service' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </SidebarLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { CogIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  services: Object,
  filters: Object,
  totalServices: Number,
  activeServices: Number,
  inactiveServices: Number,
})

const showModal = ref(false)
const editingService = ref(null)
const processing = ref(false)
const searchTerm = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')
const showSuccessMessage = ref(false)
const successMessage = ref('')
const showErrorMessage = ref(false)
const errorMessage = ref('')

const form = reactive({
  name: '',
  description: '',
  price: '',
  duration: '',
  icon: '',
  image: '',
  is_active: true,
  sort_order: '',
})

const openCreateModal = () => {
  editingService.value = null
  resetForm()
  showModal.value = true
}

const editService = (service) => {
  editingService.value = service
  form.name = service.name
  form.description = service.description
  form.price = service.price
  form.duration = service.duration || ''
  form.icon = service.icon || ''
  form.image = service.image || ''
  form.is_active = service.is_active
  form.sort_order = service.sort_order
  showModal.value = true
}

const resetForm = () => {
  form.name = ''
  form.description = ''
  form.price = ''
  form.duration = ''
  form.icon = ''
  form.image = ''
  form.is_active = true
  form.sort_order = ''
}

const closeModal = () => {
  showModal.value = false
  resetForm()
  editingService.value = null
}

const showSuccess = (message) => {
  successMessage.value = message
  showSuccessMessage.value = true
  showErrorMessage.value = false
  setTimeout(() => {
    showSuccessMessage.value = false
  }, 5000)
}

const showError = (message) => {
  errorMessage.value = message
  showErrorMessage.value = true
  showSuccessMessage.value = false
  setTimeout(() => {
    showErrorMessage.value = false
  }, 5000)
}

const saveService = async () => {
  processing.value = true

  try {
    const url = editingService.value
      ? `/admin/services/${editingService.value.id}`
      : '/admin/services'

    const method = editingService.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify(form),
    })

    if (response.ok) {
      const data = await response.json()
      closeModal()
      const action = editingService.value ? 'updated' : 'created'
      showSuccess(`Service ${action} successfully!`)
      router.reload()
    } else {
      const error = await response.json()
      showError(error.message || 'An error occurred while saving the service')
    }
  } catch (error) {
    console.error('Error saving service:', error)
    showError('An error occurred while saving the service')
  } finally {
    processing.value = false
  }
}

const toggleService = async (service) => {
  try {
    const response = await fetch(`/admin/services/${service.id}/toggle`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })

    if (response.ok) {
      const data = await response.json()
      showSuccess('Service status updated successfully!')
      router.reload()
    } else {
      const error = await response.json()
      showError(error.message || 'An error occurred while updating the service')
    }
  } catch (error) {
    console.error('Error toggling service:', error)
    showError('An error occurred while updating the service')
  }
}

const deleteService = async (service) => {
  if (!confirm('Are you sure you want to delete this service?')) {
    return
  }

  try {
    const response = await fetch(`/admin/services/${service.id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })

    if (response.ok) {
      showSuccess('Service deleted successfully!')
      router.reload()
    } else {
      const error = await response.json()
      showError(error.error || 'An error occurred while deleting the service')
    }
  } catch (error) {
    console.error('Error deleting service:', error)
    showError('An error occurred while deleting the service')
  }
}

const setStatusFilter = (status) => {
  statusFilter.value = status
  performSearch()
}

const performSearch = () => {
  router.get(route('admin.services.index'), {
    search: searchTerm.value,
    status: statusFilter.value,
  }, {
    preserveState: true,
    replace: true,
  })
}

const getServiceIconBg = (serviceName) => {
  const name = serviceName.toLowerCase()
  if (name.includes('classic') || name.includes('haircut')) {
    return 'bg-gradient-to-br from-blue-400 to-blue-600'
  } else if (name.includes('kids')) {
    return 'bg-gradient-to-br from-orange-400 to-orange-600'
  } else if (name.includes('color')) {
    return 'bg-gradient-to-br from-purple-400 to-purple-600'
  } else if (name.includes('beard')) {
    return 'bg-gradient-to-br from-emerald-400 to-emerald-600'
  } else if (name.includes('mobile')) {
    return 'bg-gradient-to-br from-indigo-400 to-indigo-600'
  } else {
    return 'bg-gradient-to-br from-rose-400 to-rose-600'
  }
}

const getServiceDuration = (serviceName) => {
  const name = serviceName.toLowerCase()
  if (name.includes('kids')) {
    return '20-25 mins'
  } else if (name.includes('color')) {
    return '60-90 mins'
  } else if (name.includes('beard')) {
    return '15-20 mins'
  } else if (name.includes('mobile')) {
    return '45-60 mins'
  } else if (name.includes('special') || name.includes('treatment')) {
    return '75-90 mins'
  } else {
    return '30-45 mins'
  }
}
</script>

