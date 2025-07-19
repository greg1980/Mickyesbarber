<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <!-- TODO: For full support, refactor to use a <picture> element for background images. For now, use WebP if supported by browser, fallback to JPEG. -->
    <div
        class="flex min-h-screen flex-col items-center justify-center relative overflow-hidden"
        style="background: url('/images/hero/barber-hero.webp'), url('/images/hero/barber-hero.jpg') center center / cover no-repeat;"
    >
        <!-- White Blur Overlay -->
        <div class="absolute inset-0 bg-white/40 backdrop-blur-md z-0 pointer-events-none"></div>
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500 z-10 relative" />
            </Link>
        </div>
        <div
            class="mt-6 w-full overflow-hidden bg-white/90 px-8 py-8 shadow-2xl sm:max-w-md sm:rounded-2xl border-t-4 border-blue-400 z-10 relative backdrop-blur"
        >
            <slot />
        </div>
    </div>
</template>
