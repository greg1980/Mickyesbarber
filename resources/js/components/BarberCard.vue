<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="relative">
            <img :src="barber.profile_photo" :alt="barber.name" class="w-full h-64 object-cover">
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                <h3 class="text-white text-xl font-semibold">{{ barber.name }}</h3>
                <div class="flex items-center text-yellow-400">
                    <div class="flex items-center">
                        <template v-for="i in 5" :key="i">
                            <svg
                                :class="[
                                    'w-5 h-5',
                                    i <= Math.round(barber.average_rating) ? 'text-yellow-400' : 'text-gray-300'
                                ]"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </template>
                        <span class="ml-2 text-white">
                            {{ barber.average_rating.toFixed(1) }} ({{ barber.total_reviews }} reviews)
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4">
            <p class="text-gray-600 mb-4">{{ barber.bio }}</p>
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-900">Specialties:</h4>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span
                        v-for="specialty in barber.specialties"
                        :key="specialty"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                        {{ specialty }}
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <button
                    @click="$emit('select', barber)"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200"
                >
                    Book with {{ barber.name }}
                </button>
            </div>
        </div>

        <!-- Recent Transformations -->
        <div class="p-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Recent Work</h4>
            <div class="grid grid-cols-2 gap-2">
                <div v-for="transformation in recentTransformations" :key="transformation.id" class="relative">
                    <div class="aspect-w-1 aspect-h-1">
                        <img :src="transformation.after_photo" class="object-cover rounded-lg" :alt="transformation.haircut_style">
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                        <div class="flex items-center text-yellow-400 text-xs">
                            <template v-for="i in 5" :key="i">
                                <svg
                                    :class="[
                                        'w-3 h-3',
                                        i <= transformation.rating ? 'text-yellow-400' : 'text-gray-300'
                                    ]"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    barber: {
        type: Object,
        required: true
    },
    recentTransformations: {
        type: Array,
        default: () => []
    }
});

defineEmits(['select']);
</script>
