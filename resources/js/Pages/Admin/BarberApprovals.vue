<template>
  <SidebarLayout>
    <div class="max-w-7xl mx-auto px-4 py-8 bg-gray-50 min-h-screen">
              <h1 class="text-2xl font-bold mb-6 text-gray-600 flex items-center gap-2">
            <CheckBadgeIcon class="w-6 h-6" />
            Approve Barbers
        </h1>
      <div class="bg-white shadow border border-gray-200 rounded-lg p-6">
        <p class="text-gray-600 mb-4">This is the admin barber approval page. Here you will be able to view and approve pending barbers.</p>
        <!-- Pending barbers placeholder -->
        <div class="border border-dashed border-gray-300 rounded p-8 text-center text-gray-400">
          Pending barber approvals will appear here.
        </div>
      </div>
    </div>
  </SidebarLayout>
</template>

<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { CheckBadgeIcon } from '@heroicons/vue/24/outline'
</script>
