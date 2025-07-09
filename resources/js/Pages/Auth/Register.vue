<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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
    <GuestLayout>
        <Head title="Register">
          <meta name="description" content="Create your Mickyes Coiffure account to book appointments and manage your profile online." />
          <meta property="og:title" content="Register - Mickyes Coiffure" />
          <meta property="og:description" content="Sign up for Mickyes Coiffure to book appointments and manage your profile." />
          <meta property="og:type" content="website" />
          <meta property="og:url" content="https://mickyes.com/register" />
          <meta name="twitter:card" content="summary_large_image" />
          <meta name="twitter:title" content="Register - Mickyes Coiffure" />
          <meta name="twitter:description" content="Create your Mickyes Coiffure account to book and manage appointments online." />
        </Head>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center mb-6">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 14v6m-8-6v6m8-6a4 4 0 10-8 0m8-6a4 4 0 11-8 0 8 8 0 018 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Create Account</h2>
                <p class="text-gray-500 text-sm">Sign up for Mickyes Coiffure</p>
            </div>
            <div>
                <InputLabel for="name" value="Name" />
                <div class="relative mt-1">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    <TextInput
                        id="name"
                        type="text"
                        class="block w-full pl-10 py-3 text-base rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <div class="relative mt-1">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0a4 4 0 11-8 0 8 8 0 018 0z" />
                        </svg>
                    </span>
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full pl-10 py-3 text-base rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <div class="relative mt-1">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3V7a3 3 0 10-6 0v1c0 1.657 1.343 3 3 3zm6 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2h8a2 2 0 012 2z" />
                        </svg>
                    </span>
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full pl-10 py-3 text-base rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <div class="relative mt-1">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3V7a3 3 0 10-6 0v1c0 1.657 1.343 3 3 3zm6 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2h8a2 2 0 012 2z" />
                        </svg>
                    </span>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="block w-full pl-10 py-3 text-base rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>
            <div class="mt-6 flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-blue-600 underline hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all"
                >
                    Already registered?
                </Link>
                <PrimaryButton
                    class="ml-auto px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-bold rounded-xl shadow-lg hover:from-blue-600 hover:to-purple-600 transition-all text-base tracking-wide"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
