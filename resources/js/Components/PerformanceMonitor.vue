<template>
  <div v-if="showMetrics" class="fixed bottom-4 right-4 bg-gray-900 text-white p-4 rounded-lg shadow-lg text-sm z-50">
    <div class="flex items-center justify-between mb-2">
      <h3 class="font-semibold">Performance Metrics</h3>
      <button @click="showMetrics = false" class="text-gray-400 hover:text-white">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="space-y-1">
      <div class="flex justify-between">
        <span>Load Time:</span>
        <span class="font-mono">{{ loadTime }}ms</span>
      </div>
      <div class="flex justify-between">
        <span>DOM Ready:</span>
        <span class="font-mono">{{ domReady }}ms</span>
      </div>
      <div class="flex justify-between">
        <span>Images Loaded:</span>
        <span class="font-mono">{{ imagesLoaded }}/{{ totalImages }}</span>
      </div>
      <div class="flex justify-between">
        <span>Memory:</span>
        <span class="font-mono">{{ memoryUsage }}MB</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const showMetrics = ref(process.env.NODE_ENV === 'development')
const loadTime = ref(0)
const domReady = ref(0)
const imagesLoaded = ref(0)
const totalImages = ref(0)
const memoryUsage = ref(0)

const startTime = performance.now()

onMounted(() => {
  // Measure DOM ready time
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      domReady.value = Math.round(performance.now() - startTime)
    })
  } else {
    domReady.value = Math.round(performance.now() - startTime)
  }

  // Measure full load time
  window.addEventListener('load', () => {
    loadTime.value = Math.round(performance.now() - startTime)

    // Count images
    const images = document.querySelectorAll('img')
    totalImages.value = images.length

    // Track image loading
    images.forEach(img => {
      if (img.complete) {
        imagesLoaded.value++
      } else {
        img.addEventListener('load', () => {
          imagesLoaded.value++
        })
      }
    })

    // Get memory usage if available
    if (performance.memory) {
      memoryUsage.value = Math.round(performance.memory.usedJSHeapSize / 1024 / 1024)
    }
  })

  // Monitor performance metrics
  if ('PerformanceObserver' in window) {
    const observer = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        if (entry.entryType === 'largest-contentful-paint') {
          console.log('LCP:', entry.startTime)
        }
        if (entry.entryType === 'first-input') {
          console.log('FID:', entry.processingStart - entry.startTime)
        }
      }
    })

    observer.observe({ entryTypes: ['largest-contentful-paint', 'first-input'] })
  }
})

onUnmounted(() => {
  // Cleanup if needed
})
</script>
