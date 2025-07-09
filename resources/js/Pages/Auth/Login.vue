<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in">
          <meta name="description" content="Log in to your Mickyes Coiffure account to book appointments, manage your profile, and more." />
          <meta property="og:title" content="Login - Mickyes Coiffure" />
          <meta property="og:description" content="Access your Mickyes Coiffure account to manage bookings and profile." />
          <meta property="og:type" content="website" />
          <meta property="og:url" content="https://mickyes.com/login" />
          <meta name="twitter:card" content="summary_large_image" />
          <meta name="twitter:title" content="Login - Mickyes Coiffure" />
          <meta name="twitter:description" content="Log in to your Mickyes Coiffure account to book and manage appointments." />
        </Head>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center mb-6">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3V7a3 3 0 10-6 0v1c0 1.657 1.343 3 3 3zm6 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2h8a2 2 0 012 2z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Welcome Back</h2>
                <p class="text-gray-500 text-sm">Sign in to your Mickyes Coiffure account</p>
            </div>
            <div>
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
                        autofocus
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
                        autocomplete="current-password"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>
            <div class="mt-6 flex items-center justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-blue-600 underline hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all"
                >
                    Forgot your password?
                </Link>
                <PrimaryButton
                    class="ml-auto px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-bold rounded-xl shadow-lg hover:from-blue-600 hover:to-purple-600 transition-all text-base tracking-wide"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
