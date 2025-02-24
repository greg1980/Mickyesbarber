<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Rate Your Haircut
                            </h3>

                            <div class="mt-4 space-y-4">
                                <!-- Before & After Photos -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Before Photo</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <div v-if="beforePreview" class="relative">
                                                    <img :src="beforePreview" class="mx-auto h-32 w-32 object-cover rounded">
                                                    <button @click="beforePhoto = null; beforePreview = null" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div v-else>
                                                    <input @change="onBeforePhotoChange" type="file" class="sr-only" ref="beforeInput">
                                                    <button @click="$refs.beforeInput.click()" type="button" class="text-sm text-blue-600 hover:text-blue-500">
                                                        Upload photo
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">After Photo</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <div v-if="afterPreview" class="relative">
                                                    <img :src="afterPreview" class="mx-auto h-32 w-32 object-cover rounded">
                                                    <button @click="afterPhoto = null; afterPreview = null" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div v-else>
                                                    <input @change="onAfterPhotoChange" type="file" class="sr-only" ref="afterInput">
                                                    <button @click="$refs.afterInput.click()" type="button" class="text-sm text-blue-600 hover:text-blue-500">
                                                        Upload photo
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Rating -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Rating</label>
                                    <div class="mt-1 flex items-center space-x-1">
                                        <button
                                            v-for="i in 5"
                                            :key="i"
                                            @click="rating = i"
                                            class="focus:outline-none"
                                        >
                                            <svg
                                                :class="[
                                                    'w-8 h-8',
                                                    i <= rating ? 'text-yellow-400' : 'text-gray-300'
                                                ]"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Review -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Review</label>
                                    <textarea
                                        v-model="review"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Share your experience..."
                                    ></textarea>
                                </div>

                                <!-- Haircut Style -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Haircut Style</label>
                                    <input
                                        type="text"
                                        v-model="haircutStyle"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="e.g., Fade, Crew Cut, etc."
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button
                        type="button"
                        class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                        :disabled="!isValid"
                        @click="submit"
                    >
                        Submit Review
                    </button>
                    <button
                        type="button"
                        class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="$emit('close')"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    show: Boolean,
    booking: Object
});

const emit = defineEmits(['close', 'submit']);

const beforePhoto = ref(null);
const afterPhoto = ref(null);
const beforePreview = ref(null);
const afterPreview = ref(null);
const rating = ref(0);
const review = ref('');
const haircutStyle = ref('');

const isValid = computed(() => {
    return beforePhoto.value && afterPhoto.value && rating.value > 0;
});

const onBeforePhotoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        beforePhoto.value = file;
        beforePreview.value = URL.createObjectURL(file);
    }
};

const onAfterPhotoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        afterPhoto.value = file;
        afterPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    const formData = new FormData();
    formData.append('before_photo', beforePhoto.value);
    formData.append('after_photo', afterPhoto.value);
    formData.append('rating', rating.value);
    formData.append('review', review.value);
    formData.append('haircut_style', haircutStyle.value);
    formData.append('booking_id', props.booking.id);
    formData.append('barber_id', props.booking.barber_id);

    emit('submit', formData);
};
</script>
