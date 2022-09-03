<script setup lang="ts">

import { Inertia } from '@inertiajs/inertia';
import { ref, computed, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { categoryData, Paginated, tagData, UserStatus } from '@/Interfaces/PaginatedData';
import { scroll } from 'quasar'
import PublicFilters from './Filters/Public/PublicFilters.vue';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { useQuasar } from 'quasar'
import { trans } from 'laravel-vue-i18n';

const { getScrollTarget } = scroll
const { getVerticalScrollPosition, setVerticalScrollPosition } = scroll

const login = () => Inertia.get('/login')
const logout = () => Inertia.post('/logout')
const register = () => Inertia.get('/register')

interface AuthProps {
    auth : {
        user: authUser
    },
    model: {
        posts: Paginated,
        categories: Array<categoryData>,
        tags: Array<tagData>
    }
}

interface authUser {
    email: string,
    first_name: string,
    last_name?: string,
    full_name?: string,
    id: number,
    status: UserStatus,
    role: Array<string>
}

const leftDrawerOpen = ref(false)
const rightDrawerOpen = ref(false)
const $q = useQuasar()


const msg = computed(() => usePage().props.value.flash.status)
    if (msg.value) {
        $q.notify({
        type: 'negative',
        message: trans(msg.value)
    })
}

const props = defineProps<AuthProps>()
const layout = ref(null)
const btnVisibility = ref(false)

const scrollHandler = (val) => {
    btnVisibility.value = val.position >= 700 ? true : false
    }

const toTop = () => {
    const el = getScrollTarget(layout.value.$el)
    const duration = 400
    setVerticalScrollPosition(el, 10, duration)
}



</script>

<template>
    <!-- <q-layout view="hHh lpR fFf" @scroll="scrollHandler" ref="layout" class="scroll"> -->
    <!-- <q-layout view="hHh lpR fFf" @scroll="scrollHandler" ref="layout"> -->
    <q-layout view="hHh LpR fFf" @scroll="scrollHandler" ref="layout">

        <q-header reveal elevated class="text-white" height-hint="98" style="opacity: 0.9; background-color: #0000005a; backdrop-filter: blur(10px);">
            <q-toolbar>
                <q-toolbar-title>
                    <q-avatar>

                    </q-avatar>
                    {{ $t('Power Analysis Blog') }}
                </q-toolbar-title>
                <q-space />
                <div class="row items-center">
                    <div v-if="props.auth.user && usePage().props.value.can.see_credentials" class="q-mr-md">
                        {{ $t('Hi')}}, <span class="text-primary">{{ props.auth.user.full_name }}!</span> /
                        {{ $t('Role') }}: <span class="text-negative">{{ props.auth.user.role }}</span> /
                        Status: <span class="text-negative">{{ props.auth.user.status }}</span>
                    </div>
                    <div v-if="!props.auth.user">
                        <q-btn flat @click="login">Sign In</q-btn>
                        <q-btn flat @click="register">Sign Up</q-btn>
                    </div>
                    <div v-else>
                        <q-btn flat @click="logout">Logout</q-btn>
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
                    </div>
                </div>
            </q-toolbar>
        </q-header>

        <q-drawer show-if-above v-model="leftDrawerOpen" side="left" >
            <div class="full-width">
                <PublicFilters :data="{cats: props.model.categories, tags: props.model.tags }" />
            </div>

        </q-drawer>

        <q-drawer show-if-above v-model="rightDrawerOpen" side="right" >

        </q-drawer>

        <q-page-container>
            <div class="q-px-xl">
                <slot></slot>
            </div>
        </q-page-container>

        <q-page-sticky v-if="btnVisibility" @click="toTop" position="bottom-right" :offset="[20, 20]">
            <transition
                appear
                enter-active-class="animated fadeIn"
                leave-active-class="animated fadeOut">
                <q-btn round color="teal" icon="expand_less" glossy />
            </transition>
        </q-page-sticky>


        <!-- <q-footer elevated class="bg-grey-8 text-white">
            <q-toolbar>
                <q-toolbar-title>
                    <div>Title</div>
                </q-toolbar-title>
            </q-toolbar>
        </q-footer> -->

    </q-layout>
</template>

<style scoped lang="sass">

.position
    right: 20%
    bottom: 20px
</style>
