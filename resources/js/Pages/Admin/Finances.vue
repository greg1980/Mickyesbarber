<template>
    <Head title="Finances - Admin Dashboard" />
    <SidebarLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
            <!-- Modern Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-8 lg:py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                <CurrencyDollarIcon class="w-8 h-8 text-green-600" />
                            </div>
                            <div>
                                <h1 class="text-3xl lg:text-4xl font-bold mb-2">Financial Dashboard</h1>
                                <p class="text-green-100 text-lg">Track revenue, payments, and financial performance</p>
                            </div>
                        </div>
                        <div class="hidden md:flex space-x-3">
                            <button @click="exportFullReport"
                                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Full Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-8 lg:py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Key Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Revenue -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                                <p class="text-3xl font-bold text-green-600">${{ formatCurrency(stats.totalRevenue) }}</p>
                            </div>
                            <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                                <CurrencyDollarIcon class="h-6 w-6 text-green-600" />
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Revenue -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">This Month</p>
                                <p class="text-3xl font-bold text-blue-600">${{ formatCurrency(stats.monthlyRevenue) }}</p>
                            </div>
                            <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <CalendarIcon class="h-6 w-6 text-blue-600" />
                            </div>
                        </div>
                    </div>

                    <!-- Daily Revenue -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Today</p>
                                <p class="text-3xl font-bold text-purple-600">${{ formatCurrency(stats.dailyRevenue) }}</p>
                            </div>
                            <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <ClockIcon class="h-6 w-6 text-purple-600" />
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Growth -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Growth Rate</p>
                                <p class="text-3xl font-bold" :class="stats.revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ stats.revenueGrowth >= 0 ? '+' : '' }}{{ stats.revenueGrowth }}%
                                </p>
                            </div>
                            <div class="h-12 w-12 rounded-full flex items-center justify-center"
                                 :class="stats.revenueGrowth >= 0 ? 'bg-green-100' : 'bg-red-100'">
                                <ArrowTrendingUpIcon v-if="stats.revenueGrowth >= 0" class="h-6 w-6 text-green-600" />
                                <ArrowTrendingDownIcon v-else class="h-6 w-6 text-red-600" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Monthly Revenue Chart -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-600 flex items-center gap-2">
                                <div class="h-6 w-6 bg-blue-100 rounded-full flex items-center justify-center">
                                    <ChartBarIcon class="w-4 h-4 text-blue-600" />
                                </div>
                                Monthly Revenue Trend
                            </h3>
                            <button @click="refreshMonthlyChart"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Refresh
                            </button>
                        </div>
                        <div class="h-80">
                            <canvas ref="monthlyChart"></canvas>
                        </div>
                    </div>

                    <!-- Daily Revenue Chart -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-600 flex items-center gap-2">
                                <div class="h-6 w-6 bg-purple-100 rounded-full flex items-center justify-center">
                                    <PresentationChartLineIcon class="w-4 h-4 text-purple-600" />
                                </div>
                                Daily Revenue (Last 30 Days)
                            </h3>
                            <button @click="refreshDailyChart"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Refresh
                            </button>
                        </div>
                        <div class="h-80">
                            <canvas ref="dailyChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Additional Metrics -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Average Transaction Value -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-600 mb-4 flex items-center gap-2">
                            <div class="h-6 w-6 bg-orange-100 rounded-full flex items-center justify-center">
                                <CalculatorIcon class="w-4 h-4 text-orange-600" />
                            </div>
                            Quick Stats
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Avg. Transaction:</span>
                                <span class="font-semibold">${{ formatCurrency(averageTransaction) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Bookings:</span>
                                <span class="font-semibold">{{ totalBookings }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Paid Bookings:</span>
                                <span class="font-semibold">{{ paidBookings }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status Overview -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-600 mb-4 flex items-center gap-2">
                            <div class="h-6 w-6 bg-yellow-100 rounded-full flex items-center justify-center">
                                <CreditCardIcon class="w-4 h-4 text-yellow-600" />
                            </div>
                            Payment Status
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Paid</span>
                                </div>
                                <span class="font-semibold">{{ paidBookings }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Pending</span>
                                </div>
                                <span class="font-semibold">{{ pendingBookings }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                    <span class="text-gray-600">Failed</span>
                                </div>
                                <span class="font-semibold">{{ failedBookings }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Summary -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-600 mb-4 flex items-center gap-2">
                            <div class="h-6 w-6 bg-green-100 rounded-full flex items-center justify-center">
                                <BanknotesIcon class="w-4 h-4 text-green-600" />
                            </div>
                            Revenue Summary
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">This Week:</span>
                                <span class="font-semibold text-green-600">${{ formatCurrency(weeklyRevenue) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Last Month:</span>
                                <span class="font-semibold">${{ formatCurrency(lastMonthRevenue) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">YTD Total:</span>
                                <span class="font-semibold text-blue-600">${{ formatCurrency(yearToDateRevenue) }}</span>
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
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { ref, onMounted, nextTick } from 'vue'
import {
    CurrencyDollarIcon,
    CalendarIcon,
    ClockIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    ChartBarIcon,
    PresentationChartLineIcon,
    CalculatorIcon,
    CreditCardIcon,
    BanknotesIcon
} from '@heroicons/vue/24/outline'
import Chart from 'chart.js/auto'

const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    charts: {
        type: Object,
        required: true
    }
})

// Chart references
const monthlyChart = ref(null)
const dailyChart = ref(null)
let monthlyChartInstance = null
let dailyChartInstance = null

// Additional computed stats (mock data for now - we'll implement these later)
const averageTransaction = ref(0)
const totalBookings = ref(0)
const paidBookings = ref(0)
const pendingBookings = ref(0)
const failedBookings = ref(0)
const weeklyRevenue = ref(0)
const lastMonthRevenue = ref(0)
const yearToDateRevenue = ref(0)

// Format currency helper
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount || 0)
}

// PDF Export Functions
const exportFullReport = async () => {
    try {
        const response = await fetch('/admin/finances/export/full-report', {
            method: 'GET',
            headers: {
                'Accept': 'application/pdf',
            },
        })

        if (response.ok) {
            const blob = await response.blob()
            const url = window.URL.createObjectURL(blob)
            const a = document.createElement('a')
            a.href = url
            a.download = `financial-dashboard-${new Date().toISOString().split('T')[0]}.pdf`
            document.body.appendChild(a)
            a.click()
            window.URL.revokeObjectURL(url)
            document.body.removeChild(a)
        }
    } catch (error) {
        console.error('Error exporting full report:', error)
        alert('Error generating PDF. Please try again.')
    }
}

// Initialize charts
const initializeCharts = async () => {
    await nextTick()

    // Monthly Revenue Chart
    if (monthlyChart.value) {
        const ctx = monthlyChart.value.getContext('2d')
        monthlyChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: props.charts.monthlyRevenue.labels,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: props.charts.monthlyRevenue.data,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString()
                            }
                        }
                    }
                }
            }
        })
    }

    // Daily Revenue Chart
    if (dailyChart.value) {
        const ctx = dailyChart.value.getContext('2d')
        dailyChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: props.charts.dailyRevenue.labels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: props.charts.dailyRevenue.data,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString()
                            }
                        }
                    }
                }
            }
        })
    }
}

// Refresh chart data
const refreshMonthlyChart = async () => {
    try {
        const response = await fetch('/admin/finances/monthly-revenue')
        const data = await response.json()

        if (monthlyChartInstance) {
            monthlyChartInstance.data.labels = data.labels
            monthlyChartInstance.data.datasets[0].data = data.data
            monthlyChartInstance.update()
        }
    } catch (error) {
        console.error('Error refreshing monthly chart:', error)
    }
}

const refreshDailyChart = async () => {
    try {
        const response = await fetch('/admin/finances/daily-revenue')
        const data = await response.json()

        if (dailyChartInstance) {
            dailyChartInstance.data.labels = data.labels
            dailyChartInstance.data.datasets[0].data = data.data
            dailyChartInstance.update()
        }
    } catch (error) {
        console.error('Error refreshing daily chart:', error)
    }
}

// Calculate additional stats
const calculateAdditionalStats = () => {
    // These are mock calculations - we'll implement proper API endpoints later
    const totalRev = props.stats.totalRevenue || 0
    totalBookings.value = Math.floor(totalRev / 45) // Assuming avg $45 per booking
    paidBookings.value = Math.floor(totalBookings.value * 0.85)
    pendingBookings.value = Math.floor(totalBookings.value * 0.1)
    failedBookings.value = totalBookings.value - paidBookings.value - pendingBookings.value
    averageTransaction.value = totalBookings.value > 0 ? totalRev / totalBookings.value : 0
    weeklyRevenue.value = props.stats.dailyRevenue * 7
    lastMonthRevenue.value = props.stats.monthlyRevenue * 0.9 // Mock last month as 90% of current
    yearToDateRevenue.value = totalRev
}

onMounted(() => {
    calculateAdditionalStats()
    initializeCharts()
})
</script>
