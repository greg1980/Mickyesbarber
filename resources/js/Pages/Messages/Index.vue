<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const conversations = ref([]);
const selectedUser = ref(null);
const messages = ref([]);
const newMessage = ref('');
const loading = ref(true);

const fetchConversations = async () => {
    try {
        const response = await axios.get(route('messages.conversations'));
        conversations.value = response.data.conversations;
    } catch (error) {
        console.error('Error fetching conversations:', error);
    } finally {
        loading.value = false;
    }
};

const selectConversation = async (user) => {
    selectedUser.value = user;
    loading.value = true;
    try {
        const response = await axios.get(route('messages.get', user.id));
        messages.value = response.data.messages;
    } catch (error) {
        console.error('Error fetching messages:', error);
    } finally {
        loading.value = false;
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedUser.value) return;

    try {
        const response = await axios.post(route('messages.send', selectedUser.value.id), {
            content: newMessage.value
        });
        messages.value.push(response.data.message);
        newMessage.value = '';
    } catch (error) {
        console.error('Error sending message:', error);
    }
};

onMounted(() => {
    fetchConversations();
});
</script>

<template>
    <Head title="Messages" />

    <UserLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex h-[600px]">
                        <!-- Conversations List -->
                        <div class="w-1/3 border-r border-gray-200 overflow-y-auto">
                            <div class="p-4">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Conversations</h2>
                                <div v-if="loading" class="text-center py-4">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 mx-auto"></div>
                                </div>
                                <div v-else-if="conversations.length === 0" class="text-center py-4 text-gray-500">
                                    No conversations yet
                                </div>
                                <div v-else class="space-y-2">
                                    <button
                                        v-for="user in conversations"
                                        :key="user.id"
                                        @click="selectConversation(user)"
                                        class="w-full p-3 flex items-center space-x-3 hover:bg-gray-50 rounded-lg transition-colors duration-150"
                                        :class="{ 'bg-gray-50': selectedUser?.id === user.id }"
                                    >
                                        <div class="flex-shrink-0">
                                            <img
                                                v-if="user.profile_photo"
                                                :src="user.profile_photo"
                                                class="h-10 w-10 rounded-full"
                                                alt=""
                                            >
                                            <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 text-sm">{{ user.name.charAt(0) }}</span>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex justify-between">
                                                <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                                <span v-if="user.unread_count" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ user.unread_count }}
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="w-2/3 flex flex-col">
                            <div v-if="!selectedUser" class="flex-1 flex items-center justify-center text-gray-500">
                                Select a conversation to start messaging
                            </div>
                            <template v-else>
                                <!-- Messages Header -->
                                <div class="p-4 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <img
                                                v-if="selectedUser.profile_photo"
                                                :src="selectedUser.profile_photo"
                                                class="h-10 w-10 rounded-full"
                                                alt=""
                                            >
                                            <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 text-sm">{{ selectedUser.name.charAt(0) }}</span>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ selectedUser.name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Messages List -->
                                <div class="flex-1 p-4 overflow-y-auto">
                                    <div v-if="loading" class="flex items-center justify-center h-full">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                                    </div>
                                    <div v-else class="space-y-4">
                                        <div
                                            v-for="message in messages"
                                            :key="message.id"
                                            class="flex"
                                            :class="{ 'justify-end': message.sender_id === $page.props.auth.user.id }"
                                        >
                                            <div
                                                class="inline-block rounded-lg px-4 py-2 max-w-md"
                                                :class="[
                                                    message.sender_id === $page.props.auth.user.id
                                                        ? 'bg-blue-500 text-white'
                                                        : 'bg-gray-100 text-gray-900'
                                                ]"
                                            >
                                                <p class="text-sm">{{ message.content }}</p>
                                                <p class="text-xs mt-1 opacity-75">
                                                    {{ new Date(message.created_at).toLocaleTimeString() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Message Input -->
                                <div class="p-4 border-t border-gray-200">
                                    <form @submit.prevent="sendMessage" class="flex space-x-2">
                                        <input
                                            v-model="newMessage"
                                            type="text"
                                            placeholder="Type a message..."
                                            class="flex-1 rounded-full border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        >
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition"
                                            :disabled="!newMessage.trim()"
                                        >
                                            Send
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
