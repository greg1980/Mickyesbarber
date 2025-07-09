<template>
  <SidebarLayout>
    <div class="max-w-7xl mx-auto px-4 py-8 bg-gray-50 min-h-screen">
              <h1 class="text-2xl font-bold mb-6 text-gray-600 flex items-center gap-2">
            <div class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center">
                <UsersIcon class="w-5 h-5 text-gray-600" />
            </div>
            Manage Users
        </h1>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Users</p>
              <p class="text-3xl font-bold text-gray-600">{{ props.totalUsers }}</p>
            </div>
            <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center">
              <UsersIcon class="h-6 w-6 text-gray-600" />
            </div>
          </div>
        </div>
        <div class="bg-white shadow border border-gray-200 rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Active Users</p>
              <p class="text-3xl font-bold text-green-600">{{ props.activeUsers }}</p>
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
              <p class="text-sm font-medium text-gray-600">Inactive Users</p>
              <p class="text-3xl font-bold text-red-600">{{ props.inactiveUsers }}</p>
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
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
          <div class="h-6 w-6 bg-purple-100 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-purple-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
          </div>
          User List
        </h2>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2 sm:gap-0">
          <div class="flex items-center space-x-2">
            <label for="entriesPerPage" class="text-sm text-gray-700">Show</label>
            <select id="entriesPerPage" v-model="entriesPerPage" class="border rounded px-2 py-1 text-sm w-20">
              <option v-for="option in [10, 25, 50, 100]" :key="option" :value="option">{{ option }}</option>
            </select>
            <span class="text-sm text-gray-700">entries per page</span>
          </div>
          <div class="w-full sm:w-auto mt-2 sm:mt-0">
            <input
              type="text"
              v-model="search"
              @input="submitSearch"
              placeholder="Search by username..."
              class="border rounded px-3 py-1 text-sm w-full sm:w-64"
            />
          </div>
          <div class="flex items-center gap-2 mt-2 sm:mt-0">
            <button :class="['px-3 py-1 rounded', !showTrashed ? 'bg-gray-500 text-white' : 'bg-gray-200 text-gray-700']" @click="setShowTrashed(false)">Active Users</button>
            <button :class="['px-3 py-1 rounded', showTrashed ? 'bg-gray-500 text-white' : 'bg-gray-200 text-gray-700']" @click="setShowTrashed(true)">Deactivated Users</button>
          </div>
        </div>
        <div class="overflow-x-auto relative">
          <div class="sm:hidden text-xs text-gray-400 mb-2">Swipe left/right to see more columns</div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Username</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date Registered</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Role</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Status</th>
                <th class="px-2 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id">
                <td class="px-2 py-2 whitespace-nowrap">
                  <img :src="profilePhoto(user)" :alt="user.name ? 'Profile photo of ' + user.name : 'User profile photo'" class="w-10 h-10 rounded-full object-cover border" />
                </td>
                <td class="px-2 py-2 whitespace-nowrap font-medium text-gray-900">{{ user.name }}</td>
                <td class="px-2 py-2 whitespace-nowrap text-gray-700 hidden sm:table-cell">{{ user.created_at.slice(0, 10) }}</td>
                <td class="px-2 py-2 whitespace-nowrap text-gray-700 hidden sm:table-cell">{{ user.role }}</td>
                <td class="px-2 py-2 whitespace-nowrap hidden md:table-cell">
                  <span :class="statusClass(user)">{{ statusLabel(user) }}</span>
                </td>
                <td v-if="user.role !== 'admin'" class="px-2 py-2 whitespace-nowrap flex space-x-2 hidden md:table-cell">
                  <button class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded" title="View" @click="openUserModal(user)"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></button>
                  <button class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded" title="Edit" @click="openEditModal(user)" v-if="!showTrashed"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="#fff" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m-7-7h14" /></svg></button>
                  <button class="bg-orange-500 hover:bg-orange-600 text-white px-2 py-1 rounded" title="Deactivate" @click="deactivateUser(user)" v-if="!showTrashed"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                  <button class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded" title="Restore" @click="restoreUser(user)" v-if="showTrashed"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13l3 3L22 4" /></svg></button>
                </td>
                <td v-else class="px-2 py-2 whitespace-nowrap flex items-center text-gray-400 italic hidden md:table-cell">Action Access Denied</td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Dummy Pagination -->
        <div class="flex items-center justify-between mt-4">
          <div class="text-sm text-gray-600">
            Showing {{ props.users.from }} to {{ props.users.to }} of {{ props.users.total }} entries
          </div>
          <div class="flex space-x-1">
            <button
              v-for="link in props.users.links"
              :key="link.label"
              :disabled="!link.url"
              @click="goToPage(link.url)"
              v-html="link.label"
              :class="['px-3 py-1 rounded border', link.active ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700', !link.url ? 'opacity-50 cursor-not-allowed' : '']"
            />
          </div>
        </div>
      </div>
    </div>
    <!-- User Details Modal -->
    <div v-if="showUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative">
        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" @click="closeUserModal">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
          </svg>
        </button>
        <div class="flex flex-col items-center">
          <img :src="profilePhoto(selectedUser)" :alt="selectedUser.name ? 'Profile photo of ' + selectedUser.name : 'User profile photo'" class="w-20 h-20 rounded-full object-cover border mb-4" />
          <h3 class="text-xl font-bold mb-2 text-gray-600 flex items-center gap-2">
              <div class="h-6 w-6 bg-gray-100 rounded-full flex items-center justify-center">
                  <UserIcon class="w-4 h-4 text-gray-600" />
              </div>
              {{ selectedUser.name }}
          </h3>
          <p class="text-gray-600 mb-1">Email: {{ selectedUser.email }}</p>
          <p class="text-gray-600 mb-1">Date Registered: {{ selectedUser.created_at?.slice(0, 10) }}</p>
          <p class="text-gray-600 mb-1">Last Visited: <span v-if="lastVisited !== null">{{ lastVisited || 'Never' }}</span><span v-else>Loading...</span></p>
          <p class="text-gray-800 font-semibold mt-4 flex items-center gap-2">
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#FFA511" class="size-5 inline-block align-middle">
                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
              </svg>
            </span>
            Upcoming Bookings: <span v-if="upcomingBookingsCount !== null">{{ upcomingBookingsCount }}</span><span v-else>Loading...</span>
          </p>
          <p class="text-gray-800 font-semibold mt-4 flex items-center gap-2">
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#FFA511" class="size-5 inline-block align-middle">
                <path fill-rule="evenodd" d="M18 5.25a2.25 2.25 0 0 0-2.012-2.238A2.25 2.25 0 0 0 13.75 1h-1.5a2.25 2.25 0 0 0-2.238 2.012c-.875.092-1.6.686-1.884 1.488H11A2.5 2.5 0 0 1 13.5 7v7h2.25A2.25 2.25 0 0 0 18 11.75v-6.5ZM12.25 2.5a.75.75 0 0 0-.75.75v.25h3v-.25a.75.75 0 0 0-.75-.75h-1.5Z" clip-rule="evenodd" />
                <path fill-rule="evenodd" d="M3 6a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H3Zm6.874 4.166a.75.75 0 1 0-1.248-.832l-2.493 3.739-.853-.853a.75.75 0 0 0-1.06 1.06l1.5 1.5a.75.75 0 0 0 1.154-.114l3-4.5Z" clip-rule="evenodd" />
              </svg>
            </span>
            Completed Bookings: <span v-if="completedBookingsCount !== null">{{ completedBookingsCount }}</span><span v-else>Loading...</span>
          </p>
        </div>
      </div>
    </div>
    <!-- Edit User Modal -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative">
        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" @click="closeEditModal">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
          </svg>
        </button>
        <div class="flex flex-col items-center">
          <img :src="profilePhoto(editUser)" :alt="editUser.name ? 'Profile photo of ' + editUser.name : 'User profile photo'" class="w-20 h-20 rounded-full object-cover border mb-4" />
          <h3 class="text-xl font-bold mb-4 text-gray-600 flex items-center gap-2">
              <div class="h-6 w-6 bg-orange-100 rounded-full flex items-center justify-center">
                  <PencilSquareIcon class="w-4 h-4 text-orange-600" />
              </div>
              Edit User
          </h3>
          <form @submit.prevent="saveEditUser" class="w-full">
            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Username</label>
              <input v-model="editUserForm.name" type="text" class="border rounded px-3 py-2 w-full" required />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-1">Status</label>
              <select v-model="editUserForm.status" class="border rounded px-3 py-2 w-full">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="flex justify-end">
              <button type="button" class="mr-2 px-4 py-2 rounded bg-gray-200 hover:bg-gray-300" @click="closeEditModal">Cancel</button>
              <button type="submit" class="px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-600">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </SidebarLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { usePage, router } from '@inertiajs/vue3'
import axios from 'axios'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler)

import { UsersIcon, UserIcon, PencilSquareIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  users: Object,
  filters: Object,
  totalUsers: Number,
  activeUsers: Number,
  inactiveUsers: Number
})

const users = computed(() => props.users.data)
const entriesPerPage = ref(props.users.per_page || 10)

const search = ref(props.filters?.search || '')

function submitSearch() {
  router.get(route('admin.users.index'), { search: search.value }, { preserveState: true, replace: true })
}

const showUserModal = ref(false)
const selectedUser = ref({})
const completedBookingsCount = ref(null)
const lastVisited = ref(null)
const upcomingBookingsCount = ref(null)

function openUserModal(user) {
  selectedUser.value = user
  showUserModal.value = true
  completedBookingsCount.value = null
  lastVisited.value = null
  upcomingBookingsCount.value = null
  // Fetch completed bookings count
  axios.get(`/admin/users/${user.id}/completed-bookings-count`).then(res => {
    completedBookingsCount.value = res.data.count
  }).catch(() => {
    completedBookingsCount.value = 0
  })
  // Fetch last visited and upcoming bookings count
  axios.get(`/admin/users/${user.id}/stats`).then(res => {
    lastVisited.value = res.data.last_visited
    upcomingBookingsCount.value = res.data.upcoming_bookings
  }).catch(() => {
    lastVisited.value = null
    upcomingBookingsCount.value = 0
  })
}
function closeUserModal() {
  showUserModal.value = false
  selectedUser.value = {}
  completedBookingsCount.value = null
  lastVisited.value = null
  upcomingBookingsCount.value = null
}

function statusLabel(user) {
  return user.email_verified_at ? 'Active' : 'Inactive'
}
function statusClass(user) {
  return user.email_verified_at
    ? 'bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold'
    : 'bg-[#f9d7d7] text-red-700 px-2 py-1 rounded text-xs font-semibold'
}

function profilePhoto(user) {
  return user.profile_photo_path
    ? `/storage/${user.profile_photo_path}`
    : 'https://randomuser.me/api/portraits/men/32.jpg'
}

const showEditModal = ref(false)
const editUser = ref({})
const editUserForm = ref({ name: '', status: 'active' })

function openEditModal(user) {
  editUser.value = user
  showEditModal.value = true
  editUserForm.value.name = user.name
  editUserForm.value.status = user.email_verified_at ? 'active' : 'inactive'
}
function closeEditModal() {
  showEditModal.value = false
  editUser.value = {}
  editUserForm.value = { name: '', status: 'active' }
}
async function saveEditUser() {
  try {
    await axios.put(`/admin/users/${editUser.value.id}`, {
      name: editUserForm.value.name,
      status: editUserForm.value.status
    })
    closeEditModal()
    // Refresh the page or data
    router.reload({ only: ['users'] })
  } catch (e) {
    alert('Failed to update user.')
  }
}

async function deactivateUser(user) {
  if (!confirm('Are you sure you want to deactivate this user?')) return;
  try {
    await axios.delete(`/admin/users/${user.id}`)
    router.reload({ only: ['users'] })
  } catch (e) {
    alert('Failed to deactivate user.')
  }
}

const showTrashed = ref(props.filters?.trashed === '1')
function setShowTrashed(val) {
  showTrashed.value = val
  router.get(route('admin.users.index'), { ...props.filters, trashed: val ? 1 : 0, search: search.value }, { preserveState: true, replace: true })
}

async function restoreUser(user) {
  if (!confirm('Are you sure you want to restore this user?')) return;
  try {
    await axios.post(`/admin/users/${user.id}/restore`)
    router.reload({ only: ['users'] })
  } catch (e) {
    alert('Failed to restore user.')
  }
}

const userGrowthData = ref([])
const userGrowthLabels = computed(() => userGrowthData.value.map(item => item.month))
const userGrowthCounts = computed(() => userGrowthData.value.map(item => item.count))

onMounted(async () => {
  const res = await axios.get('/admin/users/growth')
  userGrowthData.value = res.data
})

watch(entriesPerPage, (newVal) => {
  router.get(route('admin.users.index'), { ...props.filters, perPage: newVal, search: search.value }, { preserveState: true, replace: true });
});
</script>

