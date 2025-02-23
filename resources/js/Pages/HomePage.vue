<template>
  <Head title="Home" />
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <main>
      <section class="relative bg-white overflow-hidden h-[60vh]">
        <div class="absolute inset-0">
          <div class="h-full w-full relative overflow-hidden">
            <img
              src="/images/hero/barber-hero.jpg"
              alt="Barber Shop Background"
              class="w-full h-full object-cover object-center sticky top-0 scale-100"
              style="object-position: center 40%;"
            />
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
          </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
          <div class="flex items-center h-full">
            <div class="text-center lg:text-left w-full py-12">
              <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
                <span class="block">Welcome to</span>
                <span class="block">MickyesBarber</span>
              </h1>
              <p class="mt-3 text-base text-gray-100 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                Professional barbering services with style and precision.
              </p>
              <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                <div class="rounded-md shadow">
                  <a
                    href="#"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-black hover:bg-gray-800 md:py-4 md:text-lg md:px-10 transition-colors duration-300"
                  >
                    Book Now
                  </a>
                </div>
                <div class="mt-3 sm:mt-0 sm:ml-3">
                  <a
                    href="#"
                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 md:py-4 md:text-lg md:px-10 transition-colors duration-300"
                  >
                    Our Services
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- After your hero section, add this services section -->
      <section class="relative bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
              Professional Barber Services
            </h2>
            <p class="mt-4 text-xl text-gray-600">
              Expert grooming services tailored to your style
            </p>
          </div>

          <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            <!-- Classic Cuts -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/classic-cut.jpg"
                  alt="Classic Haircut Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <ScissorsIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Classic Haircuts</h3>
                </div>
                <p class="text-gray-600">
                  From fades to tapers, textured crops to classic gentleman's cuts. Our experienced barbers deliver precision cuts that enhance your natural style.
                </p>
              </div>
            </div>

            <!-- Kids Cuts -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/kids-cut.jpg"
                  alt="Kids Haircut Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <UserGroupIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Kids Haircuts</h3>
                </div>
                <p class="text-gray-600">
                  Specialized in making children feel comfortable and confident. We create fun, age-appropriate styles in a welcoming environment.
                </p>
              </div>
            </div>

            <!-- Hair Coloring -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/hair-color.jpg"
                  alt="Hair Coloring Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <SwatchIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Hair Coloring & Dyeing</h3>
                </div>
                <p class="text-gray-600">
                  Express yourself with our professional coloring services. From subtle highlights to bold fashion colors, we help you achieve your desired look.
                </p>
              </div>
            </div>

            <!-- Beard Grooming -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/beard-grooming.jpg"
                  alt="Beard Grooming Service"
                  class="object-cover w-full h-48 object-center"
                  style="object-position: center center;"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <SparklesIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Beard Grooming</h3>
                </div>
                <p class="text-gray-600">
                  Complete beard care including trimming, shaping, and hot towel treatments. Keep your facial hair looking sharp and well-maintained.
                </p>
              </div>
            </div>

            <!-- Home Service -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/home-service.jpg"
                  alt="Mobile Barber Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <HomeIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Mobile Barber Service</h3>
                </div>
                <p class="text-gray-600">
                  Can't make it to the shop? Our professional barbers come to you. Enjoy the same high-quality service in the comfort of your home.
                </p>
              </div>
            </div>

            <!-- Special Treatments -->
            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  src="/images/services/special-treatment.jpg"
                  alt="Special Treatment Service"
                  class="object-cover w-full h-48"
                />
              </div>
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <HeartIcon class="h-8 w-8 text-green-600 mr-3" />
                  <h3 class="text-xl font-bold text-gray-900">Special Treatments</h3>
                </div>
                <p class="text-gray-600">
                  Scalp treatments, hot towel service, and hair therapy options available. Pamper yourself with our premium grooming experiences.
                </p>
              </div>
            </div>
          </div>

          <!-- Call to Action -->
          <div class="mt-12 text-center">
            <p class="text-xl text-gray-600 mb-8">
              Experience the difference at MickyesBarber. Walk-ins welcome, appointments preferred.
            </p>
            <a
              href="#"
              class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 md:py-4 md:text-lg md:px-10 transition-colors duration-300"
            >
              Book Your Appointment
            </a>
          </div>
        </div>
      </section>

      <!-- Content section -->
      <section class="relative bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="prose prose-lg mx-auto">
            <h2 class="text-3xl font-bold text-center mb-8">Our Services</h2>
            <p class="text-gray-600 text-center max-w-3xl mx-auto">
              At MickyesBarber, we pride ourselves on delivering exceptional grooming services tailored to your individual style.
              Our experienced team of barbers combines traditional techniques with modern trends to ensure you leave our shop
              looking and feeling your best. Whether you're seeking a classic cut or a contemporary style, we're here to exceed
              your expectations.
            </p>
            <!-- Add more content here -->
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">
            Transformations
          </h2>

          <div class="relative max-w-4xl mx-auto">
            <!-- Carousel Container -->
            <div class="relative h-[400px] overflow-hidden rounded-lg shadow-xl">
              <!-- Slides -->
              <div
                v-for="(slide, index) in slides"
                :key="index"
                class="absolute inset-0 transition-opacity duration-500"
                :class="{ 'opacity-0': currentSlide !== index }"
              >
                <div class="flex h-full">
                  <!-- Before Image -->
                  <div class="w-1/2 relative">
                    <img
                      :src="slide.before"
                      :alt="'Before ' + slide.description"
                      class="absolute inset-0 w-full h-full object-cover"
                    />
                    <div class="absolute inset-0 bg-black bg-opacity-30">
                      <span class="absolute top-4 left-4 text-white text-xl font-bold">Before</span>
                    </div>
                  </div>

                  <!-- After Image -->
                  <div class="w-1/2 relative">
                    <img
                      :src="slide.after"
                      :alt="'After ' + slide.description"
                      class="absolute inset-0 w-full h-full object-cover"
                    />
                    <div class="absolute inset-0 bg-black bg-opacity-30">
                      <span class="absolute top-4 right-4 text-white text-xl font-bold">After</span>
                    </div>
                  </div>
                </div>

                <!-- Description -->
                <div class="absolute bottom-0 inset-x-0 bg-black bg-opacity-50 text-white p-4 text-center">
                  <p class="text-lg font-semibold">{{ slide.description }}</p>
                </div>
              </div>
            </div>

            <!-- Navigation Buttons -->
            <button
              @click="prevSlide"
              class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-12 bg-white p-2 rounded-full shadow-lg hover:bg-gray-100 transition-colors duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>

            <button
              @click="nextSlide"
              class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 bg-white p-2 rounded-full shadow-lg hover:bg-gray-100 transition-colors duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>

            <!-- Slide Indicators -->
            <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 flex space-x-2">
              <button
                v-for="(_, index) in slides"
                :key="index"
                @click="currentSlide = index"
                class="w-3 h-3 rounded-full transition-colors duration-200"
                :class="currentSlide === index ? 'bg-green-600' : 'bg-gray-300'"
              />
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800" aria-labelledby="footer-heading">
      <h2 id="footer-heading" class="sr-only">Footer</h2>
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="xl:grid xl:grid-cols-3 xl:gap-8">
          <div class="space-y-8 xl:col-span-1">
            <span class="text-white text-xl font-bold">MickyesBarber</span>
            <p class="text-gray-300 text-base">
              Professional barbering services with style and precision.
            </p>
            <div class="flex space-x-6">
              <!-- Social links -->
              <a href="#" class="text-gray-400 hover:text-gray-300">
                <span class="sr-only">Facebook</span>
                <!-- Add social icons here -->
              </a>
            </div>
          </div>
          <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
            <div class="md:grid md:grid-cols-2 md:gap-8">
              <!-- Footer links -->
              <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Solutions</h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li>
                    <a href="#" class="text-base text-gray-300 hover:text-white">
                      Marketing
                    </a>
                  </li>
                  <!-- Add more links -->
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import {
  ScissorsIcon,
  UserGroupIcon,
  SwatchIcon,
  HomeIcon,
  SparklesIcon,
  HeartIcon
} from '@heroicons/vue/24/outline'
import Navigation from '@/Components/Navigation.vue';

// Define props first
const props = defineProps({
    transformations: {
        type: Array,
        required: true
    }
});

// Then use props in other declarations
const currentSlide = ref(0);
const slides = props.transformations?.slice(0, 3).map(transformation => ({
    before: transformation.before_image,
    after: transformation.after_image,
    description: transformation.description
})) || [];

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length;
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length;
};

// Auto-advance slides
onMounted(() => {
    console.log('Transformations:', props.transformations);
    console.log('Generated slides:', slides);
    setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % slides.length;
    }, 5000);
});
</script>
