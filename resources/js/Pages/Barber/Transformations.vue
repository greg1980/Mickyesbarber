<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  transformations: {
    type: Object,
    default: () => ({ data: [] })
  }
})

const openAccordion = ref({})
const toggleAccordion = id => {
  openAccordion.value[id] = !openAccordion.value[id]
}

// Computed statistics
const totalTransformations = computed(() => props.transformations.total || props.transformations.data.length)
const averageRating = computed(() => {
  const ratingsWithValue = props.transformations.data.filter(t => t.rating && t.rating > 0)
  if (ratingsWithValue.length === 0) return 0
  const sum = ratingsWithValue.reduce((acc, t) => acc + t.rating, 0)
  return (sum / ratingsWithValue.length).toFixed(1)
})
const reviewsCount = computed(() => props.transformations.data.filter(t => t.review && t.review.trim()).length)
</script>

<template>
  <SidebarLayout>
    <div class="p-4 sm:p-6 lg:p-8 space-y-6 bg-gray-50 min-h-screen">
      <!-- Modern Header with Gradient -->
      <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">My Transformations</h1>
              <p class="text-gray-600">View customer feedback and transformations</p>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="hidden lg:flex items-center space-x-4">
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
              <div class="text-sm text-gray-500">Total</div>
              <div class="text-lg font-semibold text-gray-900">{{ totalTransformations }}</div>
            </div>
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
              <div class="text-sm text-gray-500">Avg Rating</div>
              <div class="text-lg font-semibold text-yellow-600">{{ averageRating }}/5</div>
            </div>
            <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
              <div class="text-sm text-gray-500">Reviews</div>
              <div class="text-lg font-semibold text-gray-900">{{ reviewsCount }}</div>
            </div>
          </div>
        </div>

        <!-- Mobile Stats -->
        <div class="lg:hidden mt-4 grid grid-cols-3 gap-3">
          <div class="bg-white rounded-lg px-3 py-2 shadow-sm border border-gray-200 text-center">
            <div class="text-xs text-gray-500">Total</div>
            <div class="text-sm font-semibold text-gray-900">{{ totalTransformations }}</div>
          </div>
          <div class="bg-white rounded-lg px-3 py-2 shadow-sm border border-gray-200 text-center">
            <div class="text-xs text-gray-500">Avg Rating</div>
            <div class="text-sm font-semibold text-yellow-600">{{ averageRating }}/5</div>
          </div>
          <div class="bg-white rounded-lg px-3 py-2 shadow-sm border border-gray-200 text-center">
            <div class="text-xs text-gray-500">Reviews</div>
            <div class="text-sm font-semibold text-gray-900">{{ reviewsCount }}</div>
          </div>
        </div>
      </div>

      <!-- Enhanced Empty State -->
      <div v-if="!transformations.data.length" class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <div class="mx-auto h-24 w-24 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center mb-6">
          <svg class="h-12 w-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No transformations yet</h3>
        <p class="text-gray-500 mb-6 max-w-md mx-auto">
          When customers submit transformation photos and reviews after their appointments, they will appear here for you to view and celebrate your work.
        </p>
      </div>

      <!-- Modern Mobile Card View -->
      <div v-else class="sm:hidden space-y-4">
        <div
          v-for="t in transformations.data"
          :key="t.id"
          class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200"
        >
          <!-- Card Header -->
          <div class="p-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <img
                  :src="t.user?.profile_photo_url || '/images/default-avatar.png'"
                  :alt="t.user?.name"
                  class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100"
                />
                <div>
                  <div class="text-sm font-semibold text-gray-900">{{ t.user?.name }}</div>
                  <div class="text-xs text-gray-500">{{ new Date(t.created_at).toLocaleDateString('en-GB', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                  }) }}</div>
                </div>
              </div>

              <!-- Rating Badge -->
              <div v-if="t.rating" class="flex items-center space-x-1 bg-yellow-50 px-2 py-1 rounded-full border border-yellow-200">
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <span class="text-sm font-semibold text-yellow-700">{{ t.rating }}</span>
              </div>
              <div v-else class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-full">No rating</div>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-4">
            <!-- Star Rating Display -->
            <div v-if="t.rating" class="mb-4">
              <div class="flex items-center space-x-2 mb-2">
                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Rating</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="flex">
                  <svg
                    v-for="star in 5"
                    :key="star"
                    :class="star <= t.rating ? 'text-yellow-400' : 'text-gray-300'"
                    class="w-5 h-5"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                  </svg>
                </div>
                <span class="text-lg font-bold text-gray-900">{{ t.rating }}/5</span>
              </div>
            </div>

            <!-- Review Section -->
            <div class="space-y-3">
              <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Review</span>
              </div>

              <div v-if="t.review">
                <button
                  @click="toggleAccordion(t.id)"
                  class="w-full text-left bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-3 border border-green-200 hover:from-green-100 hover:to-blue-100 transition-all duration-200"
                >
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-green-700">
                      {{ openAccordion[t.id] ? 'Hide Review' : 'Read Review' }}
                    </span>
                    <svg
                      :class="{'rotate-180': openAccordion[t.id]}"
                      class="w-4 h-4 text-green-500 transition-transform duration-200"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </button>
                <div
                  v-if="openAccordion[t.id]"
                  class="mt-3 bg-white p-4 rounded-lg border border-gray-200 shadow-sm"
                >
                  <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-gray-700 italic leading-relaxed">"{{ t.review }}"</div>
                  </div>
                </div>
              </div>
              <div v-else class="text-sm text-gray-400 italic bg-gray-50 p-3 rounded-lg border border-gray-200">
                No review provided by customer
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modern Desktop Table -->
      <div v-if="transformations.data.length" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hidden sm:block">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-900">Transformation Records</h3>
          <p class="text-sm text-gray-600 mt-1">Customer feedback and ratings for your work</p>
        </div>

        <!-- Table -->
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                <div class="flex items-center space-x-2">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  <span>Customer</span>
                </div>
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                <div class="flex items-center space-x-2">
                  <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                  </svg>
                  <span>Rating</span>
                </div>
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                <div class="flex items-center space-x-2">
                  <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                  <span>Review</span>
                </div>
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                <div class="flex items-center space-x-2">
                  <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span>Date</span>
                </div>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="t in transformations.data" :key="t.id" class="hover:bg-gray-50 transition-colors duration-150">
              <!-- Customer -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-3">
                  <img
                    :src="t.user?.profile_photo_url || '/images/default-avatar.png'"
                    :alt="t.user?.name"
                    class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100"
                  />
                  <div class="text-sm font-medium text-gray-900">{{ t.user?.name }}</div>
                </div>
              </td>

              <!-- Rating -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div v-if="t.rating" class="flex items-center space-x-2">
                  <div class="flex">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      :class="star <= t.rating ? 'text-yellow-400' : 'text-gray-300'"
                      class="w-4 h-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                  </div>
                  <span class="text-sm font-semibold text-gray-900">{{ t.rating }}/5</span>
                </div>
                <div v-else class="flex items-center space-x-2">
                  <span class="text-sm text-gray-400 bg-gray-100 px-2 py-1 rounded-full">No rating</span>
                </div>
              </td>

              <!-- Review -->
              <td class="px-6 py-4 max-w-md">
                <div v-if="t.review">
                  <button
                    @click="toggleAccordion(t.id)"
                    class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline transition-colors duration-200"
                  >
                    <span>{{ openAccordion[t.id] ? 'Hide' : 'Read' }} Review</span>
                    <svg
                      :class="{'rotate-180': openAccordion[t.id]}"
                      class="w-4 h-4 transition-transform duration-200"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                  <div v-if="openAccordion[t.id]" class="mt-3 bg-gradient-to-r from-blue-50 to-green-50 p-3 rounded-lg border border-blue-200">
                    <div class="text-sm text-gray-700 italic leading-relaxed">"{{ t.review }}"</div>
                  </div>
                </div>
                <span v-else class="text-sm text-gray-400 bg-gray-100 px-2 py-1 rounded-full">No review</span>
              </td>

              <!-- Date -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ new Date(t.created_at).toLocaleDateString('en-GB', {
                  year: 'numeric',
                  month: 'short',
                  day: 'numeric'
                }) }}</div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Enhanced Pagination -->
        <div v-if="transformations.links && transformations.links.length > 3" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing <span class="font-medium text-gray-900">{{ transformations.from || 0 }}</span> to
                <span class="font-medium text-gray-900">{{ transformations.to || 0 }}</span> of
                <span class="font-medium text-gray-900">{{ transformations.total || transformations.data.length }}</span> results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
                <Link
                  v-for="link in transformations.links"
                  :key="link.label"
                  :href="link.url || ''"
                  preserve-scroll
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-200',
                    link.active
                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                    !link.url ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
                  ]"
                  v-html="link.label"
                />
              </nav>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Mobile Pagination -->
      <div v-if="transformations.data.length && transformations.links && transformations.links.length > 3" class="sm:hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
          <div class="flex items-center justify-between">
            <Link
              v-if="transformations.prev_page_url"
              :href="transformations.prev_page_url"
              preserve-scroll
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200 space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              <span>Previous</span>
            </Link>
            <span v-else class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              <span>Previous</span>
            </span>

            <div class="text-center">
              <span class="text-sm text-gray-600 font-medium">
                Page {{ transformations.current_page || 1 }} of {{ transformations.last_page || 1 }}
              </span>
            </div>

            <Link
              v-if="transformations.next_page_url"
              :href="transformations.next_page_url"
              preserve-scroll
              class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200 space-x-2"
            >
              <span>Next</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </Link>
            <span v-else class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed space-x-2">
              <span>Next</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </span>
          </div>
        </div>
      </div>
    </div>
  </SidebarLayout>
</template>
