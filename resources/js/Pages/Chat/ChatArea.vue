<script setup>
import {ref} from 'vue';

let conversationID = ref(-1);

defineExpose({
    setConversationID: (id) => {
        conversationID.value = id;
         fetchConversation();
         fetchMessages();
    }
})

let conversation = ref(null);

let messages = ref([]);

let userid = document.querySelector("meta[name='user-id']").getAttribute('content');

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
}

await fetchConversation();
await fetchMessages();

</script>

<template>


        <div class="card ">
            <div class="card-header">
                {{ conversationID===-1 ? "Please select a conversation from the side" : conversation.name }}
            </div>
            <div class="card-body"
                 style="max-height: 50vh; overflow-y: scroll; display: flex; flex-direction: column-reverse">
                <!--    Send Button -->
                <button type="button" class="btn btn-primary">Send</button>
                <!--    Chat box        -->
                <input type="text">
                <!--     For each message in messages  -->
                <div v-for="message in messages">

                    <div v-if="message.user_id === userid">
                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                            <div class="card-body text-dark">
                                <p class="card-text">{{ message.content }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                            <div class="card-body text-dark">
                                <p class="card-text">{{ message.content }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer text-muted">
                Last Message: {{ conversationID===-1 ? "-" : conversation.updated_at }}
            </div>
        </div>
</template>
