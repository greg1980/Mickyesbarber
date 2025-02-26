<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.success }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ user.name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">
                                                {{ user.email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                            ]">
                                                {{ user.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                user.barber ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'
                                            ]">
                                                {{ user.barber ? 'Barber' : 'Customer' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <!-- Toggle Status -->
                                            <button
                                                @click="toggleUserStatus(user)"
                                                :class="[
                                                    'px-3 py-1 rounded-md text-sm font-medium',
                                                    user.is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'
                                                ]"
                                            >
                                                {{ user.is_active ? 'Deactivate' : 'Activate' }}
                                            </button>

                                            <!-- Assign/Remove Barber -->
                                            <button
                                                v-if="!user.barber"
                                                @click="showAssignBarberModal(user)"
                                                class="bg-purple-100 text-purple-700 hover:bg-purple-200 px-3 py-1 rounded-md text-sm font-medium"
                                            >
                                                Assign as Barber
                                            </button>
                                            <button
                                                v-else
                                                @click="removeBarber(user)"
                                                class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded-md text-sm font-medium"
                                            >
                                                Remove Barber
                                            </button>

                                            <!-- Delete User -->
                                            <button
                                                @click="deleteUser(user)"
                                                class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            <Pagination :links="users.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Barber Modal -->
        <Modal :show="showingAssignBarberModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Assign User as Barber
                </h2>

                <form @submit.prevent="assignBarber">
                    <!-- Name -->
                    <div class="mb-4">
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <!-- Bio -->
                    <div class="mb-4">
                        <InputLabel for="bio" value="Bio" />
                        <textarea
                            id="bio"
                            v-model="form.bio"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            rows="4"
                            required
                        ></textarea>
                        <InputError :message="form.errors.bio" class="mt-2" />
                    </div>

                    <!-- Years of Experience -->
                    <div class="mb-4">
                        <InputLabel for="years_of_experience" value="Years of Experience" />
                        <TextInput
                            id="years_of_experience"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.years_of_experience"
                            required
                            min="0"
                        />
                        <InputError :message="form.errors.years_of_experience" class="mt-2" />
                    </div>

                    <!-- Specialties -->
                    <div class="mb-4">
                        <InputLabel value="Specialties" />
                        <div class="mt-2 space-y-2">
                            <label v-for="specialty in availableSpecialties" :key="specialty" class="flex items-center">
                                <input
                                    type="checkbox"
                                    :value="specialty"
                                    v-model="form.specialties"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                >
                                <span class="ml-2">{{ specialty }}</span>
                            </label>
                        </div>
                        <InputError :message="form.errors.specialties" class="mt-2" />
                    </div>

                    <!-- Profile Photo -->
                    <div class="mb-4">
                        <InputLabel for="profile_photo" value="Profile Photo" />
                        <input
                            type="file"
                            @input="form.profile_photo = $event.target.files[0]"
                            accept="image/*"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.profile_photo" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="closeModal" class="mr-2">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            Assign
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    users: Object
});

const showingAssignBarberModal = ref(false);
const selectedUser = ref(null);

const availableSpecialties = [
    'Fades',
    'Modern Cuts',
    'Classic Cuts',
    'Beard Trimming',
    'Hot Towel Shave',
    'Hair Design',
    'Color Treatment',
    'Razor Fades',
    'Beard Sculpting',
    'Traditional Cuts'
];

const form = useForm({
    name: '',
    bio: '',
    years_of_experience: '',
    specialties: [],
    profile_photo: null,
});

const showAssignBarberModal = (user) => {
    selectedUser.value = user;
    form.name = user.name;
    showingAssignBarberModal.value = true;
};

const closeModal = () => {
    showingAssignBarberModal.value = false;
    selectedUser.value = null;
    form.reset();
    form.clearErrors();
};

const assignBarber = () => {
    if (selectedUser.value) {
        form.post(route('admin.users.assign-barber', selectedUser.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const toggleUserStatus = (user) => {
    if (confirm(`Are you sure you want to ${user.is_active ? 'deactivate' : 'activate'} this user?`)) {
        router.post(route('admin.users.toggle-status', user.id), {}, {
            preserveScroll: true
        });
    }
};

const removeBarber = (user) => {
    if (confirm('Are you sure you want to remove this user\'s barber role?')) {
        router.delete(route('admin.users.remove-barber', user.id), {
            preserveScroll: true
        });
    }
};

const deleteUser = (user) => {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        router.delete(route('admin.users.delete', user.id), {
            preserveScroll: true
        });
    }
};
</script>
