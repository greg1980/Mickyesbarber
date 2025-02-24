<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen relative">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img
                src="/images/hero/barber-hero.jpg"
                alt="Background"
                class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0">
            <div>
                <Link href="/">
                    <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
                </Link>
            </div>

            <div class="mt-6 w-full overflow-hidden bg-white/90 backdrop-blur-sm px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                <slot />
            </div>
        </div>
    </div>
</template>
