<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">Payment Details</h3>
            <p class="mt-1 text-sm text-gray-600">
                Deposit amount: £{{ depositAmount }}
            </p>
        </div>

        <div class="space-y-4">
            <div class="form-control">
                <label class="block text-sm font-medium text-gray-700">Card holder name</label>
                <input
                    type="text"
                    v-model="cardName"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Name on card"
                />
            </div>

            <div class="form-control">
                <label class="block text-sm font-medium text-gray-700">Card details</label>
                <div id="card-element" class="mt-1 p-3 border rounded-md border-gray-300"></div>
                <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
            </div>

            <button
                @click="handleSubmit"
                :disabled="processing"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
                <svg
                    v-if="processing"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ processing ? 'Processing...' : 'Pay Deposit' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { loadStripe } from '@stripe/stripe-js';

const props = defineProps({
    bookingId: {
        type: [Number, String],
        required: true
    },
    depositAmount: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['success', 'error']);

const stripe = ref(null);
const card = ref(null);
const processing = ref(false);
const cardName = ref('');

onMounted(async () => {
    stripe.value = await loadStripe(process.env.MIX_STRIPE_KEY);
    const elements = stripe.value.elements();

    card.value = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });

    card.value.mount('#card-element');

    card.value.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
});

const handleSubmit = async () => {
    if (processing.value) return;

    processing.value = true;
    const displayError = document.getElementById('card-errors');

    try {
        // Get payment intent
        const response = await fetch(`/bookings/${props.bookingId}/payment-intent`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        const data = await response.json();

        // Confirm payment
        const result = await stripe.value.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: card.value,
                billing_details: {
                    name: cardName.value
                }
            }
        });

        if (result.error) {
            displayError.textContent = result.error.message;
            emit('error', result.error);
        } else {
            emit('success', result.paymentIntent);
        }
    } catch (error) {
        displayError.textContent = 'An error occurred while processing your payment.';
        emit('error', error);
    } finally {
        processing.value = false;
    }
};
</script>
