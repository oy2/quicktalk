<script setup>
import {ref} from "vue";

const emit = defineEmits(['set-convo'])

// references
let conversations = ref([]);
let users = [];
let selected = {};
let selectedUsers = [];
let groupName = "";
let userid = ref(document.querySelector("meta[name='user-id']").getAttribute('content'));
let currentConvo = ref(-1);

// expose functions to parent
defineExpose({
    refresh: () => {
        fetchConversations();
        fetchUsers();
    }
})

/**
 * Emit a signal to parent with a given conversation ID
 * @param id conversation ID
 */
const setConvo = (id) => {
    emit('set-convo', id)
    currentConvo.value = id;
}

/**
 * Fetch conversations from server and update conversations reference
 * @returns {Promise<void>} void
 */
const fetchConversations = async () => {
    const response = await fetch('/conversations');
    let json = await response.json();
    conversations.value = json.conversations; // throw out status
}

/**
 * Fetch users from server and update users reference
 * @returns {Promise<void>} void
 */
const fetchUsers = async () => {
    const response = await fetch('/users');
    let json = await response.json();
    users = json.users; // throw out status
}

/**
 * Create a new conversation with a given user ID.
 * After creating the conversation then conversations are reacquired from the server.
 * @param userId
 * @returns {Promise<void>}
 */
const createConversation = async (userId) => {
    if (!userId) return;
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
    await fetchConversations();
}

const createGroupConversation = async (userIds, name) => {
    if (!userIds || !name) return;

    const response = await fetch('/groupconversation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            additionalUsers: userIds,
            conversationName: name
        })
    });
    const json = await response.json();
    await fetchConversations();
}

/**
 * Get the name of a conversation. UI Utility function.
 * If the conversation has only two users, then the name of the other user is returned.
 * Otherwise, the name of the conversation is returned.
 * @param conversation conversation object
 * @returns {string} name of conversation
 */
const conversationName = (conversation) => {
    if (!conversation.users) return "";
    // if conversation has only two users, return the name of the other users
    if (conversation.users.length === 2) {
        return conversation.users.filter(user => user.id !== parseInt(userid.value))[0].name;
    }
    return conversation.name;
}

const isGroupDM = (conversation) => {
    return conversation.users.length > 2;
}

// Fetch initial data from the server for the first time
await fetchUsers();
await fetchConversations();

</script>

<template>
    <!-- Sidebar conversations -->
    <div class="card border-dark mb-3"
         style="max-width: 18rem; max-height: 59vh; overflow-y: scroll;">
        <div class="card-header">Conversations:</div>
        <br>
        <div class="text-dark" v-for="conversation in conversations">
            <a href="#" @click="setConvo(conversation.id)">
                <div class="card border-dark mb-3" style="max-width: 18rem;">
                    <div class="card-header">
                        {{ conversationName(conversation) }}
                        <span v-if="conversation.unread" class="badge bg-primary rounded-pill">
                            Unread
                        </span>
                        <span v-if="isGroupDM(conversation)" class="badge bg-secondary rounded-pill">
                            Group
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Button trigger modal -->
    Create a new...
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-primary btn-lg btn-block" data-bs-toggle="modal"
                data-bs-target="#staticBackdrop">
            Direct
        </button>
        <button type="button" class="btn btn-secondary btn-lg btn-block" data-bs-toggle="modal"
                data-bs-target="#groupModal">
            Group
        </button>
    </div>

    <!-- Modal for creating a group -->
    <div class="modal fade" id="groupModal" data-bs-backdrop="static"
         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Select fellow users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label>Select the users you want in the group. <br><small>Hold control to multi-select</small></label>
                    <select v-model="selectedUsers" class="form-select" multiple aria-label="multiple select example">
                        <option v-for="user in users" v-bind:value="{id: user.id}">{{
                                user.name
                            }}
                        </option>
                    </select>
                    <br>
                    <label>Group Name</label>
                    <input type="text" v-model="groupName" class="form-control" placeholder="Group Name">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                            @click="createGroupConversation(selectedUsers, groupName)">
                        Create Group
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for creating a direct message -->
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
