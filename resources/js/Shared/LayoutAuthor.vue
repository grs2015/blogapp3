<script setup lang="ts">
import { ref, computed } from 'vue'
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { usePage } from '@inertiajs/inertia-vue3';
import NavLink from '@/Shared/NavLink.vue'
import { Inertia } from '@inertiajs/inertia';
import { UserStatus } from '@/Interfaces/PaginatedData';

interface AuthProps {
    auth : {
        user: authUser
    }
}

interface authUser {
    email: string,
    first_name: string,
    last_name?: string,
    full_name?: string,
    avatar?: string,
    id: number,
    status: UserStatus,
    role?: string | Array<string> | null
}

const props = defineProps<AuthProps>()
const status = ref(usePage().props.value.auth.user.status)

const leftDrawerOpen = ref(false)
const text = ref('')

const toggleLeftDrawer = () => leftDrawerOpen.value = !leftDrawerOpen.value

const userLogout = () => { Inertia.post('/logout') }
const userProfile = () => { Inertia.get(`/author/users/${props.auth.user.id}/edit`) }

const currentAvatarPath = computed(() => props.auth.user.avatar)
const hasAvatarImage = computed(() => !!(props.auth.user.avatar))

const symbols = computed(() => props.auth.user.first_name[0] + props.auth.user.last_name[0])

</script>

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
                            <q-menu anchor="bottom right" self="top right" :offset="[0, 10]" auto-close>
                                <q-item clickable :disable="status === 'disabled'">
                                    <q-item-section @click="userProfile">{{ $t('Account Settings') }}</q-item-section>
                                </q-item>
                                <q-item clickable>
                                    <q-item-section @click="userLogout">{{ $t('Logout') }}</q-item-section>
                                </q-item>
                            </q-menu>
                        </q-btn>
                    </div>
                </div>
            </q-toolbar>
        </q-header>

        <q-drawer v-model="leftDrawerOpen" show-if-above class="bg-grey-2">
            <q-img img-class="my-custom-image relative-position" src="/images/parallax2.jpg">
                <q-item class="full-width">
                    <template v-if="!hasAvatarImage">
                        <q-item-section avatar>
                            <q-avatar size="50px" color="green" text-color="white">{{ symbols }}</q-avatar>
                        </q-item-section>
                    </template>
                    <template v-else>
                        <q-item-section avatar>
                            <q-avatar size="50px">
                                <q-img height="50px" :src="currentAvatarPath" />
                            </q-avatar>
                        </q-item-section>
                    </template>
                    <!-- <q-item-section avatar>
                        <q-avatar color="green" text-color="white" icon="bluetooth" />
                    </q-item-section> -->
                    <q-item-section>
                        <q-item-label class="text-white text-subtitle1">{{ $t('Hi')}}, {{ props.auth.user.full_name }}</q-item-label>
                        <q-item-label caption class="text-white">{{ $t('Your role: ')}} {{ props.auth.user.role }}</q-item-label>
                        <q-item-label caption class="text-white">{{ $t('Your status: ')}} {{ props.auth.user.status }}</q-item-label>
                        <q-item-label caption class="text-white">Email: {{ props.auth.user.email }}</q-item-label>
                    </q-item-section>
                </q-item>
            </q-img>
            <q-separator color="green" />
            <q-list>
                <nav-link :href="route('author.index')" :active="usePage().component.value.startsWith('Dashboard')" name="code">
                    {{ $t('Dashboard') }}
                </nav-link>
                <nav-link :href="route('author.posts.index')" :active="usePage().component.value.startsWith('Post')" name="code" :disabled="status === 'disabled'">
                    {{ $t('Posts') }}
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

<style lang="sass">

.my-custom-image
    filter: sepia()

</style>
