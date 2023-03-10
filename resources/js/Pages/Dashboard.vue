<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {Suspense} from 'vue';
import {ref} from 'vue';

// Child components
import Sidebar from './Chat/Sidebar.vue';
import ChatArea from "./Chat/ChatArea.vue";

// References to children
const chatArea = ref(null);
const sidebar = ref(null);

const userID = ref(null);

let querySelector = document.querySelector("meta[name='user-id']");

if(!querySelector){
    console.log("No user ID found. Reloading...")
    // force reload
    window.location.reload();
}

userID.value = querySelector.getAttribute('content');



// Functions to interact necessary signals to child components
/**
 * Sets the target conversation ID for the chat area. Uses exposed function from child component.
 * @param id conversation ID
 */
function setTargetConversationID(id) {
    chatArea.value.setConversationID(id);
}

function signalChatAreaRefresh() {
    chatArea.value.refresh();
}

/**
 * Signals the sidebar to refresh. Uses exposed function from child component.
 */
function signalSidebarRefresh() {
    sidebar.value.refresh();
}

/**
 * Main new event listener for the dashboard for real-time refresh.
 */
Echo.private('new-message-notification.' + userID.value)
    .listen('NewMessageNotificationEvent', (e) => {
        signalSidebarRefresh();
        signalChatAreaRefresh();
    });

</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="container">
                    <div class="row">
                        <div class="col-2">
                            <!-- Sidebar Component -- Suspense because async data calls -->
                            <Suspense>
                                <Sidebar ref="sidebar" @set-convo="setTargetConversationID"/>
                                <template #fallback>
                                    <div>Loading...</div>
                                </template>
                            </Suspense>
                        </div>
                        <div class="col-10">
                            <!-- ChatArea Component -- Suspense because async data calls -->
                            <Suspense>
                                <ChatArea ref="chatArea" @refresh="signalSidebarRefresh"/>
                                <template #fallback>
                                    <div>Loading...</div>
                                </template>
                            </Suspense>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>

</template>
