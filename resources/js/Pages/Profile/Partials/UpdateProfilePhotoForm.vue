<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Photo</h2>
            <p class="mt-1 text-sm text-gray-600">
                Update your profile photo.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <div class="flex items-center gap-x-3">
                    <img
                        v-if="form.photo"
                        :src="photoPreview"
                        class="h-12 w-12 rounded-full object-cover"
                        alt="Preview"
                    />
                    <img
                        v-else-if="user.profile_photo_url"
                        :src="user.profile_photo_url"
                        class="h-12 w-12 rounded-full object-cover"
                        alt="Current profile photo"
                    />
                    <div v-else class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500 text-lg">{{ user.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <input
                        type="file"
                        class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100"
                        @change="onFileChange"
                        accept="image/*"
                    />
                </div>
                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
const photoPreview = ref(null);

const form = useForm({
    photo: null,
});

function onFileChange(e) {
    const file = e.target.files[0];
    form.photo = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function submit() {
    form.post(route('profile.photo.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('photo');
            photoPreview.value = null;
        },
    });
}
</script>
