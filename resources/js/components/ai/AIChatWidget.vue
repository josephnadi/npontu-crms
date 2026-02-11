<script setup lang="ts">
import { ref, nextTick, watch } from 'vue';
import axios from 'axios'; // Import axios

interface ChatMessage {
  id: number;
  text: string;
  sender: 'user' | 'ai';
}

const props = defineProps<{
  modelValue: boolean; // Controls visibility from parent
}>();

const emit = defineEmits(['update:modelValue']);

const messages = ref<ChatMessage[]>([]);
const newMessage = ref('');
let messageId = 0;

const chatMessagesContainer = ref<HTMLElement | null>(null);

// Watch for changes in modelValue to control visibility
const isChatOpen = ref(props.modelValue);
watch(() => props.modelValue, (newValue) => {
  isChatOpen.value = newValue;
  if (newValue) {
    scrollToBottom();
  }
});

const scrollToBottom = () => {
  nextTick(() => {
    if (chatMessagesContainer.value) {
      chatMessagesContainer.value.scrollTop = chatMessagesContainer.value.scrollHeight;
    }
  });
};

const sendMessage = async () => {
  if (newMessage.value.trim()) {
    const userMessageText = newMessage.value;
    messages.value.push({
      id: messageId++,
      text: userMessageText,
      sender: 'user',
    });
    newMessage.value = '';
    scrollToBottom();

    try {
      const response = await axios.post('/ai/chat', { message: userMessageText });
      messages.value.push({
        id: messageId++,
        text: response.data.reply,
        sender: 'ai',
      });
      scrollToBottom();
    } catch (error) {
      console.error('Error sending message to AI:', error);
      messages.value.push({
        id: messageId++,
        text: "Sorry, I'm having trouble connecting to the AI right now. Please try again later.",
        sender: 'ai',
      });
      scrollToBottom();
    }
  }
};

// No longer needs a toggleChat function as parent controls visibility
</script>

<template>
  <transition name="slide-fade">
    <v-card class="ai-chat-card">
      <v-toolbar color="primary" density="compact">
        <v-toolbar-title>AI Assistant</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click="emit('update:modelValue', false)">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>

      <v-card-text class="chat-messages" ref="chatMessagesContainer">
        <div
          v-for="message in messages"
          :key="message.id"
          :class="['message-bubble', message.sender]"
        >
          {{ message.text }}
        </div>
      </v-card-text>

      <v-card-actions class="chat-input-area">
        <v-text-field
          v-model="newMessage"
          label="Type your message..."
          variant="outlined"
          hide-details
          density="compact"
          @keydown.enter="sendMessage"
          clearable
        ></v-text-field>
        <v-btn color="primary" icon @click="sendMessage" :disabled="!newMessage.trim()">
          <v-icon>mdi-send</v-icon>
        </v-btn>
      </v-card-actions>
    </v-card>
  </transition>
</template>

<style scoped>
.ai-chat-card {
  max-height: 500px; /* Adjust as needed */
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  border-radius: 0; /* No rounded corners for sidebar integration */
  box-shadow: none; /* No shadow for sidebar integration */
  margin-top: 16px; /* Add some space below the button */
  margin-bottom: 16px; /* Add some space above the navigation */
}

.chat-messages {
  flex-grow: 1;
  overflow-y: auto;
  padding: 16px;
  background-color: #f5f5f5; /* Light background for messages */
}

.message-bubble {
  max-width: 70%;
  padding: 8px 12px;
  border-radius: 15px;
  margin-bottom: 8px;
  word-wrap: break-word;
}

.message-bubble.user {
  background-color: #e0f7fa; /* Light blue for user messages */
  margin-left: auto;
  text-align: right;
}

.message-bubble.ai {
  background-color: #e8eaf6; /* Light indigo for AI messages */
  margin-right: auto;
  text-align: left;
}

.chat-input-area {
  padding: 8px 16px;
  display: flex;
  align-items: center;
  border-top: 1px solid #eee;
}

/* Vue Transition Styles */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease-out;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>