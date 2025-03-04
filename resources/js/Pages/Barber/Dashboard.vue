<template>
    <AuthenticatedLayout title="Barber Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Barber Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Loading State -->
                <div v-if="isLoading" class="text-center py-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
                    <p class="mt-2 text-gray-600">Loading dashboard data...</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ error }}
                </div>

                <div v-else>
                    <!-- Stats Section -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-gray-600 text-sm">Today's Appointments</div>
                            <div class="text-3xl font-bold">{{ todayAppointments.length }}</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-gray-600 text-sm">Week's Revenue</div>
                            <div class="text-3xl font-bold">£{{ weeklyRevenue.toFixed(2) }}</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-gray-600 text-sm">Total Clients</div>
                            <div class="text-3xl font-bold">{{ totalClients }}</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-gray-600 text-sm">Average Rating</div>
                            <div class="text-3xl font-bold">{{ averageRating.toFixed(1) }} ⭐</div>
                        </div>
                    </div>

                    <!-- Calendar Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold mb-4">Weekly Schedule</h3>
                        <div class="grid grid-cols-7 gap-4">
                            <div v-for="day in calendarDays"
                                 :key="day.date"
                                 class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50"
                                 :class="{ 'bg-blue-50': day.hasAppointments }"
                                 @click="selectedDate = parseISO(day.date)">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-600">{{ day.dayName }}</div>
                                    <div class="text-lg font-bold">{{ day.dayNumber }}</div>
                                    <div v-if="day.hasAppointments" class="mt-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ day.appointmentCount }} appt{{ day.appointmentCount !== 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today's Appointments -->
                        <div class="mt-8">
                            <h4 class="text-md font-medium mb-4">
                                Appointments for {{ format(selectedDate, 'MMMM d, yyyy') }}
                            </h4>
                            <div class="space-y-4">
                                <div v-if="appointments.length === 0" class="text-gray-500 text-center py-4">
                                    No appointments scheduled for this day
                                </div>
                                <div v-else v-for="appointment in appointments" :key="appointment.id"
                                     class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium">{{ appointment.client_name }}</div>
                                            <div class="text-sm text-gray-600">
                                                {{ format(parseISO(appointment.booking_time), 'h:mm a') }}
                                            </div>
                                            <div class="text-sm text-gray-500">Service: {{ appointment.service }}</div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button @click="completeAppointment(appointment.id)"
                                                    class="px-3 py-1 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition"
                                                    :disabled="appointment.status === 'completed'">
                                                {{ appointment.status === 'completed' ? 'Completed' : 'Complete' }}
                                            </button>
                                            <button @click="sendReminder(appointment.id)"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 transition">
                                                Send Reminder
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Recent Messages</h3>
                        <div class="space-y-4">
                            <div v-if="messages.length === 0" class="text-gray-500 text-center py-4">
                                No new messages
                            </div>
                            <div v-else class="divide-y">
                                <div v-for="message in messages" :key="message.id" class="py-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">
                                                    {{ message.sender_name?.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow">
                                            <div class="flex items-center justify-between">
                                                <div class="font-medium">{{ message.sender_name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ format(parseISO(message.created_at), 'MMM d, h:mm a') }}
                                                </div>
                                            </div>
                                            <div class="text-gray-600 mt-1">{{ message.content }}</div>
                                            <button @click="replyToMessage(message.id)"
                                                    class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                                                Reply
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { format, startOfWeek, addDays, parseISO } from 'date-fns';
import axios from 'axios';
import Echo from 'laravel-echo';

const todayAppointments = ref([]);
const weeklyRevenue = ref(0);
const totalClients = ref(0);
const averageRating = ref(0);
const messages = ref([]);
const isLoading = ref(true);
const error = ref(null);
const echo = ref(null);

// Socket.IO setup
const setupWebSockets = (barberId) => {
    // Listen for new bookings
    echo.value = window.Echo.private(`barber.${barberId}`)
        .listen('booking.created', (e) => {
            console.log('New booking received:', e);
            refreshDashboardData();
        })
        .listen('booking.updated', (e) => {
            console.log('Booking updated:', e);
            refreshDashboardData();
        })
        .listen('booking.cancelled', (e) => {
            console.log('Booking cancelled:', e);
            refreshDashboardData();
        });

    // Listen for new messages
    echo.value.listen('message.sent', (e) => {
        console.log('New message received:', e);
        const newMessage = e.message;
        if (messages.value.length >= 5) {
            messages.value.pop();
        }
        messages.value.unshift({
            id: newMessage.id,
            sender_name: newMessage.sender.name,
            content: newMessage.content,
            created_at: newMessage.created_at,
            is_read: false
        });
    });

    // Listen for rating updates
    echo.value.listen('transformation.rated', (e) => {
        console.log('New rating received:', e);
        averageRating.value = e.newAverageRating;
    });
};

const refreshDashboardData = async () => {
    await Promise.all([
        fetchDashboardData(),
        fetchAppointments(selectedDate.value)
    ]);
};

const fetchDashboardData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/barber/dashboard-data');
        const data = response.data;

        todayAppointments.value = data.todayAppointments;
        weeklyRevenue.value = data.weeklyRevenue;
        totalClients.value = data.totalClients;
        averageRating.value = data.averageRating;
        messages.value = data.recentMessages;

        // Setup WebSocket listeners if not already set up
        if (!echo.value && data.barberId) {
            setupWebSockets(data.barberId);
        }
    } catch (e) {
        error.value = 'Failed to load dashboard data';
        console.error('Error fetching dashboard data:', e);
    } finally {
        isLoading.value = false;
    }
};

const completeAppointment = async (appointmentId) => {
    try {
        await axios.post(`/barber/appointments/${appointmentId}/complete`);
        // Refresh the appointments list
        await fetchDashboardData();
    } catch (e) {
        console.error('Error completing appointment:', e);
    }
};

const sendReminder = async (appointmentId) => {
    try {
        await axios.post(`/barber/appointments/${appointmentId}/send-reminder`);
        // Show success message
    } catch (e) {
        console.error('Error sending reminder:', e);
    }
};

// Calendar functionality
const selectedDate = ref(new Date());
const appointments = ref([]);

const fetchAppointments = async (date) => {
    try {
        const response = await axios.get('/barber/calendar-data', {
            params: {
                date: format(date, 'yyyy-MM-dd')
            }
        });
        appointments.value = response.data.appointments;
    } catch (e) {
        console.error('Error fetching appointments:', e);
    }
};

const calendarDays = computed(() => {
    const weekStart = startOfWeek(selectedDate.value);
    return Array.from({ length: 7 }, (_, index) => {
        const date = addDays(weekStart, index);
        const dateStr = format(date, 'yyyy-MM-dd');
        const dayAppointments = appointments.value.filter(apt =>
            format(parseISO(apt.booking_date), 'yyyy-MM-dd') === dateStr
        );

        return {
            date: dateStr,
            dayName: format(date, 'EEE'),
            dayNumber: format(date, 'd'),
            hasAppointments: dayAppointments.length > 0,
            appointmentCount: dayAppointments.length
        };
    });
});

// Message handling
const replyToMessage = async (messageId) => {
    // We'll implement this in the messaging feature
    console.log('Reply to message:', messageId);
};

// Cleanup WebSocket connections on component unmount
onUnmounted(() => {
    if (echo.value) {
        echo.value.leaveAllChannels();
        echo.value = null;
    }
});

onMounted(async () => {
    await fetchDashboardData();
    await fetchAppointments(selectedDate.value);
});
</script>
