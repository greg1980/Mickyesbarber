<template>
    <Head title="Admin Dashboard" />

    <SidebarLayout>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Pending Barber Approvals -->
            <div class="bg-white shadow rounded-xl p-4">
                <h2 class="text-lg font-semibold mb-2">Pending Barber Approvals</h2>
                <div v-if="pendingBarbers.length > 0" class="space-y-4">
                    <div v-for="barber in pendingBarbers" :key="barber.id" class="border-b pb-4 last:border-b-0 last:pb-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">{{ barber.name }}</p>
                                <p class="text-sm text-gray-600">{{ barber.email }}</p>
                                <p class="text-sm text-gray-500">Applied: {{ barber.applied_date }}</p>
                            </div>
                            <button
                                @click="approveBarber(barber.id)"
                                class="px-3 py-1 text-sm font-medium text-white bg-gray-900 rounded-md hover:bg-gray-800"
                            >
                                Approve
                            </button>
                        </div>
                    </div>
                </div>
                <p v-else class="text-gray-600">No pending approvals</p>
            </div>

            <!-- User Statistics -->
            <div class="bg-white shadow rounded-xl p-4">
                <h2 class="text-lg font-semibold mb-2">User Statistics</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Users</span>
                        <span class="font-medium text-gray-900">{{ stats.totalUsers }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Active Barbers</span>
                        <span class="font-medium text-gray-900">{{ stats.activeBarbers }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Appointments</span>
                        <span class="font-medium text-gray-900">{{ stats.totalAppointments }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow rounded-xl p-4">
                <h2 class="text-lg font-semibold mb-2">Recent Activity</h2>
                <div v-if="recentActivity.length > 0" class="space-y-4">
                    <div v-for="activity in recentActivity" :key="activity.id" class="border-b pb-4 last:border-b-0 last:pb-0">
                        <p class="text-sm text-gray-900">{{ activity.description }}</p>
                        <p class="text-xs text-gray-500">{{ activity.date }}</p>
                    </div>
                </div>
                <p v-else class="text-gray-600">No recent activity</p>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

// Mock data - replace with actual data from your backend
const pendingBarbers = [
    {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        applied_date: '2024-05-20'
    }
]

const stats = {
    totalUsers: 150,
    activeBarbers: 12,
    totalAppointments: 450
}

const recentActivity = [
    {
        id: 1,
        description: 'New barber registration: John Doe',
        date: '2024-05-20'
    }
]

const approveBarber = (barberId) => {
    // Implement barber approval logic
    console.log('Approving barber:', barberId)
}
</script>
