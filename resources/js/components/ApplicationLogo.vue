<template>
    <div class="flex items-center">
        <img
            src="/images/logo.png"
            alt="MickyesBarbers Logo"
            class="h-12 w-auto"
            @error="handleImageError"
            v-if="!imageError"
        />
        <h1 v-else class="text-2xl font-bold text-gray-800">
            Mickyes<span class="text-blue-600">Barbers</span>
        </h1>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const imageError = ref(false);

const handleImageError = () => {
    imageError.value = true;
};
</script>
