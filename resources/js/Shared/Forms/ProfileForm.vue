<script setup lang="ts">


import { trans } from 'laravel-vue-i18n'
import { Inertia } from '@inertiajs/inertia'
import { userData } from '@/Interfaces/PaginatedData';
import { useQuasar } from 'quasar';
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/inertia-vue3'


interface Props {
    data: {
        user?: userData
    }
}

const props = defineProps<Props>()


const form = useForm<userData>({
    id: props.data.user.id,
    email: props.data.user.email,
    first_name: props.data.user.first_name,
    last_name: props.data.user.last_name,
    intro: props.data.user.intro,
    last_login: props.data.user.last_login,
    mobile: props.data.user.mobile,
    posts_count: props.data.user.posts_count,
    profile: props.data.user.profile,
    registered_at: props.data.user.registered_at,
    roles: props.data.user.roles,
    status: props.data.user.status,
    email_verified_at: props.data.user.email_verified_at,
    avatar: props.data.user.avatar
})

const currentAvatarPath = computed(() => props.data.user.avatar)
const hasAvatarImage = computed(() => !!(props.data.user.avatar))
const symbols = computed(() => form.first_name[0] + form.last_name[0])

</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="actionAccount">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    {{ $t('User profile information') }}
                </q-card-section>
                <q-card-section>
                    <div class="column q-col-gutter-md">
                        <div class="row q-col-gutter-x-md">
                            <div class="col-8 q-col-gutter-y-md">
                                <div class="row q-col-gutter-md">
                                    <div class="col-6">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('First name') }}</div>
                                                    <q-input disable v-model="form.first_name" dense data-test="first_name-input"
                                                        input-class="bg-grey-3" filled >
                                                    </q-input>
                                                </div>
                                                <div class="col">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Last name') }}</div>
                                                    <q-input disable v-model="form.last_name" dense data-test="last_name-input"
                                                        input-class="bg-grey-3" filled >
                                                    </q-input>
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                    <div class="col-6">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Mobile') }}</div>
                                                    <q-input disable v-model="form.mobile" type="tel" dense data-test="mobile-input"
                                                        input-class="bg-grey-3" filled >
                                                    </q-input>
                                                </div>
                                                <div class="col">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Email') }}</div>
                                                    <q-input disable v-model="form.email" type="email" dense data-test="email-input"
                                                        input-class="bg-grey-3" filled >
                                                    </q-input>
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </div>
                                <div class="row q-col-gutter-md">
                                    <div class="col">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Short introduction info') }}</div>
                                                    <q-editor disable :placeholder="$t('Drop some lines...')" v-model="form.intro"
                                                        min-height="5rem" content-class="bg-grey-4" />
                                                </div>
                                                <div class="col">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Profile description') }}</div>
                                                    <q-editor disable min-height="8rem" :placeholder="$t('Drop some lines...')"
                                                        v-model="form.profile" content-class="bg-grey-4" />
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 column q-col-gutter-y-md ">
                                <div class="col-auto">
                                    <q-card flat bordered>
                                        <q-card-section class="column">
                                            <div class="text-subtitle2 text-primary text-weight-regular">
                                            {{ $t('Current avatar') }}</div>
                                            <q-separator spaced />
                                            <div class="row flex-center relative-position">
                                                <div v-if="!hasAvatarImage">
                                                    <q-avatar size="100px" color="orange">{{ symbols }}</q-avatar>
                                                </div>
                                                <div v-else>
                                                    <q-avatar size="100px">
                                                        <q-img height="100px" :src="currentAvatarPath" />
                                                    </q-avatar>
                                                </div>

                                            </div>
                                            <div class="row q-mt-md justify-between items-center">
                                                <div class="text-subtitle2 text-primary text-weight-regular">
                                                    {{ $t('User registered at:') }}
                                                </div>
                                                <div class="text-right">
                                                    {{ form.registered_at }}
                                                </div>
                                            </div>
                                            <q-separator spaced />
                                            <div class="row justify-between items-center">
                                                <div class="text-subtitle2 text-primary text-weight-regular">
                                                    {{ $t('User last logged-in:') }}
                                                </div>
                                                <div class="text-right">
                                                    {{ form.last_login }}
                                                </div>
                                            </div>
                                            <q-separator spaced />
                                            <div class="row justify-between items-center">
                                                <div class="text-subtitle2 text-primary text-weight-regular">
                                                    {{ $t('Email status:') }}
                                                </div>
                                                <div v-if="form.email_verified_at" class="row items-center">
                                                    <q-icon size="1.5rem" name="verified" color="green" class="q-mr-xs"/>
                                                    <div class="text-green">{{ $t('Verified') }}</div>
                                                </div>
                                                <div v-else class="row items-center">

                                                    <q-icon size="1.5rem" name="error_outline" color="red" class="q-mr-xs"/>
                                                    <div class="text-red">{{ $t('Not Verified') }}</div>
                                                </div>
                                            </div>
                                            <q-separator spaced />
                                            <div class="row justify-between items-center">
                                                <div class="text-subtitle2 text-primary text-weight-regular">
                                                    {{ $t('User role:') }}
                                                </div>
                                                <div class="text-right">
                                                    {{ form.roles }}
                                                </div>
                                            </div>
                                            <q-separator spaced />

                                            <div class="row justify-between items-center">
                                                <div class="text-subtitle2 text-primary text-weight-regular">
                                                    {{ $t('User status:') }}
                                                </div>
                                                <div class="text-right" :class="[form.status === 'enabled' ? 'text-green' : 'text-red']">
                                                    {{ form.status }}
                                                </div>
                                            </div>
                                            <q-separator spaced />
                                            <div class="text-subtitle2 text-primary text-weight-regular">
                                            {{ $t('User posts') }}</div>
                                            <div class="row flex-center text-h2 text-grey">
                                                {{ form.posts_count }}
                                            </div>
                                        </q-card-section>
                                    </q-card>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row justify-end items-center">
                        <div class="row q-col-gutter-md">
                            <div>
                                <Link as="div" href="/admin/users">
                                    <q-btn outline color="secondary">{{ $t('Back') }}</q-btn>
                                </Link>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </q-form>
    </div>
</template>

