<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {Suspense} from 'vue';
// import Sidebar
import Sidebar from './Chat/Sidebar.vue';

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
                                    <Sidebar />
                                    <template #fallback>
                                        <div>Loading...</div>
                                    </template>
                                </Suspense>


                            </div>
                            <div class="col-8">
                                <div class="card ">
                                    <div class="card-header">
                                        John Smith
                                    </div>
                                    <div class="card-body"
                                         style="max-height: 50vh; overflow-y: scroll; display: flex; flex-direction: column-reverse">
                                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Hello, how are you doing?</p>
                                            </div>
                                        </div>

                                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Pretty good, thanks!</p>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">This is a fake conversation</p>
                                            </div>
                                        </div>

                                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Where I am demoing that scrolling works!</p>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Wow how exciting lol</p>
                                            </div>
                                        </div>

                                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Pretty good, thanks!</p>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Do do do</p>
                                            </div>
                                        </div>

                                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">This is going to be a bit of a longer message or
                                                    something.</p>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Hello, how are you doing?</p>
                                            </div>
                                        </div>

                                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Pretty good, thanks!</p>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Hello, how are you doing?</p>
                                            </div>
                                        </div>

                                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Pretty good, thanks!</p>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Hello, how are you doing?</p>
                                            </div>
                                        </div>

                                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Pretty good, thanks!</p>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-3" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Hello, how are you doing?</p>
                                            </div>
                                        </div>

                                        <div class="card border-blue mb-3  ms-auto" style="max-width: 18rem;">
                                            <div class="card-body text-dark">
                                                <p class="card-text">Pretty good, thanks!</p>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="card-footer text-muted">
                                        2 days ago
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </AuthenticatedLayout>

</template>
