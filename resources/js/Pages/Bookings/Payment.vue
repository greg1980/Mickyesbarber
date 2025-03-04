    <template>
    <Head title="Payment" />

    <UserLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Complete Payment
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <Alert v-if="error" class="mb-6" variant="danger">
                            {{ error }}
                        </Alert>

                        <!-- Booking Summary -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Summary</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Date</p>
                                        <p class="font-medium">{{ formatDate(booking.booking_date) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Time</p>
                                        <p class="font-medium">{{ formatTime(booking.booking_time) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Barber</p>
                                        <p class="font-medium">{{ booking.barber.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Deposit Amount</p>
                                        <p class="font-medium">{{ formatCurrency(booking.deposit_amount) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form @submit.prevent="handleSubmit">
                            <div class="mb-6">
                                <InputLabel value="Card Details" />
                                <div id="card-element" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                                <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
                            </div>

                            <div class="flex justify-end">
                                <Button
                                    type="submit"
                                    :disabled="processing"
                                    :processing="processing"
                                >
                                    Pay {{ formatCurrency(booking.deposit_amount) }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import InputLabel from '@/components/InputLabel.vue';
import Button from '@/components/Button.vue';
import Alert from '@/components/Alert.vue';
import { format, parse } from 'date-fns';
import { loadStripe } from '@stripe/stripe-js';

const page = usePage();
const stripeKey = page.props.stripe_key;

// Debug logging
console.log('Inertia shared props:', page.props);
console.log('Stripe key from props:', stripeKey);

const props = defineProps({
    booking: {
        type: Object,
        required: true
    },
    clientSecret: {
        type: String,
        required: true
    }
});

const stripe = ref(null);
const card = ref(null);
const processing = ref(false);
const error = ref(null);

const formatDate = (date) => {
    try {
        return format(new Date(date), 'MMMM d, yyyy');
    } catch (e) {
        console.error('Error formatting date:', e);
        return 'Invalid date';
    }
};

const formatTime = (time) => {
    try {
        // Handle different time formats
        if (!time) return 'No time specified';

        // If time is in HH:mm:ss format
        if (time.includes(':')) {
            const [hours, minutes] = time.split(':');
            const date = new Date();
            date.setHours(parseInt(hours, 10));
            date.setMinutes(parseInt(minutes, 10));
            return format(date, 'h:mm a');
        }

        return 'Invalid time format';
    } catch (e) {
        console.error('Error formatting time:', e);
        return 'Invalid time';
    }
};

const formatCurrency = (amount) => {
    try {
        // Convert to number if it's a string
        const numericAmount = typeof amount === 'string' ? parseFloat(amount) : amount;

        // Check if it's a valid number
        if (typeof numericAmount !== 'number' || isNaN(numericAmount)) {
            console.error('Invalid amount:', amount);
            return '£0.00';
        }

        return `£${numericAmount.toFixed(2)}`;
    } catch (e) {
        console.error('Error formatting currency:', e);
        return '£0.00';
    }
};

onMounted(async () => {
    try {
        console.log('Mounting Payment component...');
        console.log('Stripe key:', stripeKey);

        if (!stripeKey) {
            error.value = 'Stripe key is missing. Please contact support.';
            console.error('Stripe key is missing from Inertia props');
            return;
        }

        console.log('Initializing Stripe with key:', stripeKey);
        stripe.value = await loadStripe(stripeKey);

        if (!stripe.value) {
            error.value = 'Failed to initialize Stripe. Please try again.';
            console.error('Stripe initialization failed');
            return;
        }

        console.log('Stripe initialized successfully');
        const elements = stripe.value.elements();

        // Create card element
        card.value = elements.create('card');
        card.value.mount('#card-element');
        console.log('Card element mounted');

        // Handle validation errors
        card.value.addEventListener('change', (event) => {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
                console.error('Card validation error:', event.error);
            } else {
                displayError.textContent = '';
            }
        });
    } catch (e) {
        error.value = 'Failed to initialize payment form. Please try again.';
        console.error('Stripe initialization error:', e);
    }
});

const handleSubmit = async () => {
    if (processing.value) return;

    processing.value = true;
    error.value = null;

    try {
        const { error: stripeError, paymentIntent } = await stripe.value.confirmCardPayment(
            props.clientSecret,
            {
                payment_method: {
                    card: card.value,
                }
            }
        );

        if (stripeError) {
            error.value = stripeError.message;
            processing.value = false;
            return;
        }

        // Payment successful, update booking
        await useForm().post(route('booking.process-payment', props.booking.id), {
            payment_intent: paymentIntent.id
        });

        // Redirect to success page
        window.location = route('bookings.index');
    } catch (e) {
        console.error('Payment error:', e);
        error.value = 'An error occurred while processing your payment. Please try again.';
        processing.value = false;
    }
};
</script>
