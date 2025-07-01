<template>
    <Head title="My Bookings" />
    <SidebarLayout>
        <div class="p-3 lg:p-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="bg-white shadow border border-gray-300 rounded px-4 lg:px-6 py-3 lg:py-4 mb-4 lg:mb-6">
                <h1 class="text-lg lg:text-xl font-bold flex items-center text-gray-600">
                    <div class="h-6 w-6 lg:h-8 lg:w-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    My Bookings
                </h1>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="mb-4 px-3 lg:px-4 py-3 rounded bg-green-100 text-green-800 font-semibold text-center transition-opacity duration-500 text-sm lg:text-base">
                {{ successMessage }}
            </div>

            <!-- Offline Message -->
            <div v-if="!isOnline" class="mb-4 px-3 lg:px-4 py-3 rounded bg-yellow-100 text-yellow-800 font-semibold text-center text-sm lg:text-base">
                You're currently offline. Viewing cached data.
            </div>

            <!-- View Mode Toggle -->
            <div class="mb-4 flex gap-2">
                <button
                    class="px-3 lg:px-4 py-2 rounded border text-sm lg:text-base"
                    :class="viewMode === 'upcoming' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300'"
                    @click="viewMode = 'upcoming'"
                >
                    Upcoming
                </button>
                <button
                    class="px-3 lg:px-4 py-2 rounded border text-sm lg:text-base"
                    :class="viewMode === 'past' ? 'bg-gray-600 text-white border-gray-600' : 'bg-white text-gray-700 border-gray-300'"
                    @click="viewMode = 'past'"
                >
                    Past & Cancelled
                </button>
            </div>

            <!-- Mobile Card View (Hidden on larger screens) -->
            <div class="lg:hidden space-y-3">
                <div v-for="booking in (viewMode === 'upcoming' ? filteredBookings : pastOrCancelledBookings)" :key="booking.id" class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm">
                    <!-- Booking Header -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <img v-if="booking.barber?.user?.profile_photo" :src="`/storage/${booking.barber.user.profile_photo}`" alt="Barber" class="h-10 w-10 rounded-full mr-3" />
                            <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-3 text-sm font-bold">
                                {{ booking.barber?.user?.name ? booking.barber.user.name.charAt(0) : 'N/A' }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 text-sm">
                                    {{ booking.barber?.user?.name || 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ formatDate(booking.booking_date) }}
                                </div>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs leading-5 font-semibold rounded-full"
                              :class="statusClass(booking.status)">
                            {{ booking.status || 'N/A' }}
                        </span>
                    </div>

                    <!-- Booking Details -->
                    <div class="grid grid-cols-2 gap-3 text-xs text-gray-600 mb-3">
                        <div>
                            <span class="font-medium">Time:</span>
                            <div>{{ formatTime(booking.booking_time) }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Price:</span>
                            <div>£{{ formatPrice(booking.service_price) }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Deposit:</span>
                            <div>£{{ formatPrice(booking.amount_paid) }}</div>
                        </div>
                        <div>
                            <span class="font-medium">Balance:</span>
                            <div>£{{ formatPrice(booking.service_price - booking.amount_paid) }}</div>
                        </div>
                    </div>

                    <!-- Mobile Actions -->
                    <div v-if="booking.status !== 'cancelled' && !isPastBooking(booking)" class="flex flex-wrap gap-2">
                        <button
                            class="text-blue-600 hover:underline text-xs px-2 py-1 border border-blue-300 rounded"
                            @click="openPaymentModal(booking)"
                        >Pay</button>
                        <button
                            class="text-red-600 hover:underline text-xs px-2 py-1 border border-red-300 rounded"
                            @click="handleCancel(booking)"
                        >Cancel</button>
                        <button
                            class="text-green-600 hover:underline text-xs px-2 py-1 border border-green-300 rounded"
                            @click="openRescheduleModal(booking)"
                        >Reschedule</button>
                    </div>
                </div>

                <!-- No bookings message for mobile -->
                <div v-if="!(viewMode === 'upcoming' ? filteredBookings.length : pastOrCancelledBookings.length)" class="bg-white border border-gray-300 rounded-lg p-6 text-center text-gray-500 text-sm">
                    No bookings found.
                </div>
            </div>

            <!-- Desktop Table View (Hidden on mobile) -->
            <div class="hidden lg:block overflow-x-auto bg-white shadow border border-gray-300 p-4 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barber</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deposit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="booking in (viewMode === 'upcoming' ? filteredBookings : pastOrCancelledBookings)" :key="booking.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ formatDate(booking.booking_date) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ formatTime(booking.booking_time) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img v-if="booking.barber?.user?.profile_photo" :src="`/storage/${booking.barber.user.profile_photo}`" alt="Barber" class="h-10 w-10 rounded-full mr-2" />
                                    <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">N/A</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      :class="statusClass(booking.status)">
                                    {{ booking.status || 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                £{{ formatPrice(booking.service_price) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                £{{ formatPrice(booking.amount_paid) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                £{{ formatPrice(booking.service_price - booking.amount_paid) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button
                                    v-if="booking.status !== 'cancelled' && !isPastBooking(booking)"
                                    class="text-blue-600 hover:underline mr-2"
                                    @click="openPaymentModal(booking)"
                                >Pay</button>
                                <button
                                    v-if="booking.status !== 'cancelled' && !isPastBooking(booking)"
                                    class="text-red-600 hover:underline mr-2"
                                    @click="handleCancel(booking)"
                                >Cancel</button>
                                <button
                                    v-if="booking.status !== 'cancelled' && !isPastBooking(booking)"
                                    class="text-green-600 hover:underline"
                                    @click="openRescheduleModal(booking)"
                                >Reschedule</button>
                            </td>
                        </tr>
                        <tr v-if="!(viewMode === 'upcoming' ? filteredBookings.length : pastOrCancelledBookings.length)">
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No bookings found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Payment Modal -->
            <PaymentModal
                :show="showPaymentModal"
                :booking="selectedBooking"
                @close="showPaymentModal = false"
                @payment-success="handlePaymentSuccess"
            />

            <!-- Reschedule Modal -->
            <div v-if="showRescheduleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                <div class="bg-white rounded-lg shadow-lg p-4 lg:p-6 w-full max-w-sm lg:max-w-md mx-4 relative">
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-xl lg:text-2xl" @click="closeRescheduleModal">&times;</button>
                    <h2 class="text-lg font-bold mb-4">Reschedule Booking</h2>

                    <!-- Original Booking Details -->
                    <div class="mb-4 p-3 bg-gray-50 rounded">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Current Booking</h3>
                        <p class="text-sm text-gray-600">Date: {{ formatDate(rescheduleBooking?.booking_date) }}</p>
                        <p class="text-sm text-gray-600">Time: {{ formatTime(rescheduleBooking?.booking_time) }}</p>
                    </div>

                    <!-- New Date Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select New Date</label>
                        <input type="date" v-model="rescheduleDate" :min="minDate" class="w-full border rounded px-3 py-2 text-sm lg:text-base" @change="fetchAvailableSlots" />
                    </div>

                    <!-- New Time Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select New Time</label>
                        <div v-if="loadingSlots" class="text-gray-500 text-sm">Loading available slots...</div>
                        <div v-else-if="availableSlots.length" class="grid grid-cols-2 lg:grid-cols-3 gap-2">
                            <button v-for="slot in availableSlots" :key="slot" @click="rescheduleTime = slot" :class="['px-2 py-1 rounded text-xs lg:text-sm', rescheduleTime === slot ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-green-100']">
                                {{ slot }}
                            </button>
                        </div>
                        <div v-else class="text-gray-500 text-sm">No available slots for this date.</div>
                    </div>

                    <!-- Barber Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Barber</label>
                        <select v-model="selectedBarberId" class="w-full border rounded px-3 py-2 text-sm lg:text-base" :disabled="loadingBarbers">
                            <option v-for="barber in availableBarbers" :key="barber.id" :value="barber.id">
                                {{ barber.user?.name || ('Barber #' + barber.id) }}
                            </option>
                        </select>
                        <div v-if="loadingBarbers" class="text-gray-500 text-sm mt-1">Loading available barbers...</div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex flex-col lg:flex-row justify-end space-y-2 lg:space-y-0 lg:space-x-2">
                        <button class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm lg:text-base" @click="closeRescheduleModal">Cancel</button>
                        <button
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm lg:text-base"
                            :disabled="!rescheduleDate || !rescheduleTime || submittingReschedule"
                            @click="confirmReschedule"
                        >
                            {{ submittingReschedule ? 'Rescheduling...' : 'Reschedule' }}
                        </button>
                    </div>
                    <div v-if="rescheduleError" class="mt-2 text-red-600 text-sm text-center">{{ rescheduleError }}</div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import PaymentModal from '@/Components/PaymentModal.vue'
import { ref, watch, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    bookings: {
        type: Array,
        default: () => []
    }
})

const showPaymentModal = ref(false)
const selectedBooking = ref(null)
const successMessage = ref("")
const showRescheduleModal = ref(false)
const rescheduleDate = ref("")
const rescheduleTime = ref("")
const availableSlots = ref([])
const loadingSlots = ref(false)
const submittingReschedule = ref(false)
const rescheduleError = ref("")
const availableBarbers = ref([])
const loadingBarbers = ref(false)
const selectedBarberId = ref(null)
const viewMode = ref('upcoming') // 'upcoming' or 'past'
let rescheduleBooking = null

// Add new refs for offline state
const isOnline = ref(navigator.onLine)

const filteredBookings = computed(() => {
    const filtered = props.bookings.filter(
    booking => booking.status !== 'cancelled' && !isPastBooking(booking)
  )
    console.log('Filtered Bookings:', filtered)
    return filtered
})

const pastOrCancelledBookings = computed(() => {
    return props.bookings.filter(
    booking => booking.status === 'cancelled' || isPastBooking(booking)
  )
})

function openPaymentModal(booking) {
    selectedBooking.value = booking
    showPaymentModal.value = true
}

function handlePaymentSuccess() {
    showPaymentModal.value = false
    // Optionally, refresh bookings or show a toast
}

function formatDate(date) {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString();
}
function formatTime(time) {
    if (!time) return '';
    // If time is in HH:mm:ss or HH:mm format
    if (/^\d{2}:\d{2}(:\d{2})?$/.test(time)) {
        const [h, m] = time.split(':');
        const hour = parseInt(h, 10);
        const minute = m;
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 === 0 ? 12 : hour % 12;
        return `${hour12}:${minute} ${ampm}`;
    }
    // If time is a full ISO string, extract time part in local time
    if (time.length > 5) {
        const d = new Date(time);
        const hour = d.getHours();
        const minute = d.getMinutes().toString().padStart(2, '0');
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 === 0 ? 12 : hour % 12;
        return `${hour12}:${minute} ${ampm}`;
    }
    return time;
}
function formatPrice(value) {
    const number = parseFloat(value) || 0;
    return number.toFixed(2);
}
function statusClass(status) {
    if (status === 'confirmed') return 'bg-green-100 text-green-800';
    if (status === 'pending') return 'bg-yellow-100 text-yellow-800';
    if (status === 'cancelled') return 'bg-red-100 text-red-800';
    return 'bg-gray-100 text-gray-800';
}
function handleCancel(booking) {
    const csrfToken = document.querySelector('meta[name=\'csrf-token\']')?.content;
    console.log('CSRF Token:', csrfToken);
    if (!confirm('Are you sure you want to cancel this booking?')) return;
    fetch(`/booking/${booking.id}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to cancel booking');
        successMessage.value = 'Booking cancelled successfully.';
        setTimeout(() => {
            successMessage.value = '';
            window.location.reload();
        }, 2500);
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
function openRescheduleModal(booking) {
    showRescheduleModal.value = true
    rescheduleBooking = booking
    rescheduleDate.value = ""
    rescheduleTime.value = ""
    availableSlots.value = []
    rescheduleError.value = ""
    availableBarbers.value = []
    selectedBarberId.value = booking.barber_id
}
function closeRescheduleModal() {
    showRescheduleModal.value = false
}
const minDate = new Date().toISOString().split('T')[0]

function fetchAvailableBarbers() {
    if (!rescheduleDate.value || !rescheduleTime.value) return
    loadingBarbers.value = true
    availableBarbers.value = []
    fetch(`/api/available-barbers?date=${rescheduleDate.value}&time=${rescheduleTime.value}`)
        .then(async response => {
            const data = await response.json()
            if (!response.ok) throw new Error('Failed to fetch available barbers')
            availableBarbers.value = data.barbers || []
            // If the current barber is not available, reset selection
            if (!availableBarbers.value.some(b => b.id === selectedBarberId.value)) {
                selectedBarberId.value = availableBarbers.value.length ? availableBarbers.value[0].id : null
            }
        })
        .catch(() => {
            availableBarbers.value = []
        })
        .finally(() => {
            loadingBarbers.value = false
        })
}

function fetchAvailableSlots() {
    if (!rescheduleBooking || !rescheduleDate.value) return
    loadingSlots.value = true
    availableSlots.value = []
    rescheduleError.value = ""

    // Check if selected date is in the past
    const selectedDate = new Date(rescheduleDate.value)
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    if (selectedDate < today) {
        rescheduleError.value = "Cannot select a date in the past"
        loadingSlots.value = false
        return
    }

    fetch(`/api/available-slots?barber_id=${rescheduleBooking.barber_id}&date=${rescheduleDate.value}&booking_id=${rescheduleBooking.id}`)
        .then(async response => {
            const data = await response.json()
            if (!response.ok) {
                throw new Error(data.error || 'Failed to fetch available slots')
            }
            availableSlots.value = data.available_slots || []
            if (availableSlots.value.length === 0) {
                rescheduleError.value = "No available slots for this date"
            }
        })
        .catch(error => {
            rescheduleError.value = error.message || "Failed to load available slots. Please try again."
            console.error('Error fetching slots:', error)
            availableSlots.value = []
        })
        .finally(() => {
            loadingSlots.value = false
        })
}

// Watch for date and time changes to fetch available barbers
watch([rescheduleDate, rescheduleTime], ([date, time]) => {
    if (date && time) fetchAvailableBarbers()
})

function confirmReschedule() {
    if (!rescheduleBooking || !rescheduleDate.value || !rescheduleTime.value || !selectedBarberId.value) return

    const newDateTime = new Date(`${rescheduleDate.value}T${rescheduleTime.value}`)
    const oldDateTime = new Date(`${rescheduleBooking.booking_date}T${rescheduleBooking.booking_time}`)

    if (newDateTime < new Date()) {
        rescheduleError.value = "Cannot reschedule to a time in the past"
        return
    }

    if (confirm(`Are you sure you want to reschedule your booking from ${formatDate(rescheduleBooking.booking_date)} at ${formatTime(rescheduleBooking.booking_time)} to ${formatDate(rescheduleDate.value)} at ${rescheduleTime.value}?`)) {
        submitReschedule()
    }
}

function submitReschedule() {
    if (!rescheduleBooking || !rescheduleDate.value || !rescheduleTime.value || !selectedBarberId.value) return
    submittingReschedule.value = true
    rescheduleError.value = ""
    const csrfToken = document.querySelector('meta[name=\'csrf-token\']')?.content

    // Convert date to YYYY-MM-DD if needed
    let dateToSend = rescheduleDate.value
    if (dateToSend.includes('/')) {
        // Convert DD/MM/YYYY to YYYY-MM-DD
        const [day, month, year] = dateToSend.split('/')
        dateToSend = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    }

    // Ensure time is only HH:MM
    let timeToSend = rescheduleTime.value
    if (timeToSend.length > 5) {
        timeToSend = timeToSend.slice(-8, -6) + ':' + timeToSend.slice(-5, -3)
        if (timeToSend.length !== 5) {
            timeToSend = rescheduleTime.value.slice(-5)
        }
    }

    fetch(`/booking/${rescheduleBooking.id}/reschedule`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            booking_date: dateToSend,
            booking_time: timeToSend,
            barber_id: selectedBarberId.value
        })
    })
    .then(async response => {
        if (response.status === 409) {
            const data = await response.json()
            rescheduleError.value = data.error || 'This slot is already booked.'
            submittingReschedule.value = false
            return
        }
        if (!response.ok) throw new Error('Failed to reschedule booking')
        successMessage.value = 'Booking rescheduled successfully.'
        showRescheduleModal.value = false
        setTimeout(() => {
            successMessage.value = ''
            window.location.reload()
        }, 2500)
    })
    .catch(error => {
        rescheduleError.value = error.message
        submittingReschedule.value = false
    })
}

function isPastBooking(booking) {
    let dateStr = booking.booking_date.trim();
    let timeStr = booking.booking_time.trim();

    // If dateStr is ISO (e.g., 2023-05-21T00:00:00.000000Z), extract YYYY-MM-DD
    if (dateStr.includes('T')) {
        dateStr = dateStr.split('T')[0];
    }
    // If timeStr is ISO (e.g., 2025-05-23T11:00:00.000000Z), extract HH:MM
    if (timeStr.includes('T')) {
        timeStr = timeStr.split('T')[1].slice(0, 5);
    } else if (timeStr.length > 5) {
        timeStr = timeStr.slice(0, 5);
    }

    // Convert DD/MM/YYYY to YYYY-MM-DD if needed
    if (dateStr.includes('/')) {
        const [day, month, year] = dateStr.split('/');
        dateStr = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    }

    const [hours, minutes] = timeStr.split(':');
    // Use Date.UTC to avoid timezone issues
    const bookingDateTimeUTC = Date.UTC(
        Number(dateStr.slice(0, 4)),
        Number(dateStr.slice(5, 7)) - 1,
        Number(dateStr.slice(8, 10)),
        Number(hours),
        Number(minutes)
    );
    const nowUTC = Date.now();
    console.log('Booking:', booking.booking_date, booking.booking_time, 'Parsed UTC:', new Date(bookingDateTimeUTC), 'Now:', new Date(nowUTC));
    if (isNaN(bookingDateTimeUTC)) {
        console.warn('Invalid booking date/time:', booking.booking_date, booking.booking_time);
        return false;
    }
    return bookingDateTimeUTC < nowUTC;
}

// Debug: Log the profile photo URL for each booking
console.log('Bookings:', props.bookings.map(booking => booking.barber?.user?.profile_photo));

const showSuccess = ref(false)
onMounted(() => {
  const params = new URLSearchParams(window.location.search)
  if (params.get('success') === '1') {
    showSuccess.value = true
    window.history.replaceState({}, document.title, window.location.pathname)
  }
})
</script>
