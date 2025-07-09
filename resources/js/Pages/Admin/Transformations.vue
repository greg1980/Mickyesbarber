<template>
  <SidebarLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <!-- Modern Header -->
      <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white py-8 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-center text-center">
            <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
              <SparklesIcon class="w-8 h-8 text-purple-600" />
            </div>
            <div>
              <h1 class="text-3xl lg:text-4xl font-bold mb-2">Manage Transformations</h1>
              <p class="text-purple-100 text-lg">Review and approve customer transformation submissions</p>
            </div>
          </div>
        </div>
      </div>

      <main class="py-8 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

          <!-- Stats Overview -->
          <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center">
                <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Pending Review</p>
                  <p class="text-2xl font-bold text-gray-900">{{ transformations.length }}</p>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center">
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Average Rating</p>
                  <p class="text-2xl font-bold text-gray-900">{{ averageRating }}</p>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center">
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                  <SparklesIcon class="w-6 h-6 text-blue-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">This Week</p>
                  <p class="text-2xl font-bold text-gray-900">{{ transformations.length }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Transformations Grid -->
          <div v-if="transformations.length === 0" class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <SparklesIcon class="w-12 h-12 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No pending transformations</h3>
            <p class="text-gray-500">All transformations have been reviewed.</p>
          </div>

          <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <div
              v-for="transformation in transformations"
              :key="transformation.id"
              class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full"
            >
              <!-- Customer Info Header -->
              <div class="p-6 pb-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <img
                      :src="transformation.user.profile_photo_url"
                      :alt="transformation.user.name"
                      class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm"
                    />
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900">{{ transformation.user.name }}</h3>
                      <div class="flex items-center space-x-1">
                        <svg v-for="i in 5" :key="i"
                             :class="i <= transformation.rating ? 'text-yellow-400' : 'text-gray-300'"
                             class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-600 ml-1">{{ transformation.rating }}/5</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Customer Review -->
              <div class="px-6 py-4 bg-gray-50 min-h-[100px] flex flex-col justify-center">
                <div v-if="transformation.review">
                  <h4 class="text-sm font-semibold text-gray-700 mb-2">Customer Review:</h4>
                  <p class="text-sm text-gray-600 italic leading-relaxed" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ transformation.review }}</p>
                </div>
                <div v-else class="text-center">
                  <p class="text-sm text-gray-400 italic">No review provided</p>
                </div>
              </div>

              <!-- Before/After Images -->
              <div class="p-6 flex-1 flex flex-col justify-end">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                  <!-- Before Image -->
                  <div class="relative">
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 relative group">
                      <img
                        v-if="transformation.before"
                        :src="transformation.before"
                        alt="Before transformation"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        @error="handleImageError"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                        <div class="text-center">
                          <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Before</p>
                          <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                          <p class="text-xs">No Image</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- After Image -->
                  <div class="relative">
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 relative group">
                      <img
                        v-if="transformation.after"
                        :src="transformation.after"
                        alt="After transformation"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        @error="handleImageError"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                        <div class="text-center">
                          <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">After</p>
                          <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                          <p class="text-xs">No Image</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                  <button
                    @click="approveTransformation(transformation.id)"
                    class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Approve
                  </button>

                  <button
                    @click="rejectTransformation(transformation.id)"
                    class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Reject
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </SidebarLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import axios from 'axios'
import { SparklesIcon } from '@heroicons/vue/24/outline'

const transformations = ref([])

const averageRating = computed(() => {
  if (transformations.value.length === 0) return '0.0'
  const total = transformations.value.reduce((sum, t) => sum + t.rating, 0)
  return (total / transformations.value.length).toFixed(1)
})

onMounted(async () => {
  try {
    const res = await axios.get('/admin/transformations/pending')
    transformations.value = res.data
  } catch (error) {
    console.error('Error loading transformations:', error)
  }
})

function handleImageError(event) {
  console.error('Error loading image:', event.target.src)
  event.target.style.display = 'none'
  const placeholder = event.target.parentNode.querySelector('.text-center')
  if (placeholder) {
    placeholder.style.display = 'flex'
  }
}

async function approveTransformation(id) {
  if (!confirm('Approve this transformation?')) return;

  try {
    // Get fresh CSRF token
    const csrfResponse = await axios.get('/refresh-csrf')
    const csrfToken = csrfResponse.data.csrf_token

    await axios.post(`/admin/transformations/${id}/approve`, {}, {
      headers: {
        'X-CSRF-TOKEN': csrfToken
      }
    })

    transformations.value = transformations.value.filter(t => t.id !== id)
  } catch (error) {
    console.error('Error approving transformation:', error)
    alert('Error approving transformation. Please try again.')
  }
}

async function rejectTransformation(id) {
  if (!confirm('Reject this transformation?')) return;

  try {
    // Get fresh CSRF token
    const csrfResponse = await axios.get('/refresh-csrf')
    const csrfToken = csrfResponse.data.csrf_token

    await axios.post(`/admin/transformations/${id}/reject`, {}, {
      headers: {
        'X-CSRF-TOKEN': csrfToken
      }
    })

    transformations.value = transformations.value.filter(t => t.id !== id)
  } catch (error) {
    console.error('Error rejecting transformation:', error)
    alert('Error rejecting transformation. Please try again.')
  }
}
</script>
