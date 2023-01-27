<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {Suspense} from 'vue';
// import Sidebar
import Sidebar from './Chat/Sidebar.vue';
import ChatArea from "./Chat/ChatArea.vue";

import {ref} from 'vue';

const chatArea = ref(null);

function setTargetConversationID(id) {
    chatArea.value.setConversationID(id);
}


// emits

</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="container">
                        <div class="row">
                            <div class="col-2">
                                <Suspense>
                                    <Sidebar @set-convo="setTargetConversationID" />
                                    <template #fallback>
                                        <div>Loading...</div>
                                    </template>
                                </Suspense>
                            </div>
                            <div class="col-8">
                                <Suspense>
                                    <ChatArea ref="chatArea" />
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
