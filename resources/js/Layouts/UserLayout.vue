<script setup>
import { ref, h } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import {
    CalendarIcon,
    UserIcon,
    ClockIcon,
    CurrencyDollarIcon,
    PhotoIcon,
    ChatBubbleLeftRightIcon,
} from '@heroicons/vue/24/outline';

const DashboardIcon = (props) => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'currentColor',
    viewBox: '0 0 20 20',
    class: props.class,
    'aria-hidden': 'true',
}, [
    h('path', {
        d: 'M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z'
    })
]);

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;

const navigation = [
    {
        name: 'Dashboard',
        href: route('dashboard'),
        icon: DashboardIcon,
        active: route().current('dashboard')
    },
    {
        name: 'Book Appointment',
        href: route('bookings.create'),
        icon: CalendarIcon,
        active: route().current('bookings.create')
    },
    {
        name: 'My Appointments',
        href: route('bookings.user'),
        icon: ClockIcon,
        active: route().current('bookings.user')
    },
    {
        name: 'My Transformations',
        href: route('transformations.index'),
        icon: PhotoIcon,
        active: route().current('transformations.*')
    },
    {
        name: 'Messages',
        href: route('messages.index'),
        icon: ChatBubbleLeftRightIcon,
        active: route().current('messages.*')
    },
    // Only show Register as Barber if user is not already a barber
    ...(!user.isBarber ? [{
        name: 'Register as Barber',
        href: route('barber.register'),
        icon: UserIcon,
        active: route().current('barber.register')
    }] : [])
];
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Top Navigation -->
        <div class="bg-white border-b border-gray-200">
            <div class="flex justify-between h-16 px-4">
                <div class="flex-1"></div>
                <!-- Right side buttons -->
                <div class="flex items-center justify-end">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center text-sm font-medium text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                <div class="flex items-center">
                                    <div class="relative h-8 w-8 rounded-full overflow-hidden mr-2">
                                        <img v-if="user.profile_photo"
                                            :src="user.profile_photo"
                                            class="h-full w-full object-cover"
                                            :alt="user.name" />
                                        <div v-else
                                            class="h-full w-full flex items-center justify-center bg-gray-200 text-gray-600 text-sm font-medium uppercase">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                    </div>
                                    {{ user.name }}
                                    <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                Profile Settings
                            </DropdownLink>

                            <div class="border-t border-gray-200 my-2"></div>

                            <form @submit.prevent="$inertia.post(route('logout'))" class="px-4 py-2">
                                <button type="submit" class="w-full text-left text-gray-700 hover:bg-gray-100 px-4 py-2 text-sm">
                                    Logout
                                </button>
                            </form>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </div>

        <!-- Side Navigation -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4 bg-white border-b border-gray-200">
                    <Link :href="route('dashboard')" class="text-gray-900 text-xl font-bold">
                        MickyesBarber
                    </Link>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            item.active
                                ? 'bg-gray-900 text-white'
                                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                            'group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-150'
                        ]"
                    >
                        <component
                            :is="item.icon"
                            :class="[
                                item.active ? 'text-white' : 'text-gray-400 group-hover:text-gray-500',
                                'mr-3 h-5 w-5 flex-shrink-0'
                            ]"
                            aria-hidden="true"
                        />
                        {{ item.name }}
                    </Link>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pl-64">
            <main class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
