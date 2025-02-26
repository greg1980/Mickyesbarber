<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="showMessage" class="mb-4">
                    <div :class="[
                        'p-4 rounded-md',
                        messageType === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
                    ]">
                        {{ message }}
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-6">Register as a Barber</h2>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Bio -->
                            <div>
                                <InputLabel for="bio" value="Bio" />
                                <textarea
                                    id="bio"
                                    v-model="form.bio"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="4"
                                    required
                                ></textarea>
                                <InputError :message="form.errors.bio" class="mt-2" />
                            </div>

                            <!-- Years of Experience -->
                            <div>
                                <InputLabel for="years_of_experience" value="Years of Experience" />
                                <input
                                    id="years_of_experience"
                                    type="number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :value="form.years_of_experience"
                                    @input="e => form.years_of_experience = e.target.value"
                                    required
                                    min="0"
                                />
                                <InputError :message="form.errors.years_of_experience" class="mt-2" />
                            </div>

                            <!-- Specialties -->
                            <div>
                                <InputLabel value="Specialties" />
                                <div class="mt-2 space-y-2">
                                    <label v-for="specialty in availableSpecialties" :key="specialty" class="flex items-center">
                                        <input
                                            type="checkbox"
                                            :value="specialty"
                                            v-model="form.specialties"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        >
                                        <span class="ml-2">{{ specialty }}</span>
                                    </label>
                                </div>
                                <InputError :message="form.errors.specialties" class="mt-2" />
                            </div>

                            <!-- Profile Photo -->
                            <div>
                                <InputLabel for="profile_photo" value="Profile Photo" />
                                <input
                                    type="file"
                                    @input="form.profile_photo = $event.target.files[0]"
                                    accept="image/*"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.profile_photo" class="mt-2" />
                            </div>

                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Register
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const showMessage = ref(false);
const message = ref('');
const messageType = ref('success');

const availableSpecialties = [
    'Fades',
    'Modern Cuts',
    'Classic Cuts',
    'Beard Trimming',
    'Hot Towel Shave',
    'Hair Design',
    'Color Treatment',
    'Razor Fades',
    'Beard Sculpting',
    'Traditional Cuts'
];

const form = useForm({
    name: '',
    bio: '',
    years_of_experience: '',
    specialties: [],
    profile_photo: null,
});

const submit = () => {
    if (form.processing) return;

    form.post(route('barber.register.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showMessage.value = true;
            message.value = 'Successfully registered as a barber! Redirecting to dashboard...';
            messageType.value = 'success';

            // Redirect to dashboard after a short delay
            setTimeout(() => {
                window.location.href = route('dashboard');
            }, 2000);
        },
        onError: (errors) => {
            showMessage.value = true;
            message.value = Object.values(errors)[0] || 'An error occurred during registration.';
            messageType.value = 'error';

            // Hide error message after 5 seconds
            setTimeout(() => {
                showMessage.value = false;
            }, 5000);
        },
        onStart: () => {
            showMessage.value = true;
            message.value = 'Processing registration...';
            messageType.value = 'success';
        },
        onFinish: () => {
            form.processing = false;
        }
    });
};

// Watch for flash messages from the server
onMounted(() => {
    const page = usePage();
    const flash = page.props.flash;

    if (flash.success || flash.error) {
        message.value = flash.message || flash.success || flash.error;
        messageType.value = flash.type || (flash.success ? 'success' : 'error');
        showMessage.value = true;

        setTimeout(() => {
            showMessage.value = false;
        }, 5000);
    }
});
</script>
