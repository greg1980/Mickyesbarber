<template>
    <button
        :type="type"
        :class="[
            'inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-medium transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2',
            {
                'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-500': variant === 'primary',
                'bg-gray-500 text-white hover:bg-gray-600 focus:ring-gray-500': variant === 'secondary',
                'bg-green-500 text-white hover:bg-green-600 focus:ring-green-500': variant === 'success',
                'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500': variant === 'warning',
                'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500': variant === 'danger',
                'bg-cyan-500 text-white hover:bg-cyan-600 focus:ring-cyan-500': variant === 'info',
                'bg-gray-900 text-white hover:bg-gray-800 focus:ring-gray-900': variant === 'dark',
                'bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-200': variant === 'light',
                'text-blue-600 underline hover:text-blue-900': variant === 'link',
                'opacity-50 cursor-not-allowed': disabled
            },
            className
        ]"
        :disabled="disabled || processing"
    >
        <div v-if="processing" class="mr-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <slot />
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => [
            'primary',
            'secondary',
            'success',
            'warning',
            'danger',
            'info',
            'dark',
            'light',
            'link'
        ].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    },
    processing: {
        type: Boolean,
        default: false
    },
    className: {
        type: String,
        default: ''
    }
});
</script>
