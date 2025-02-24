<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { UserIcon, EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline';

defineProps({
    auth: Object
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <Navigation :auth="$page.props.auth" />

    <GuestLayout>
        <form @submit.prevent="submit">
            <div class="flex items-center mb-4">
                <InputLabel for="name" value="Name" class="w-24 mr-4" />
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <UserIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="custom-input block w-full pl-10 border-gray-400 hover:border-gray-300 transition-colors duration-200 rounded-md shadow-sm"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>
            </div>

            <div class="flex items-center mb-4">
                <InputLabel for="email" value="Email" class="w-24 mr-4" />
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <EnvelopeIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="custom-input block w-full pl-10 border-gray-400 hover:border-gray-300 transition-colors duration-200 rounded-md shadow-sm"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-1" :message="form.errors.email" />
                </div>
            </div>

            <div class="flex items-center mb-4">
                <InputLabel for="password" value="Password" class="w-24 mr-4" />
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <LockClosedIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="custom-input block w-full pl-10 border-gray-400 hover:border-gray-300 transition-colors duration-200 rounded-md shadow-sm"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>
            </div>

            <div class="flex items-center mb-4">
                <InputLabel for="password_confirmation" value="Confirm Password" class="w-24 mr-4" />
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <LockClosedIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="custom-input block w-full pl-10 border-gray-400 hover:border-gray-300 transition-colors duration-200 rounded-md shadow-sm"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-1" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="flex items-center mb-4">
                <div class="w-24 mr-4"></div>
                <div class="flex-1 flex items-center justify-between">
                    <PrimaryButton
                        class="bg-green-600 hover:bg-green-700 transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-lg active:scale-95 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        <span class="flex items-center">
                            Register
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </span>
                    </PrimaryButton>

                    <Link
                        :href="route('login')"
                        class="text-sm text-gray-600 hover:text-gray-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        Already registered?
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

<style>
.custom-input {
    border-width: 2px !important;
}

.custom-input:focus {
    --tw-ring-color: rgb(229 231 235) !important; /* gray-200 */
    --tw-ring-offset-shadow: none !important;
    --tw-ring-shadow: 0 0 0 2px var(--tw-ring-color) !important;
    box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000) !important;
    border-color: #D1D5DB !important; /* gray-300 */
    outline: none !important;
}

.custom-input:focus-visible {
    outline: none !important;
}
</style>
