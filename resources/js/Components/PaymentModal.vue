<template>
    <Modal :show="show" @close="$emit('close')" class="payment-modal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Complete Payment
            </h2>

            <template v-if="!paymentSuccess">
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-gray-600">Booking Date:</div>
                        <div>{{ formatDate(booking?.booking_date) }}</div>

                        <div class="text-gray-600">Time:</div>
                        <div>{{ formatTime(booking?.booking_time) }}</div>

                        <div class="text-gray-600">Barber:</div>
                        <div>{{ booking?.barber?.user?.name }}</div>

                        <div class="text-gray-600">Total Price:</div>
                        <div>£{{ formatPrice(booking?.service_price) }}</div>

                        <div class="text-gray-600">Amount Paid:</div>
                        <div>£{{ formatPrice(booking?.amount_paid) }}</div>

                        <div class="text-gray-600 font-semibold">Balance Due:</div>
                        <div class="font-semibold">£{{ formatPrice(balanceDue) }}</div>
                    </div>
                </div>

                <!-- Payment Options -->
                <div v-if="booking.amount_paid == 0" class="mb-6">
                    <h3 class="mb-2 font-semibold">How would you like to pay?</h3>
                    <label class="block mb-2">
                        <input type="radio" v-model="paymentType" value="deposit" />
                        <span class="ml-2">Pay 10% deposit (£{{ depositAmount }})</span>
                    </label>
                    <label class="block mb-4">
                        <input type="radio" v-model="paymentType" value="full" />
                        <span class="ml-2">Pay full amount (£{{ formatPrice(booking?.service_price) }})</span>
                    </label>
                </div>

                <!-- Card input only if paymentType is selected or balance due -->
                <div v-if="(booking.amount_paid == 0 && paymentType) || (booking.deposit_paid && balanceDue > 0)" class="mb-6">
                    <div id="card-element" class="mt-2"></div>
                    <div id="card-errors" class="text-red-500 text-sm mt-2">{{ error }}</div>
                </div>

                <div v-if="error" class="text-red-600 text-center mb-4">{{ error }}</div>

                <div class="flex justify-end space-x-3">
                    <SecondaryButton
                        @click="$emit('close')"
                        :disabled="processing"
                    >
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton
                        v-if="booking.amount_paid == 0 && paymentType"
                        @click="handleSubmit"
                        :disabled="processing"
                    >
                        Pay {{ paymentType === 'deposit' ? 'Deposit' : 'Full Amount' }} £{{ paymentType === 'deposit' ? depositAmount : formatPrice(booking?.service_price) }}
                    </PrimaryButton>
                    <PrimaryButton
                        v-else-if="booking.deposit_paid && balanceDue > 0"
                        @click="handleSubmitBalance"
                        :disabled="processing"
                    >
                        Pay Balance £{{ formatPrice(balanceDue) }}
                    </PrimaryButton>
                </div>
            </template>

            <template v-else>
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Payment Successful!</h3>
                    <p class="text-sm text-gray-600 mb-6">Your payment and booking have been confirmed.</p>
                    <a
                        href="/dashboard"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    >
                        Return to Dashboard
                    </a>
                </div>
            </template>
        </div>
    </Modal>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useToast } from 'vue-toastification';
import { usePage } from '@inertiajs/vue3';
import { loadStripe } from '@stripe/stripe-js';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    booking: Object
});

const emit = defineEmits(['close', 'payment-success']);
const toast = useToast();
const stripe = ref(null);
const card = ref(null);
const processing = ref(false);
const error = ref('');
const initialized = ref(false);
const paymentSuccess = ref(false);
const paymentType = ref('');

const depositAmount = computed(() => {
    return (props.booking?.service_price * 0.10).toFixed(2);
});
const balanceDue = computed(() => {
    return (props.booking?.service_price - (props.booking?.amount_paid || 0)).toFixed(2);
});

watch(
    [() => props.show, paymentType],
    async ([show, type]) => {
        // Only mount card element if modal is open and card input is visible
        if (
            show &&
            ((props.booking.amount_paid == 0 && type) || (props.booking.deposit_paid && balanceDue.value > 0))
        ) {
            await nextTick();
            if (!stripe.value) {
                const stripeKey = usePage().props.stripe_key;
                if (!stripeKey) {
                    error.value = 'Stripe key is missing';
                    return;
                }
                stripe.value = await loadStripe(stripeKey);
            }
            if (card.value) {
                card.value.unmount();
            }
                const elements = stripe.value.elements();
                card.value = elements.create('card');
                card.value.mount('#card-element');
                card.value.on('change', (event) => {
                error.value = event.error ? event.error.message : '';
                });
                initialized.value = true;
        } else if (!show && card.value) {
            card.value.unmount();
            initialized.value = false;
        }
    }
);

onUnmounted(() => {
    if (card.value) {
        card.value.unmount();
    }
    initialized.value = false;
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

const createPaymentIntent = async (type, amount) => {
    try {
        const response = await axios.post(route('payment.create-intent', props.booking.id), {
            booking_id: props.booking.id,
            payment_type: type,
            amount: amount
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
        const type = paymentType.value;
        const amount = type === 'deposit' ? parseFloat(depositAmount.value) : parseFloat(props.booking?.service_price);
        const { clientSecret } = await createPaymentIntent(type, amount);
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
                payment_type: type
            });
            toast.success('Payment successful!');
            paymentSuccess.value = true;
        }
    } catch (e) {
        console.error('Payment error:', e);
        error.value = 'Payment failed. Please try again.';
    } finally {
        processing.value = false;
    }
};

const handleSubmitBalance = async () => {
    if (processing.value) return;
    processing.value = true;
    error.value = '';
    try {
        const amount = parseFloat(balanceDue.value);
        const { clientSecret } = await createPaymentIntent('full', amount);
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
                payment_type: 'full'
            });
            toast.success('Payment successful!');
            paymentSuccess.value = true;
        }
    } catch (e) {
        console.error('Payment error:', e);
        error.value = 'Payment failed. Please try again.';
    } finally {
        processing.value = false;
    }
};
</script>
