<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Navbar from '../Components/Navbar.vue';
import Footer from '../Components/Footer.vue';

const props = defineProps({
    transformations: {
        type: Array,
        required: true
    },
    services: Array,
    reviews: Array
});

const currentSlide = ref(0);

// Use the full URLs directly from the transformations data
const slides = props.transformations?.slice(0, 3).map(transformation => ({
    before: transformation.before_image,  // Already contains full URL
    after: transformation.after_image,    // Already contains full URL
    description: transformation.description
})) || [];

// Add debug logging
onMounted(() => {
    console.log('Slides array:', slides);
    setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % slides.length;
    }, 5000);
});
</script>

<template>
    <Head title="Home" />

    <div class="min-h-screen bg-gray-100">
        <Navbar />

        <!-- Hero Section -->
        <section class="relative h-[600px] bg-cover bg-center" style="background-image: url('/images/hero-bg.jpg')">
            <div class="absolute inset-0 bg-black opacity-50"></div>
            <div class="relative z-10 flex items-center justify-center h-full">
                <div class="text-center text-white">
                    <h1 class="text-5xl font-bold mb-4">Welcome to Mickye's Barbers</h1>
                    <p class="text-xl mb-8">Professional haircuts and styling for everyone</p>
                    <a href="#book" class="bg-white text-black px-8 py-3 rounded-full font-semibold hover:bg-gray-200 transition">
                        Book Now
                    </a>
                </div>
            </div>
        </section>

        <!-- Transformations Section -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center mb-8">Transformations</h2>

                <!-- Debug info -->
                <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                    <p>Number of slides: {{ slides.length }}</p>
                    <p>Current slide index: {{ currentSlide }}</p>
                    <p v-if="slides.length > 0">
                        Current URLs:<br>
                        Before: {{ slides[currentSlide].before }}<br>
                        After: {{ slides[currentSlide].after }}
                    </p>
                </div>

                <div class="relative max-w-4xl mx-auto">
                    <!-- Show carousel if we have transformations -->
                    <div v-if="slides && slides.length > 0">
                        <div class="relative h-[400px] overflow-hidden rounded-lg shadow-xl">
                            <!-- Add debugging info -->
                            <div class="absolute top-0 left-0 z-50 bg-black bg-opacity-50 text-white p-2">
                                <p>Number of slides: {{ slides.length }}</p>
                                <p>Current slide index: {{ currentSlide }}</p>
                                <p v-if="slides.length > 0">
                                    Current URLs:<br>
                                    Before: {{ slides[currentSlide].before }}<br>
                                    After: {{ slides[currentSlide].after }}
                                </p>
                            </div>

                            <div v-for="(slide, index) in slides" :key="index"
                                class="absolute inset-0 transition-opacity duration-500"
                                :class="{ 'opacity-0': currentSlide !== index }">
                                <div class="flex h-full">
                                    <!-- Before Image -->
                                    <div class="w-1/2 relative">
                                        <img :src="slide.before"
                                             :alt="'Before ' + slide.description"
                                             class="absolute inset-0 w-full h-full object-cover"
                                             @error="console.log('Error loading before image:', slide.before)"
                                             @load="console.log('Before image loaded:', slide.before)">
                                        <div class="absolute inset-0 bg-black bg-opacity-30">
                                            <span class="absolute top-4 left-4 text-white text-xl font-bold">Before</span>
                                        </div>
                                    </div>
                                    <!-- After Image -->
                                    <div class="w-1/2 relative">
                                        <img :src="slide.after"
                                             :alt="'After ' + slide.description"
                                             class="absolute inset-0 w-full h-full object-cover"
                                             @error="console.log('Error loading after image:', slide.after)"
                                             @load="console.log('After image loaded:', slide.after)">
                                        <div class="absolute inset-0 bg-black bg-opacity-30">
                                            <span class="absolute top-4 left-4 text-white text-xl font-bold">After</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="absolute bottom-0 inset-x-0 bg-black bg-opacity-50 text-white p-4 text-center">
                                    <p class="text-lg font-semibold">{{ slide.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Show message if no transformations -->
                    <div v-else class="text-center text-gray-500">
                        No transformations available
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <!-- ... existing services section ... -->

        <!-- Reviews Section -->
        <!-- ... existing reviews section ... -->

        <Footer />
    </div>
</template>
