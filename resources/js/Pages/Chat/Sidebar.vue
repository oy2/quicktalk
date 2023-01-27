<script setup>
let conversations = [];
let users = [];
let selected = {};

const fetchConversations = async () => {
    const response = await fetch('/conversations');
    let json = await response.json();
    console.log(json);
    conversations = json.conversations; // throw out status
}

const fetchUsers = async () => {
    const response = await fetch('/users');
    let json = await response.json();
    users = json.users; // throw out status
}

// handle new conversation
const createConversation = async (userId) => {
    if(!userId) return;
    const response = await fetch('/conversation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            receiver_id: userId
        })
    });
    const json = await response.json();
    conversations.push(json.conversation);
}


await fetchUsers();

await fetchConversations();

</script>

<template>
    <div class="card border-dark mb-3"
         style="max-width: 18rem; max-height: 59vh; overflow-y: scroll;">
        <div class="card-header">Conversations:</div>

        <div class="card-body text-dark" v-for="conversation in conversations">
            <p class="card-text">{{ conversation.name }}</p>
        </div>

    </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#staticBackdrop">
        New Conversation
    </button>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static"
         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Select a fellow
                        user</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select v-model="selected" class="form-select" aria-label="Default select example">
                        <option v-for="user in users" v-bind:value="{id: user.id}">{{
                                user.name
                            }}
                        </option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                            @click="createConversation(selected.id)">
                        Create Conversation
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>
