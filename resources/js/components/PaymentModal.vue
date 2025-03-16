<template>
    <Modal :show="show" @close="$emit('close')" class="payment-modal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Complete Payment
            </h2>

            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-gray-600">Booking Date:</div>
                    <div>{{ formatDate(booking?.booking_date) }}</div>

                    <div class="text-gray-600">Time:</div>
                    <div>{{ formatTime(booking?.booking_time) }}</div>

                    <div class="text-gray-600">Barber:</div>
                    <div>{{ booking?.barber?.name }}</div>

                    <div class="text-gray-600">Total Price:</div>
                    <div>£{{ formatPrice(booking?.service_price) }}</div>

                    <div class="text-gray-600">Deposit Paid:</div>
                    <div>£{{ formatPrice(booking?.deposit_amount) }}</div>

                    <div class="text-gray-600 font-semibold">Balance Due:</div>
                    <div class="font-semibold">£{{ formatPrice(booking?.balance_amount) }}</div>
                </div>
            </div>

            <div class="mb-6">
                <div id="card-element" class="mt-2"></div>
                <div id="card-errors" class="text-red-500 text-sm mt-2">{{ error }}</div>
            </div>

            <div class="flex justify-end space-x-3">
                <Button
                    variant="secondary"
                    @click="$emit('close')"
                    :disabled="processing"
                >
                    Cancel
                </Button>
                <Button
                    variant="primary"
                    @click="handleSubmit"
                    :processing="processing"
                >
                    Pay {{ parseFloat(booking?.deposit_amount) > 0 ? 'Balance' : 'Deposit' }} £{{ formatPrice(parseFloat(booking?.deposit_amount) > 0 ? booking?.balance_amount : (booking?.service_price * 0.25)) }}
                </Button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';
import Modal from '@/components/Modal.vue';
import Button from '@/components/Button.vue';
import { useToast } from 'vue-toastification';
import { router, usePage } from '@inertiajs/vue3';
import { loadStripe } from '@stripe/stripe-js';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    booking: Object
});

const emit = defineEmits(['close']);
const toast = useToast();
const stripe = ref(null);
const card = ref(null);
const processing = ref(false);
const error = ref('');

onMounted(async () => {
    try {
        const stripeKey = usePage().props.stripe_key;
        if (!stripeKey) {
            error.value = 'Stripe key is missing';
            return;
        }

        stripe.value = await loadStripe(stripeKey);
        const elements = stripe.value.elements();

        card.value = elements.create('card');
        card.value.mount('#card-element');

        card.value.on('change', (event) => {
            if (event.error) {
                error.value = event.error.message;
            } else {
                error.value = '';
            }
        });
    } catch (e) {
        console.error('Stripe initialization error:', e);
        error.value = 'Failed to initialize payment form';
    }
});

onUnmounted(() => {
    if (card.value) {
        card.value.unmount();
    }
});

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-GB', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (time) => {
    if (!time) return '';
    return new Date(time).toLocaleTimeString('en-GB', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
};

const formatPrice = (value) => {
    const number = parseFloat(value) || 0;
    return number.toFixed(2);
};

const createPaymentIntent = async () => {
    try {
        const isDeposit = parseFloat(props.booking?.deposit_amount || 0) <= 0;
        const paymentAmount = isDeposit ? 6.25 : parseFloat(props.booking?.balance_amount || 0);

        const response = await axios.post(route('payment.create-intent', props.booking.id), {
            booking_id: props.booking.id,
            payment_type: isDeposit ? 'deposit' : 'balance',
            amount: paymentAmount
        });

        return response.data;
    } catch (error) {
        console.error('Error creating payment intent:', error);
        throw error;
    }
};

const handleSubmit = async () => {
    if (processing.value) return;

    processing.value = true;
    error.value = '';

    try {
        const { clientSecret, amount } = await createPaymentIntent();

        const result = await stripe.value.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card.value,
            }
        });

        if (result.error) {
            error.value = result.error.message;
            return;
        }

        if (result.paymentIntent.status === 'succeeded') {
            await axios.post(route('payment.process', props.booking.id), {
                booking_id: props.booking.id,
                payment_intent_id: result.paymentIntent.id,
                amount: amount,
                payment_type: props.booking.deposit_amount > 0 ? 'balance' : 'deposit'
            });

            toast.success('Payment successful!');
            emit('close');
            window.location.reload();
        }
    } catch (e) {
        console.error('Payment error:', e);
        error.value = 'Payment failed. Please try again.';
    } finally {
        processing.value = false;
    }
};
</script>
