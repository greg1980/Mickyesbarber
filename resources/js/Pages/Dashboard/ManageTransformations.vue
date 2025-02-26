<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    transformations: {
        type: Array,
        default: () => []
    },
    bookings: {
        type: Array,
        default: () => []
    },
    barbers: {
        type: Array,
        default: () => []
    }
});

const isSubmitting = ref(false);
const showMessage = ref(false);
const messageText = ref('');
const messageType = ref('success');

// Watch for flash messages
watch(() => usePage().props.flash, (newFlash) => {
    if (newFlash?.success || newFlash?.error) {
        messageText.value = newFlash.success || newFlash.error;
        messageType.value = newFlash.success ? 'success' : 'error';
        showMessage.value = true;
        setTimeout(() => {
            showMessage.value = false;
        }, 5000);
    }
}, { deep: true });

const form = useForm({
    before_photo: null,
    after_photo: null,
    rating: '',
    review: '',
    haircut_style: '',
    booking_id: ''
});

const handleSubmit = () => {
    if (!form.before_photo || !form.after_photo || !form.haircut_style || !form.rating) {
        messageText.value = 'Please fill in all required fields';
        messageType.value = 'error';
        showMessage.value = true;
        setTimeout(() => {
            showMessage.value = false;
        }, 3000);
        return;
    }

    isSubmitting.value = true;

    form.post(route('transformations.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            isSubmitting.value = false;
            messageText.value = 'Transformation uploaded successfully!';
            messageType.value = 'success';
            showMessage.value = true;
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        },
        onError: (errors) => {
            console.error('Upload failed:', errors);
            isSubmitting.value = false;
            messageText.value = Object.values(errors)[0];
            messageType.value = 'error';
            showMessage.value = true;
        }
    });
};

onMounted(() => {
    console.log('Component mounted');
    console.log('Transformations:', props.transformations);
});
</script>

<template>
    <Head title="My Transformations" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Transformations
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Message Toast -->
                <div
                    v-if="showMessage"
                    :class="[
                        'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-500',
                        messageType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                    ]"
                >
                    {{ messageText }}
                </div>

                <!-- Loading Overlay -->
                <div
                    v-if="isSubmitting"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
                    <div class="bg-white p-6 rounded-lg shadow-xl">
                        <p class="text-lg">Uploading transformation...</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Transformation</h3>
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select Booking *</label>
                            <select
                                v-model="form.booking_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                required
                            >
                                <option value="">Select a completed booking</option>
                                <option v-for="booking in bookings" :key="booking.id" :value="booking.id">
                                    {{ new Date(booking.booking_date).toLocaleDateString() }} - {{ booking.booking_time }} with {{ booking.barber.name }}
                                </option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Only completed bookings are available for review</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Before Photo *</label>
                            <input
                                type="file"
                                @input="form.before_photo = $event.target.files[0]"
                                accept="image/*"
                                class="mt-1 block w-full"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">After Photo *</label>
                            <input
                                type="file"
                                @input="form.after_photo = $event.target.files[0]"
                                accept="image/*"
                                class="mt-1 block w-full"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Haircut Style *</label>
                            <input
                                type="text"
                                v-model="form.haircut_style"
                                placeholder="e.g., Fade, Crew Cut, etc."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rating *</label>
                            <div class="mt-1 flex items-center space-x-2">
                                <template v-for="star in 5" :key="star">
                                    <button
                                        type="button"
                                        @click="form.rating = star"
                                        class="text-2xl focus:outline-none"
                                        :class="star <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
                                    >
                                        ★
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Review</label>
                            <textarea
                                v-model="form.review"
                                rows="3"
                                placeholder="Share your experience with the haircut..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            ></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200 disabled:opacity-50"
                            >
                                {{ isSubmitting ? 'Uploading...' : 'Submit Review' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Transformations Display -->
                <div v-if="transformations.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        v-for="transformation in transformations"
                        :key="transformation.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden"
                    >
                        <div class="p-4">
                            <!-- Admin Info -->
                            <div v-if="$page.props.auth.user.is_admin" class="mb-4 border-b pb-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Customer:</span>
                                        <span class="ml-2 text-sm text-gray-600">{{ transformation.user.name }}</span>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Date:</span>
                                        <span class="ml-2 text-sm text-gray-600">{{ new Date(transformation.created_at).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <span class="text-sm font-medium text-gray-700">Barber:</span>
                                    <span class="ml-2 text-sm text-gray-600">{{ transformation.barber.name }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">Before</h4>
                                    <img
                                        :src="transformation.before_image"
                                        alt="Before transformation"
                                        class="w-full h-48 object-cover rounded"
                                    >
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">After</h4>
                                    <img
                                        :src="transformation.after_image"
                                        alt="After transformation"
                                        class="w-full h-48 object-cover rounded"
                                    >
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-gray-600">{{ transformation.review }}</p>
                                <div class="mt-2 flex items-center">
                                    <span class="text-sm font-medium text-gray-700">Style:</span>
                                    <span class="ml-2 text-sm text-gray-600">{{ transformation.haircut_style }}</span>
                                </div>
                                <div class="mt-1 flex items-center">
                                    <span class="text-sm font-medium text-gray-700">Rating:</span>
                                    <div class="ml-2 flex">
                                        <span v-for="n in transformation.rating" :key="n" class="text-yellow-400">★</span>
                                        <span v-for="n in 5 - transformation.rating" :key="n + transformation.rating" class="text-gray-300">★</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center text-gray-500 py-8">
                    {{ $page.props.auth.user.is_admin ? 'No transformations found.' : 'You haven\'t added any transformations yet.' }}
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
