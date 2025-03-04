<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';

const navigation = [
    {
        name: 'Dashboard',
        href: route('admin.dashboard'),
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    {
        name: 'Users',
        href: route('admin.users'),
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    },
    {
        name: 'Bookings',
        href: route('admin.bookings'),
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
    }
];

const isSidebarOpen = ref(true);
const page = usePage();

const currentPath = computed(() => {
    return page.url.split('?')[0].replace(/\/$/, '');
});

const isActive = (path) => {
    const normalizedPath = path.replace(/\/$/, '');
    const normalizedCurrentPath = currentPath.value.replace(/\/$/, '');
    return normalizedCurrentPath === normalizedPath ||
           normalizedCurrentPath.startsWith(normalizedPath + '/');
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Static sidebar for desktop -->
        <div class="hidden md:block md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
            <div class="flex flex-col flex-grow border-r border-gray-200 bg-white pt-0">
                <!-- Sidebar Logo -->
                <div class="flex items-center h-16 flex-shrink-0 px-4 bg-white border-b border-gray-200">
                    <Link :href="route('home')" class="text-xl font-bold text-gray-800">
                        MickyesBarbers
                    </Link>
                </div>

                <!-- Sidebar Navigation -->
                <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
                    <nav class="flex-1 space-y-2">
                        <Link v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-md group"
                            :class="[
                                (item.name === 'Transformations' && route().current('transformations.*')) ||
                                (item.name !== 'Transformations' && route().current('admin.*'))
                                    ? 'bg-gray-100 text-gray-900'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            ]">
                            <svg class="flex-shrink-0 w-6 h-6 mr-3 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col flex-1 md:pl-64">
            <!-- Top nav -->
            <div class="flex-shrink-0">
                <div class="bg-white border-b border-gray-200">
                    <div class="flex justify-between h-16 px-4">
                        <!-- Mobile menu button -->
                        <div class="flex items-center md:hidden">
                            <button @click="toggleSidebar" type="button" class="text-gray-500 hover:text-gray-900">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>

                        <!-- Spacer for flex layout -->
                        <div class="flex-1"></div>

                        <!-- Right side buttons -->
                        <div class="flex items-center justify-end">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center text-sm font-medium text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                        <div class="flex items-center">
                                            <div class="relative h-8 w-8 rounded-full overflow-hidden mr-2">
                                                <img v-if="$page.props.auth.user.profile_photo"
                                                    :src="$page.props.auth.user.profile_photo"
                                                    class="h-full w-full object-cover"
                                                    :alt="$page.props.auth.user.name" />
                                                <div v-else
                                                    class="h-full w-full flex items-center justify-center bg-gray-200 text-gray-600 text-sm font-medium uppercase">
                                                    {{ $page.props.auth.user.name.charAt(0) }}
                                                </div>
                                            </div>
                                            {{ $page.props.auth.user.name }}
                                            <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="py-2">
                                        <div class="px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            My Profile
                                        </DropdownLink>

                                        <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                            </svg>
                                            My Account
                                        </DropdownLink>

                                        <DropdownLink :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                            </svg>
                                            My Task
                                        </DropdownLink>

                                        <div class="border-t border-gray-200 my-2"></div>

                                        <form @submit.prevent="$inertia.post(route('logout'))" class="px-4 py-2">
                                            <button type="submit" class="w-full text-center px-4 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition duration-150 ease-in-out">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content area -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="py-6">
                    <div class="px-4 mx-auto max-w-7xl sm:px-6 md:px-8">
                        <!-- Page header -->
                        <header v-if="$slots.header" class="mb-6">
                            <slot name="header" />
                        </header>

                        <!-- Page content -->
                        <div class="mb-8">
                            <slot />
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Mobile sidebar -->
        <div v-if="isSidebarOpen" class="fixed inset-0 z-40 md:hidden">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="toggleSidebar"></div>

            <!-- Sidebar panel -->
            <div class="relative flex flex-col w-full max-w-xs bg-white">
                <div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
                    <Link :href="route('home')" class="text-xl font-bold text-gray-800">
                        MickyesBarbers
                    </Link>
                    <button @click="toggleSidebar" class="text-gray-500 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 px-4 py-4 overflow-y-auto">
                    <nav class="space-y-2">
                        <Link v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-md group"
                            :class="[
                                (item.name === 'Transformations' && route().current('transformations.*')) ||
                                (item.name !== 'Transformations' && route().current('admin.*'))
                                    ? 'bg-gray-100 text-gray-900'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            ]">
                            <svg class="flex-shrink-0 w-6 h-6 mr-3 text-gray-400 group-hover:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.router-link-active {
    @apply bg-indigo-50 text-indigo-600 border-indigo-600;
}
</style>
