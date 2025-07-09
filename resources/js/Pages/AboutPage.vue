<template>
  <Head title="About">
    <meta name="description" content="Learn about Mickyes Coiffure, our story, our team, and our commitment to professional barbering in Newcastle." />
    <meta property="og:title" content="About Mickyes Coiffure - Our Story & Team" />
    <meta property="og:description" content="Discover the story behind Mickyes Coiffure, Newcastle's trusted barbershop. Meet our experienced team and see why customers love us!" />
    <meta property="og:image" content="/images/hero/pexels-photo-2881253.jpeg" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://mickyesbarbers.com/about" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="About Mickyes Coiffure - Our Story & Team" />
    <meta name="twitter:description" content="Learn about our barbershop, our team, and our passion for great haircuts in Newcastle." />
    <meta name="twitter:image" content="/images/hero/pexels-photo-2881253.jpeg" />
  </Head>
  <Navigation :auth="$page.props.auth" />
  <div class="min-h-screen bg-gray-50">
    <main>
      <!-- Hero Section -->
      <section class="relative bg-white overflow-hidden">
        <div class="absolute inset-0">
          <div class="h-full w-full relative overflow-hidden">
            <img
              src="/images/hero/pexels-photo-2881253.jpeg"
                              alt="About Mickyes Coiffure"
              class="w-full h-full object-cover object-center"
            />
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
          </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
          <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
              About Us
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-100 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
              Your trusted destination for professional barbering services
            </p>
          </div>
        </div>
      </section>

      <!-- Our Story -->
      <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:text-center">
            <h2 class="text-base text-green-600 font-semibold tracking-wide uppercase">Our Story</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              A Legacy of Excellence
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
              Founded with a passion for traditional barbering and modern style, Mickyes Coiffure brings together {{ barbers ? barbers.length : 'talented' }} skilled professionals committed to excellence.
            </p>
          </div>

          <div class="mt-10">
            <div class="prose prose-lg text-gray-500 mx-auto">
              <p>
                At Mickyes Coiffure, we believe that a great haircut is more than just a service – it's an experience. Our team of skilled barbers combines traditional techniques with contemporary styles to create looks that are uniquely yours.
              </p>
              <p>
                                  We take pride in our attention to detail and commitment to customer satisfaction. Every visit to Mickyes Coiffure is an opportunity to relax, refresh, and leave feeling confident and looking your best.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Our Values -->
      <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:text-center">
            <h2 class="text-base text-green-600 font-semibold tracking-wide uppercase">Our Values</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              What Sets Us Apart
            </p>
          </div>

          <div class="mt-10">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
              <!-- Quality -->
              <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center mr-4 shadow-lg shadow-emerald-200 transform transition-all duration-300 hover:scale-110">
                    <SparklesIcon class="h-6 w-6 text-white" />
                  </div>
                  <h3 class="text-xl font-bold text-gray-900">Quality</h3>
                </div>
                <p class="text-gray-600">
                  We use only the finest products and maintain the highest standards in our services to ensure you receive the best possible experience.
                </p>
              </div>

              <!-- Expertise -->
              <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-lg shadow-blue-200 transform transition-all duration-300 hover:scale-110">
                    <AcademicCapIcon class="h-6 w-6 text-white" />
                  </div>
                  <h3 class="text-xl font-bold text-gray-900">Expertise</h3>
                </div>
                <p class="text-gray-600">
                  Our barbers are highly trained professionals with years of experience in creating the perfect look for every client.
                </p>
              </div>

              <!-- Community -->
              <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center mb-4">
                  <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mr-4 shadow-lg shadow-purple-200 transform transition-all duration-300 hover:scale-110">
                    <UserGroupIcon class="h-6 w-6 text-white" />
                  </div>
                  <h3 class="text-xl font-bold text-gray-900">Community</h3>
                </div>
                <p class="text-gray-600">
                  We're more than just a barbershop – we're a gathering place for our community, where relationships are built and maintained.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Team Section -->
      <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="lg:text-center">
            <h2 class="text-base text-green-600 font-semibold tracking-wide uppercase">Our Team</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
              Meet the Professionals
            </p>
          </div>

          <div class="mt-10">
            <!-- Show message if no barbers -->
            <div v-if="!barbers || barbers.length === 0" class="text-center py-12">
              <div class="bg-gray-100 rounded-lg p-8">
                <UserGroupIcon class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">Our Team is Growing</h3>
                <p class="text-gray-600">We're currently building our team of professional barbers. Check back soon!</p>
              </div>
            </div>

            <!-- Dynamic Barber Grid -->
            <div v-else class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
              <div v-for="barber in barbers" :key="barber.id" class="bg-gray-50 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative">
                  <img
                    :src="barber.photo_url"
                    :alt="barber.name"
                    class="w-full h-64 object-cover"
                  />
                  <!-- Rating Overlay -->
                  <div v-if="barber.total_reviews > 0" class="absolute top-4 right-4 bg-white bg-opacity-95 rounded-full px-3 py-1 shadow-lg">
                    <div class="flex items-center space-x-1">
                      <span class="text-sm font-bold text-yellow-500">{{ barber.avg_rating }}</span>
                      <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.385 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.385-2.46a1 1 0 00-1.175 0l-3.385 2.46c-.784.57-1.838-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.385-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z" />
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="p-6">
                  <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xl font-bold text-gray-900">{{ barber.name }}</h3>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                      {{ barber.years_of_experience }}+ years
                    </span>
                  </div>
                  <p class="text-green-600 mb-3 font-medium">{{ barber.title }}</p>
                  <p class="text-gray-600 mb-4">{{ barber.bio }}</p>

                  <!-- Rating Stars -->
                  <div v-if="barber.total_reviews > 0" class="flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                      <div class="flex">
                        <span v-for="star in 5" :key="star"
                              :class="star <= Math.round(barber.avg_rating) ? 'text-yellow-400' : 'text-gray-300'"
                              class="text-lg">★</span>
                      </div>
                      <span class="text-sm text-gray-500">({{ barber.total_reviews }} reviews)</span>
                    </div>
                  </div>
                  <div v-else class="text-sm text-gray-400 italic">New team member</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Section -->
      <section class="py-12 bg-gradient-to-r from-gray-50 to-green-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
            <div class="text-center">
              <div class="text-3xl font-bold text-gray-900">{{ barbers ? barbers.length : 0 }}</div>
              <div class="text-sm text-gray-600 uppercase tracking-wide">Expert Barbers</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-gray-900">{{ getTotalExperience() }}+</div>
              <div class="text-sm text-gray-600 uppercase tracking-wide">Years Combined Experience</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-gray-900">{{ getAverageRating() }}</div>
              <div class="text-sm text-gray-600 uppercase tracking-wide">Average Rating</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-gray-900">{{ getTotalReviews() }}+</div>
              <div class="text-sm text-gray-600 uppercase tracking-wide">Happy Customers</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Call to Action -->
      <section class="py-16 bg-gradient-to-r from-green-600 to-green-700">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
          <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
            Ready for Your Perfect Cut?
          </h2>
          <p class="mt-4 text-lg text-green-100">
            Join thousands of satisfied customers who trust our professional barbers for their grooming needs.
          </p>
          <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              :href="route('booking.create')"
              class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-green-600 bg-white hover:bg-green-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
              </svg>
              Book Your Appointment
            </Link>
            <Link
              :href="route('services')"
              class="inline-flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-md text-white bg-transparent hover:bg-white hover:text-green-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              View Our Services
            </Link>
          </div>
        </div>
      </section>
    </main>
  </div>
  <Footer />
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Navigation from '@/Components/Navigation.vue'
import Footer from '@/Components/Footer.vue'
import {
  SparklesIcon,
  AcademicCapIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  barbers: Array
})

// Stats calculations
const getTotalExperience = () => {
  if (!props.barbers || props.barbers.length === 0) return 0
  return props.barbers.reduce((total, barber) => total + (barber.years_of_experience || 0), 0)
}

const getAverageRating = () => {
  if (!props.barbers || props.barbers.length === 0) return '0.0'
  const barbersWithRatings = props.barbers.filter(barber => barber.total_reviews > 0)
  if (barbersWithRatings.length === 0) return '0.0'

  const totalRating = barbersWithRatings.reduce((total, barber) => total + barber.avg_rating, 0)
  return (totalRating / barbersWithRatings.length).toFixed(1)
}

const getTotalReviews = () => {
  if (!props.barbers || props.barbers.length === 0) return 0
  return props.barbers.reduce((total, barber) => total + (barber.total_reviews || 0), 0)
}
</script>
