<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:modelValue', 'update:checked']);

const props = defineProps({
    modelValue: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        type: String,
        default: null,
    },
    checked: {
        type: Boolean,
        default: false,
    },
});

const proxyChecked = computed({
    get() {
        return props.modelValue || props.checked;
    },
    set(val) {
        emit('update:modelValue', val);
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
    />
</template>
