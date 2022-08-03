<template>
    <q-layout view="lHh Lpr lFf" class="bg-white">
        <q-header elevated>
            <q-toolbar>
                <div class="row flex-center" style="width: 100%">
                    <div class="col-auto row items-center">
                        <q-btn flat dense round @click="toggleLeftDrawer" aria-label="Menu" icon="menu" class="q-mr-sm" />
                        <q-separator vertical color="grey-5" />
                        <q-toolbar-title shrink>{{ $t('Dashboard') }}</q-toolbar-title>
                    </div>
                    <div class="col row justify-center">
                        <q-input dark dense standout v-model="text" input-class="text-right" style="width: 300px; max-width: 100%">
                            <template #append>
                                <q-icon v-if="text === ''" name="search" />
                                <q-icon v-else name="clear" class="cursor-pointer" @click="text = ''" />
                            </template>
                        </q-input>
                    </div>
                    <div class="col-auto row justify-end">
                        <q-btn-group class="q-mr-md" outline>
                            <q-btn round @click="loadLanguageAsync('de')" data-test="DE">
                                <q-avatar>
                                    <q-img src="/images/germany.png" />
                                </q-avatar>
                            </q-btn>
                            <q-btn round @click="loadLanguageAsync('pl')" data-test="PL">
                                <q-avatar>
                                    <q-img src="/images/poland.png" />
                                </q-avatar>
                            </q-btn>
                            <q-btn round @click="loadLanguageAsync('en')" data-test="EN">
                                <q-avatar>
                                    <q-img src="/images/great-britain.png" />
                                </q-avatar>
                            </q-btn>
                        </q-btn-group>
                        <q-btn color="green" icon="account_circle">
                            <q-menu anchor="bottom right" self="top right" :offset="[0, 10]">
                                <q-item clickable>
                                    <q-item-section>Account Settings</q-item-section>
                                </q-item>
                                <q-item clickable>
                                    <q-item-section>Logout</q-item-section>
                                </q-item>
                            </q-menu>
                        </q-btn>
                    </div>
                </div>
            </q-toolbar>
        </q-header>

        <q-drawer v-model="leftDrawerOpen" show-if-above class="bg-grey-2">
            <q-img img-class="my-custom-image" src="/images/parallax2.jpg" />
            <q-list>
                <nav-link :href="route('admin.index')" :active="usePage().component.value === 'Dashboard/Index'" name="code">
                    {{ $t('Dashboard') }}
                </nav-link>
                <nav-link :href="route('admin.posts.index')" :active="usePage().component.value === 'Post/Index'" name="code">
                    {{ $t('Posts') }}
                </nav-link>
                <nav-link :href="route('admin.index')" :active="usePage().component.value === 'Category/Index'" name="code">
                    {{ $t('Categories') }}
                </nav-link>
                <nav-link :href="route('admin.index')" :active="usePage().component.value === 'Tag/Index'" name="code">
                    {{ $t('Tags') }}
                </nav-link>
                <nav-link :href="route('admin.index')" :active="usePage().component.value === 'User/Index'" name="code">
                    {{ $t('Users') }}
                </nav-link>
            </q-list>
        </q-drawer>
        <q-page-container>
            <q-page padding>
                <slot></slot>
            </q-page>
        </q-page-container>
    </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { usePage } from '@inertiajs/inertia-vue3';
import NavLink from '@/Shared/NavLink.vue'

const leftDrawerOpen = ref(false)
const text = ref('')

const toggleLeftDrawer = () => leftDrawerOpen.value = !leftDrawerOpen.value

</script>

<style lang="sass">
.my-custom-image
  filter: sepia()
</style>
