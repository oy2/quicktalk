<script setup>
import {ref} from 'vue';

const emit = defineEmits(['refresh'])

// references
let conversationID = ref(-1);
let conversation = ref({name: "Loading...", updated_at: "Loading...", users: []});
let text = "";
let messages = ref([]);
let userid = ref(document.querySelector("meta[name='user-id']").getAttribute('content'));
let isTyping = ref(false);

// expose functions to parent
defineExpose({
    setConversationID: (id) => {
        conversationID.value = id;
        fetchConversation();
        fetchMessages();
    },
    refresh: () => {
        fetchConversation();
        fetchMessages();
    }
})

/**
 * Emit a signal to parent to refresh the other UI components
 */
const signalRefresh = () => {
    emit('refresh');
}

/**
 * Fetch a conversation from the server and update the conversation reference
 * @returns {Promise<void>} void
 */
const fetchConversation = async () => {
    if (conversationID.value === -1) return;
    const response = await fetch('/conversation/' + conversationID.value, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
    let json = await response.json();
    conversation.value = json.conversation;
    signalRefresh();
}

/**
 * Fetch messages from the server and update the messages reference
 * @returns {Promise<void>} void
 */
const fetchMessages = async () => {
    if (conversationID.value === -1) return;
    // fetch messages
    const response = await fetch('/messages/' + conversationID.value, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });
    let json = await response.json();
    messages.value = json.messages;
    // reverse messages
    messages.value.reverse();
}

/**
 * Send a message to the server and trigger a UI update
 * @param message message to send
 * @returns {Promise<void>} void
 */
const sendMessage = async (message) => {
    if (!message || conversationID.value === -1) return;

    const response = await fetch('/message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            conversation_id: conversationID.value,
            message: message,
        })
    });
    const json = await response.json();
    await fetchMessages();
    signalRefresh();
}

/**
 * Handle sync chat message submission from input within UI
 * @param e event
 */
const handleSubmit = (e) => {
    // This method is sync to prevent multiple messages being sent
    e.preventDefault();
    e.target.value = "";
    sendMessage(text); // fires off async
    text = null;
}

/**
 * Return the name of a conversation based on the conversation object and number of users.
 * @param conversation conversation object
 * @returns {*|string} name of conversation
 */
const conversationName = (conversation) => {
    if(conversationID.value === -1 || !conversation.users) return "Please select a conversation from the side";
    // if conversation has only two users, return the name of the other users
    if (conversation.users.length === 2) {
        return "Chat with " + conversation.users.filter(user => user.id !== parseInt(userid.value))[0].name;
    }
    return conversation.name;
}

const isGroupDM = (conversation) => {
    return conversation.users.length > 2;
}



// Fetch initial data
await fetchConversation();
await fetchMessages();

</script>

<template>


    <div class="card ">
        <div class="card-header">
            {{ conversationName(conversation) }}
        </div>
        <div class="card-body"
             style="max-height: 50vh; overflow-y: scroll; display: flex; flex-direction: column-reverse">
            <!--    Send Button -->
            <!--                //<button type="button" class="btn btn-primary" @click="handleSubmit">Send</button>-->
            <!--    Chat box        -->
            <input v-model="text" type="text" v-on:keyup.enter="handleSubmit">
            <!--     For each message in messages  -->
            <div v-for="message in messages">

                <div v-if="message.user_id == userid">
                    <div class="card border-blue mb-1  ms-auto" style="max-width: 18rem">
                        <div class="card-body text-dark" >
                            <p class="card-text">{{ message.content }}</p>
                        </div>
                        <!-- Small space for Sent at date created_at -->
                        <div class="card-footer text-muted">
                            <small>Sent at: {{ message.created_at }}</small>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="card border-dark mb-1 " style="max-width: 18rem;">
                        <div class="card-body text-dark">
                            <p class="card-text">{{ message.content }}</p>
                        </div>
                        <!-- Small space for Sent at date created_at -->
                        <div class="card-footer text-muted">
                            <span v-if="isGroupDM(conversation)" class="">
                                {{ message.user.name }}
                        </span>
                            <small> at: {{ message.created_at }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            {{ conversationID.value === -1 ? "-" : "Users: " + conversation.users.map(user => user.name).join(", ") }}
        </div>
    </div>
</template>
