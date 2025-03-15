<template>
    <Head title="Register as Barber" />
    <UserLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Register as a Barber
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header Section -->
                        <div class="mb-8">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Register as a Barber</h1>
                            <p class="text-gray-600">Join our team of professional barbers and start building your client base today. Fill out the form below to get started.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" required />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full max-w-md"
                                    v-model="form.name"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="profile_photo" value="Profile Photo" required />
                                <input
                                    type="file"
                                    id="profile_photo"
                                    @input="form.profile_photo = $event.target.files[0]"
                                    class="mt-1 block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-none file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-500 file:text-white
                                    hover:file:bg-blue-600
                                    file:transition-colors file:duration-200"
                                    accept="image/*"
                                    required
                                />
                                <InputError :message="form.errors.profile_photo" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="bio" value="Bio" required />
                                <textarea
                                    id="bio"
                                    v-model="form.bio"
                                    class="mt-1 block w-full max-w-md rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="4"
                                    required
                                ></textarea>
                                <InputError :message="form.errors.bio" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="years_of_experience" value="Years of Experience" required />
                                <TextInput
                                    id="years_of_experience"
                                    type="number"
                                    class="mt-1 block w-full max-w-[160px]"
                                    v-model="form.years_of_experience"
                                    required
                                />
                                <InputError :message="form.errors.years_of_experience" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel value="Specialties" required />
                                <div class="mt-2 space-y-2">
                                    <label class="inline-flex items-center mb-3">
                                        <input
                                            type="checkbox"
                                            value="Fades"
                                            v-model="form.specialties"
                                            class="rounded border-gray-400 text-gray-800 shadow-sm focus:ring-gray-800"
                                        />
                                        <span class="ms-2 me-4">Fades</span>
                                    </label>
                                    <label class="inline-flex items-center mb-3">
                                        <input
                                            type="checkbox"
                                            value="Modern Cuts"
                                            v-model="form.specialties"
                                            class="rounded border-gray-400 text-gray-800 shadow-sm focus:ring-gray-800"
                                        />
                                        <span class="ms-2 me-4">Modern Cuts</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            value="Classic Cuts"
                                            v-model="form.specialties"
                                            class="rounded border-gray-400 text-gray-800 shadow-sm focus:ring-gray-800"
                                        />
                                        <span class="ms-2">Classic Cuts</span>
                                    </label>
                                </div>
                                <InputError :message="form.errors.specialties" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Register
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';
import Button from '@/components/Button.vue';

const form = useForm({
    name: '',
    profile_photo: null,
    bio: '',
    years_of_experience: '',
    specialties: [],
});

const submit = () => {
    form.post(route('barber.register'));
};
</script>
