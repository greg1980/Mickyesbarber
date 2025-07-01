<template>
  <SidebarLayout>
    <div class="max-w-7xl mx-auto px-4 py-8 bg-gray-50 min-h-screen">
              <h1 class="text-2xl font-bold mb-6 text-gray-600 flex items-center gap-2">
            <div class="h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center">
                <SparklesIcon class="w-5 h-5 text-purple-600" />
            </div>
            Manage Transformations
        </h1>
      <div class="bg-white shadow border border-gray-200 rounded-lg p-6">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Before</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">After</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Rating</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="t in transformations" :key="t.id">
              <td class="px-4 py-3 flex items-center gap-2">
                <img :src="t.user.profile_photo_url" class="w-10 h-10 rounded-full object-cover border" />
                <span>{{ t.user.name }}</span>
              </td>
              <td class="px-4 py-3">
                <div class="relative w-16 h-16">
                  <img v-if="t.before" :src="t.before" class="w-16 h-16 object-cover rounded border" @error="handleImageError" />
                  <div v-else class="w-16 h-16 bg-gray-200 rounded border flex items-center justify-center text-gray-500 text-xs">No Image</div>
                  <div class="image-placeholder w-16 h-16 bg-gray-200 rounded border flex items-center justify-center text-gray-500 text-xs absolute top-0 left-0 hidden">No Image</div>
                </div>
              </td>
              <td class="px-4 py-3">
                <div class="relative w-16 h-16">
                  <img v-if="t.after" :src="t.after" class="w-16 h-16 object-cover rounded border" @error="handleImageError" />
                  <div v-else class="w-16 h-16 bg-gray-200 rounded border flex items-center justify-center text-gray-500 text-xs">No Image</div>
                  <div class="image-placeholder w-16 h-16 bg-gray-200 rounded border flex items-center justify-center text-gray-500 text-xs absolute top-0 left-0 hidden">No Image</div>
                </div>
              </td>
              <td class="px-4 py-3">
                <span class="flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="#FCAF17" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a1.5 1.5 0 0 1 2.72 0l2.09 4.241a1.5 1.5 0 0 0 1.13.82l4.674.679a1.5 1.5 0 0 1 .832 2.56l-3.382 3.298a1.5 1.5 0 0 0-.43 1.327l.799 4.654a1.5 1.5 0 0 1-2.18 1.582l-4.186-2.204a1.5 1.5 0 0 0-1.396 0l-4.186 2.204a1.5 1.5 0 0 1-2.18-1.582l.8-4.654a1.5 1.5 0 0 0-.43-1.327l-3.383-3.298a1.5 1.5 0 0 1 .832-2.56l4.674-.68a1.5 1.5 0 0 0 1.13-.819l2.09-4.242Z" />
                  </svg>
                  <span>{{ t.rating }}</span>
                </span>
              </td>
              <td class="px-4 py-3">
                <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded mr-2" @click="approveTransformation(t.id)">Approve</button>
                <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded" @click="rejectTransformation(t.id)">Reject</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </SidebarLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import axios from 'axios'
import { SparklesIcon } from '@heroicons/vue/24/outline'

const transformations = ref([])

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
  const placeholder = event.target.parentNode.querySelector('.image-placeholder')
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
