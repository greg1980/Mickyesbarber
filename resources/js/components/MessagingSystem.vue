<template>
    <div class="flex h-[600px] bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Conversations List -->
        <div class="w-1/3 border-r">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Messages</h2>
            </div>
            <div class="overflow-y-auto h-[calc(600px-4rem)]">
                <div v-for="conversation in conversations"
                     :key="conversation.user.id"
                     @click="selectConversation(conversation)"
                     class="p-4 border-b hover:bg-gray-50 cursor-pointer"
                     :class="{ 'bg-blue-50': selectedConversation?.user.id === conversation.user.id }">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium">{{ conversation.user.name }}</div>
                            <div class="text-sm text-gray-500 truncate">
                                {{ conversation.last_message.content }}
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="text-xs text-gray-500">
                                {{ conversation.last_message.time }}
                            </div>
                            <div v-if="conversation.unread_count > 0"
                                 class="mt-1 bg-blue-500 text-white text-xs rounded-full px-2 py-0.5">
                                {{ conversation.unread_count }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col">
            <div v-if="selectedConversation" class="flex-1 flex flex-col">
                <!-- Chat Header -->
                <div class="p-4 border-b">
                    <div class="font-medium">{{ selectedConversation.user.name }}</div>
                    <div class="text-sm text-gray-500">
                        {{ selectedConversation.user.isBarber ? 'Barber' : 'Client' }}
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 overflow-y-auto p-4" ref="messagesContainer">
                    <div v-for="message in messages"
                         :key="message.id"
                         class="mb-4 flex"
                         :class="{ 'justify-end': message.sender_id === userId }">
                        <div class="max-w-[70%] rounded-lg px-4 py-2"
                             :class="message.sender_id === userId ? 'bg-blue-500 text-white' : 'bg-gray-100'">
                            <div class="text-sm">{{ message.content }}</div>
                            <div class="text-xs mt-1"
                                 :class="message.sender_id === userId ? 'text-blue-100' : 'text-gray-500'">
                                {{ formatTime(message.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="p-4 border-t">
                    <form @submit.prevent="sendMessage" class="flex space-x-2">
                        <input v-model="newMessage"
                               type="text"
                               class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Type your message..."
                               :disabled="isSending">
                        <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 disabled:opacity-50"
                                :disabled="isSending || !newMessage.trim()">
                            Send
                        </button>
                    </form>
                </div>
            </div>

            <div v-else class="flex-1 flex items-center justify-center text-gray-500">
                Select a conversation to start messaging
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue';
import { format } from 'date-fns';

const props = defineProps({
    userId: {
        type: Number,
        required: true
    }
});

const conversations = ref([]);
const selectedConversation = ref(null);
const messages = ref([]);
const newMessage = ref('');
const isSending = ref(false);
const messagesContainer = ref(null);

const fetchConversations = async () => {
    try {
        const response = await axios.get('/api/messages/conversations');
        conversations.value = response.data;
    } catch (error) {
        console.error('Error fetching conversations:', error);
    }
};

const selectConversation = async (conversation) => {
    selectedConversation.value = conversation;
    await fetchMessages(conversation.user.id);
};

const fetchMessages = async (userId) => {
    try {
        const response = await axios.get(`/api/messages/${userId}`);
        messages.value = response.data.data;
        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || isSending.value) return;

    isSending.value = true;
    try {
        const response = await axios.post(`/api/messages/${selectedConversation.value.user.id}`, {
            content: newMessage.value
        });

        messages.value.unshift(response.data);
        newMessage.value = '';
        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error('Error sending message:', error);
    } finally {
        isSending.value = false;
    }
};

const formatTime = (timestamp) => {
    return format(new Date(timestamp), 'p');
};

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

// Listen for new messages
window.Echo.private(`user.${props.userId}`)
    .listen('.message.sent', (e) => {
        if (selectedConversation.value?.user.id === e.message.sender_id) {
            messages.value.unshift(e.message);
            nextTick(() => scrollToBottom());
        }
        fetchConversations();
    });

watch(selectedConversation, () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = 0;
    }
});

onMounted(() => {
    fetchConversations();
});
</script>
