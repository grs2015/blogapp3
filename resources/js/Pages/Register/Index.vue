<script lang="ts">
export default {
    layout: Layout
}
</script>

<script setup lang="ts">

import Layout from '@/Shared/RegisterLayout.vue'
import { useForm, Link } from '@inertiajs/inertia-vue3';
import { UserRole } from '@/Interfaces/PaginatedData'
import { ref } from 'vue'

const isPassword = ref(true)
const form = useForm({
    first_name: null,
    last_name: null,
    email: null,
    password: null,
    password_confirmation: null,
    role: UserRole.Author
})

const registerUser = () => form.post('/register')

</script>

<template>
    <div class="row flex-center fit window-height">
        <q-form @submit.prevent="registerUser" style="width: 30%">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    Register Form
                </q-card-section>
                <q-card-section>
                    <div class="column q-col-gutter-y-md">
                        <div class="col">
                            <q-input v-model="form.first_name" dense clearable clear-icon="close" outlined label="Your first name"
                                type="text" bottom-slots
                                :error-message="form.errors.first_name"
                                :error="!!(form.errors.first_name)">
                                <template v-slot:prepend>
                                    <q-icon name="person" color="orange" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col">
                            <q-input v-model="form.last_name" dense clearable clear-icon="close" outlined label="Your last name"
                                type="text" bottom-slots
                                :error-message="form.errors.last_name"
                                :error="!!(form.errors.last_name)">
                                <template v-slot:prepend>
                                    <q-icon name="person" color="orange" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col">
                            <q-input v-model="form.email" dense clearable clear-icon="close" outlined label="Your email"
                                type="email" bottom-slots
                                :error-message="form.errors.email"
                                :error="!!(form.errors.email)">
                                <template v-slot:prepend>
                                    <q-icon name="email" color="orange" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col">
                            <q-input v-model="form.password" dense clearable clear-icon="close" outlined label="Your password"
                                :type="isPassword ? 'password' : 'text'" bottom-slots
                                :error-message="form.errors.password"
                                :error="!!(form.errors.password)">
                                <template v-slot:prepend>
                                    <q-icon name="lock" color="orange" />
                                </template>
                                <template v-slot:append>
                                    <q-icon
                                        :name="isPassword ? 'visibility_off' : 'visibility'"
                                        class="cursor-pointer"
                                        @click="isPassword = !isPassword"
                                    />
                                </template>
                            </q-input>
                        </div>
                        <div class="col">
                            <q-input v-model="form.password_confirmation" dense clearable clear-icon="close" outlined label="Confirm your password"
                                :type="isPassword ? 'password' : 'text'" bottom-slots
                                :error-message="form.errors.password_confirmation"
                                :error="!!(form.errors.password_confirmation)">
                                <template v-slot:prepend>
                                    <q-icon name="lock" color="orange" />
                                </template>
                                <template v-slot:append>
                                    <q-icon
                                        :name="isPassword ? 'visibility_off' : 'visibility'"
                                        class="cursor-pointer"
                                        @click="isPassword = !isPassword"
                                    />
                                </template>
                            </q-input>
                        </div>
                        <div class="col">
                            <q-radio v-model="form.role" val="author" label="Author" />
                            <q-radio v-model="form.role" val="member" label="Commentor" />
                        </div>
                    </div>
                </q-card-section>
                <q-card-actions align="right" class="q-gutter-sm">
                    <Link as="div" href="/">
                        <q-btn flat color="secondary" :disable="form.processing">Cancel</q-btn>
                    </Link>
                    <q-btn flat color="primary" type="submit" :loading="form.processing">Register</q-btn>
                </q-card-actions>
            </q-card>
        </q-form>
    </div>
</template>
