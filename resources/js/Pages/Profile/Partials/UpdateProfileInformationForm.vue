<script setup>
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Button from '@/components/Button.vue';
import TextInput from '@/components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Alert from '@/components/Alert.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const photoPreview = ref(null);
const photoInput = ref(null);

const form = useForm({
    name: user.name,
    email: user.email,
    profile_photo: null,
    mobile_number: user.mobile_number || '',
    _method: 'PATCH'
});

const selectNewPhoto = () => {
    try {
        if (photoInput.value) {
            photoInput.value.click();
        }
    } catch (error) {
        console.error('Error selecting photo:', error);
    }
};

const updatePhotoPreview = (e) => {
    try {
        const file = e.target.files[0];
        if (!file) return;

        form.profile_photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } catch (error) {
        console.error('Error updating photo preview:', error);
    }
};

const submitForm = () => {
    if (form.profile_photo || form.name !== user.name || form.email !== user.email) {
        form.post(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('profile_photo');
                photoPreview.value = null;
            },
            forceFormData: true
        });
    }
};
</script>

<template>
    <section class="max-w-xl">
        <!-- Success Alert -->
        <Alert
            v-if="$page.props.flash.success"
            type="success"
            class="mb-6"
        >
            {{ $page.props.flash.success }}
        </Alert>

        <!-- Error Alert -->
        <Alert
            v-if="$page.props.flash.error"
            type="danger"
            class="mb-6"
        >
            {{ $page.props.flash.error }}
        </Alert>

        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="submitForm" class="mt-6 space-y-6" enctype="multipart/form-data">
            <div>
                <InputLabel value="Profile Photo" />

                <div class="mt-2 flex items-center space-x-6">
                    <div class="relative h-20 w-20 rounded-full overflow-hidden">
                        <img v-if="photoPreview" :src="photoPreview" class="h-full w-full object-cover" />
                        <img v-else-if="user.profile_photo" :src="user.profile_photo" class="h-full w-full object-cover" />
                        <div v-else class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-600 text-xl font-medium uppercase">
                            {{ user.name.charAt(0) }}
                        </div>
                    </div>

                    <div>
                        <input
                            type="file"
                            ref="photoInput"
                            class="hidden"
                            @change="updatePhotoPreview"
                            accept="image/*"
                        >
                        <Button
                            type="button"
                            variant="primary"
                            @click="selectNewPhoto"
                        >
                            Select New Photo
                        </Button>
                    </div>
                </div>
                <InputError :message="form.errors.profile_photo" class="mt-2" />
            </div>

            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="mobile_number" value="Mobile Number" />

                <TextInput
                    id="mobile_number"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.mobile_number"
                    placeholder="Enter your mobile number"
                    autocomplete="tel"
                />

                <p class="mt-1 text-sm text-gray-500">Format: +XX XXXX XXXX or national format</p>
                <InputError class="mt-2" :message="form.errors.mobile_number" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Button
                        variant="link"
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="text-sm"
                    >
                        Click here to re-send the verification email.
                    </Button>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button
                    type="submit"
                    variant="primary"
                    :disabled="form.processing || (!form.profile_photo && form.name === user.name && form.email === user.email)"
                    :processing="form.processing"
                >
                    Save
                </Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
