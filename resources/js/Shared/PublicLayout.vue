<script setup lang="ts">

import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3';
import { UserStatus } from '@/Interfaces/PaginatedData';

const login = () => Inertia.get('/login')
const logout = () => Inertia.post('/logout')
const register = () => Inertia.get('/register')

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
    id: number,
    status: UserStatus,
    role: Array<string>
}

const props = defineProps<AuthProps>()

</script>

<template>
    <q-layout view="hHh lpR fFf">

        <q-header reveal elevated class="bg-primary text-white" height-hint="98">
            <q-toolbar>
                <q-toolbar-title>
                    <q-avatar>

                    </q-avatar>
                    Blog Application
                </q-toolbar-title>
                <q-space />
                <div v-if="!props.auth.user">
                    <q-btn flat @click="login">Sign In</q-btn>
                    <q-btn flat @click="register">Sign Up</q-btn>
                </div>
                <div v-else>
                    <q-btn flat @click="logout">Logout</q-btn>
                </div>
            </q-toolbar>
        </q-header>

        <q-page-container>
            <slot></slot>
        </q-page-container>

        <q-footer elevated class="bg-grey-8 text-white">
            <q-toolbar>
                <q-toolbar-title>
                    <div>Title</div>
                </q-toolbar-title>
            </q-toolbar>
        </q-footer>

    </q-layout>
</template>
