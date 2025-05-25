<template>
    <Head title="Customer Dashboard" />

    <SidebarLayout>
        <div class="min-h-screen bg-gray-50">
            <!-- Four summary cards: full width, single line on large screens -->
            <div class="w-full px-4 pt-8 pb-4">
                <h1 class="text-xl font-bold mb-6">Customer Dashboard</h1>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white shadow-lg rounded p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Doughnut 1</h3>
                        <DoughnutChart :data="doughnutData1" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                    </div>
                    <div class="bg-white shadow-lg rounded p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Doughnut 2</h3>
                        <DoughnutChart :data="doughnutData2" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                    </div>
                    <div class="bg-white shadow-lg rounded p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Doughnut 3</h3>
                        <DoughnutChart :data="doughnutData3" :options="doughnutOptions" style="max-width:120px; height:128px;" />
                    </div>
                    <div class="bg-white shadow-lg rounded p-6 w-full flex flex-col items-center h-64 justify-center">
                        <h3 class="text-lg font-semibold mb-2">Bar Chart</h3>
                        <BarChart :data="barData" :options="barOptions" style="max-width:180px; height:128px;" />
                    </div>
                </div>
            </div>

            <div class="max-w-3xl mx-auto py-12">
                <div class="space-y-4 bg-white shadow-lg rounded p-6">
                    <h2 class="text-lg font-semibold">Your Appointments</h2>
                    <div v-if="bookings.length" class="grid gap-4">
                        <div v-for="booking in bookings" :key="booking.id" class="bg-gray-100 p-4 rounded-xl">
                            <p class="font-semibold">Service: {{ booking.service_name || booking.service?.name || 'N/A' }}</p>
                            <p class="text-sm text-gray-500">
                                Barber: {{ booking.barber?.user?.name || 'N/A' }}<br />
                                {{ booking.booking_time ? new Date(booking.booking_time).toLocaleString() : 'N/A' }}
                            </p>
                            <span class="text-xs uppercase px-2 py-1 rounded bg-gray-200">
                                {{ booking.status || 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <p v-else>No appointments yet.</p>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { defineProps } from 'vue'
import { Doughnut, Bar } from 'vue-chartjs'
import { Chart, ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

Chart.register(ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const DoughnutChart = Doughnut
const BarChart = Bar

const doughnutData1 = {
  labels: ['A', 'B', 'C'],
  datasets: [{
    data: [30, 50, 20],
    backgroundColor: ['#fbbf24', '#3b82f6', '#10b981'],
    borderWidth: 0
  }]
}
const doughnutData2 = {
  labels: ['X', 'Y', 'Z'],
  datasets: [{
    data: [10, 60, 30],
    backgroundColor: ['#f87171', '#6366f1', '#fbbf24'],
    borderWidth: 0
  }]
}
const doughnutData3 = {
  labels: ['P', 'Q', 'R'],
  datasets: [{
    data: [40, 40, 20],
    backgroundColor: ['#10b981', '#f59e42', '#3b82f6'],
    borderWidth: 0
  }]
}
const doughnutOptions = {
  cutout: '70%',
  plugins: { legend: { display: false } },
  responsive: true,
  maintainAspectRatio: false
}

const barData = {
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
  datasets: [{
    label: 'Visits',
    data: [12, 19, 3, 5, 2],
    backgroundColor: '#3b82f6',
    borderRadius: 6,
    barPercentage: 0.7,
    categoryPercentage: 0.7
  }]
}
const barOptions = {
  plugins: { legend: { display: false } },
  responsive: true,
  maintainAspectRatio: false,
  layout: { padding: 10 },
  scales: {
    x: {
      grid: { display: false },
      ticks: { font: { size: 14 } }
    },
    y: {
      grid: { display: false },
      beginAtZero: true,
      ticks: { font: { size: 14 } }
    }
  }
}

defineProps({
    bookings: {
        type: Array,
        default: () => []
    }
})
</script>
