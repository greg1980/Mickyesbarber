<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdateProfilePhotoForm from './Partials/UpdateProfilePhotoForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile">
      <meta name="description" content="Edit your profile, update your password, and manage your account at Mickyes Coiffure." />
      <meta property="og:title" content="Edit Profile - Mickyes Coiffure" />
      <meta property="og:description" content="Manage your account settings, update your profile photo, and change your password at Mickyes Coiffure." />
      <meta property="og:type" content="website" />
      <meta property="og:url" content="https://mickyes.com/profile/edit" />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content="Edit Profile - Mickyes Coiffure" />
      <meta name="twitter:description" content="Edit your profile and manage your account at Mickyes Coiffure." />
    </Head>

    <SidebarLayout>
        <div class="p-6">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
                <p class="text-gray-600 mt-2">Manage your account settings and preferences</p>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 shadow rounded-lg">
                    <UpdateProfilePhotoForm />
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>
