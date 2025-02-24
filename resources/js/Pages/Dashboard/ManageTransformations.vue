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
    before_image: null,
    after_image: null,
    description: ''
});

const handleSubmit = () => {
    console.log('Form submitted');
    console.log('Form data:', {
        before_image: form.before_image,
        after_image: form.after_image,
        description: form.description
    });

    if (!form.before_image || !form.after_image || !form.description.trim()) {
        messageText.value = 'Please fill in all fields';
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
            console.log('Upload successful');
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
    <Head title="Manage Transformations" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Transformations
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
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Before Image</label>
                            <input
                                type="file"
                                @input="form.before_image = $event.target.files[0]"
                                accept="image/*"
                                class="mt-1 block w-full"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">After Image</label>
                            <input
                                type="file"
                                @input="form.after_image = $event.target.files[0]"
                                accept="image/*"
                                class="mt-1 block w-full"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            ></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200 disabled:opacity-50"
                            >
                                {{ isSubmitting ? 'Uploading...' : 'Upload Transformation' }}
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
                            <p class="mt-4 text-gray-600">{{ transformation.description }}</p>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center text-gray-500 py-8">
                    No transformations added yet.
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
