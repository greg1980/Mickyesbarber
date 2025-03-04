<script setup>
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const imageError = ref(false);

const handleImageError = () => {
    imageError.value = true;
};
</script>

<template>
    <div class="min-h-screen relative">
        <!-- Background with Fallback -->
        <div class="absolute inset-0 z-0">
            <template v-if="!imageError">
                <img
                    src="/images/hero/barber-hero.jpg"
                    alt="Background"
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                />
            </template>
            <div
                :class="[
                    'absolute inset-0',
                    imageError ? 'bg-gradient-to-br from-gray-900 to-blue-900' : 'bg-opacity-0'
                ]"
            ></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0">
            <div>
                <Link href="/" class="flex items-center justify-center">
                    <ApplicationLogo class="h-20 w-20" />
                </Link>
            </div>

            <div class="mt-6 w-full overflow-hidden bg-white/90 backdrop-blur-sm px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                <slot />
            </div>
        </div>
    </div>
</template>
