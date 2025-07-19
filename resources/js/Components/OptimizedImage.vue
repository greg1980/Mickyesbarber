<template>
  <picture class="block">
    <!-- WebP source for modern browsers -->
    <source
      v-if="webpSrc && webpSrc !== 'null' && webpSrc !== ''"
      :srcset="webpSrc"
      type="image/webp"
      :sizes="sizes"
    />

    <!-- Fallback image -->
    <img
      v-if="src && src !== 'null' && src !== ''"
      :src="src"
      :alt="alt"
      :class="[imgClass, { 'loaded': isLoaded }]"
      :loading="lazy ? 'lazy' : 'eager'"
      :decoding="decoding"
      @load="onLoad"
      @error="onError"
      v-bind="$attrs"
    />

    <!-- Placeholder when no image is available -->
    <div
      v-else
      :class="[imgClass, 'bg-gray-200 flex items-center justify-center']"
      :style="{ minHeight: '200px' }"
    >
      <div class="text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p class="text-gray-500 text-sm">Image not available</p>
      </div>
    </div>
  </picture>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: false,
    default: null
  },
  webpSrc: {
    type: String,
    default: null
  },
  alt: {
    type: String,
    required: true
  },
  imgClass: {
    type: String,
    default: ''
  },
  lazy: {
    type: Boolean,
    default: true
  },
  decoding: {
    type: String,
    default: 'async'
  },
  sizes: {
    type: String,
    default: '100vw'
  }
})

const emit = defineEmits(['load', 'error'])

const isLoaded = ref(false)
const hasError = ref(false)

const onLoad = () => {
  isLoaded.value = true
  emit('load')
}

const onError = () => {
  hasError.value = true
  emit('error')
}
</script>

<style scoped>
img {
  transition: opacity 0.3s ease-in-out;
}

img[loading="lazy"] {
  opacity: 0;
}

img[loading="lazy"].loaded {
  opacity: 1;
}

/* For non-lazy images, make them visible immediately */
img[loading="eager"] {
  opacity: 1;
}
</style>
