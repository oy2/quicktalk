<script setup>
import {ref} from 'vue';

let conversationID = ref(-1);
let conversation = ref({name: "Loading...", updated_at: "Loading..."});
let text = "";
let messages = ref([]);
let userid = ref(document.querySelector("meta[name='user-id']").getAttribute('content'));

defineExpose({
    setConversationID: (id) => {
        conversationID.value = id;
        fetchConversation();
        fetchMessages();
    }
})

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
}

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
}

const handleSubmit = (e) => {
    // This method is sync to prevent multiple messages being sent
    e.preventDefault();
    e.target.value = "";
    sendMessage(text); // fires off async
    text = null;
}

const conversationName = (conversation) => {
    if(conversationID.value === -1 || !conversation.users) return "Please select a conversation from the side";
    // if conversation has only two users, return the name of the other users
    if (conversation.users.length === 2) {
        return "Chat with " + conversation.users.filter(user => user.id !== userid)[0].name;
    }
    return conversation.name;
}

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
                    <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                        <div class="card-body text-dark">
                            <p class="card-text">{{ message.content }}</p>
                        </div>
                        <!-- Small space for Sent at date created_at -->
                        <div class="card-footer text-muted">
                            <small>Sent at: {{ message.created_at }}</small>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="card border-dark mb-3 " style="max-width: 18rem;">
                        <div class="card-body text-dark">
                            <p class="card-text">{{ message.content }}</p>

                        </div>
                        <!-- Small space for Sent at date created_at -->
                        <div class="card-footer text-muted">
                            <small>Sent at: {{ message.created_at }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            Last Message: {{ conversationID.value === -1 ? "-" : conversation.updated_at }}
        </div>
    </div>
</template>
