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
const $q = useQuasar()
const modeCreate = ref(true)
const confirm = ref(false)

const fNameRef = ref(null)
const lNameRef = ref(null)
const emailRef = ref(null)
const mobileRef = ref(null)
const isPassword = ref(true)

let form = useForm<userData>({
    id: null,
    email: '',
    first_name: '',
    last_name: '',
    mobile: '',
    password: '',
    password_confirmation: ''
})

if (props.data.user) {
    modeCreate.value = false

    form = useForm<userData>({
        id: props.data.user.id,
        email: props.data.user.email,
        first_name: props.data.user.first_name,
        last_name: props.data.user.last_name,
        mobile: props.data.user.mobile,
    })
}

const actionUser = () => {

    if (modeCreate.value) {
        form.post('/admin/users', {
        onSuccess: () => onStoreSuccess(),
        onError: () => onStoreFail()
        })
        return
    }

    form.put(`/admin/users/${form.id}`, {
        onSuccess: () => onUpdateSuccess(),
        onError: () => onUpdateFail(),
    })
}

const onStoreSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The user has been stored successfully')
    })
}

const onStoreFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while storing user')
    })
}

const onUpdateSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The user has been updated successfully')
    })
}

const onUpdateFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while updating user')
    })
}

const resetForm = () => {
    form.reset()
    fNameRef.value.resetValidation()
    lNameRef.value.resetValidation()
    emailRef.value.resetValidation()
    mobileRef.value.resetValidation()
}

</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="actionUser">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    <div class="row justify-between items-center">
                        <div>
                            {{ modeCreate ? $t('Create User') : $t('Edit User') }}
                        </div>
                        <div v-if="!modeCreate" class="row q-gutter-x-sm">
                            <q-badge outline
                                :class="[(props.data.user.roles === 'member') ? 'text-primary' : 'text-accent']"
                                :label="props.data.user.roles" />
                            <q-badge outline
                                :class="[(props.data.user.status === 'pending') ? 'text-red' :
                                (props.data.user.status === 'enabled') ? 'text-primary' :
                                'text-grey']"
                                :label="props.data.user.status" />
                        </div>
                        <div v-else>
                            <q-badge outline class="text-accent" label="admin" />
                        </div>
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="column q-col-gutter-md">
                        <div class="row q-col-gutter-x-md">
                            <div class="col">
                                <div class="row q-col-gutter-md">
                                    <div :class="[modeCreate ? 'col-4': 'col']">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-thin">
                                                        {{ $t('First name') }}</div>
                                                    <q-input v-model="form.first_name" dense data-test="first_name-input"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 40 || $t('Please use maximum characters: ') + '40']" ref="fNameRef"
                                                    :error-message="form.errors.first_name"
                                                    :error="!!(form.errors.first_name)"
                                                    bottom-slots clearable clear-icon="close">
                                                        <template v-slot:prepend>
                                                            <q-icon name="edit" color="orange" />
                                                        </template>
                                                    </q-input>
                                                </div>
                                                <div class="col">
                                                    <div class="text-subtitle2 text-primary text-weight-thin">
                                                        {{ $t('Last name') }}</div>
                                                    <q-input v-model="form.last_name" dense data-test="last_name-input"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 40 || $t('Please use maximum characters: ') + '40']" ref="lNameRef"
                                                    :error-message="form.errors.last_name"
                                                    :error="!!(form.errors.last_name)"
                                                    bottom-slots clearable clear-icon="close">
                                                        <template v-slot:prepend>
                                                            <q-icon name="edit" color="orange" />
                                                        </template>
                                                    </q-input>
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                    <div :class="[modeCreate ? 'col-4': 'col']">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-thin">
                                                        {{ $t('Mobile') }}</div>
                                                    <q-input v-model="form.mobile" type="tel" dense data-test="mobile-input"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 20 || $t('Please use maximum characters: ') + '20']" ref="mobileRef"
                                                    :error-message="form.errors.mobile"
                                                    :error="!!(form.errors.mobile)"
                                                    bottom-slots clearable clear-icon="close">
                                                        <template v-slot:prepend>
                                                            <q-icon name="phone_iphone" color="orange" />
                                                        </template>
                                                    </q-input>
                                                </div>
                                                <div class="col">
                                                    <div class="text-subtitle2 text-primary text-weight-thin">
                                                        {{ $t('Email') }}</div>
                                                    <q-input v-model="form.email" type="email" dense data-test="email-input"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 100 || $t('Please use maximum characters: ') + '100']" ref="emailRef"
                                                    :error-message="form.errors.email"
                                                    :error="!!(form.errors.email)"
                                                    bottom-slots clearable clear-icon="close">
                                                        <template v-slot:prepend>
                                                            <q-icon name="email" color="orange" />
                                                        </template>
                                                    </q-input>
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                    <div v-if="modeCreate" class="col-4">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-thin">
                                                        {{ $t('Password') }}</div>
                                                    <q-input v-model="form.password" dense clearable clear-icon="close"
                                                        :type="isPassword ? 'password' : 'text'" bottom-slots
                                                        :error-message="form.errors.password"
                                                        :error="!!(form.errors.password)"
                                                        :disable="form.processing">
                                                        <template v-slot:prepend>
                                                            <q-icon name="key" color="orange" />
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
                                                    <div class="text-subtitle2 text-primary text-weight-thin">
                                                        {{ $t('Password confirmation') }}</div>
                                                    <q-input v-model="form.password_confirmation" dense clearable clear-icon="close"
                                                        :type="isPassword ? 'password' : 'text'" bottom-slots
                                                        :error-message="form.errors.password_confirmation"
                                                        :error="!!(form.errors.password_confirmation)"
                                                        :disable="form.processing">
                                                        <template v-slot:prepend>
                                                            <q-icon name="key" color="orange" />
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
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row justify-between items-center">
                        <div>
                            <q-btn outline color="negative" @click="confirm = true" :disable="form.processing">{{
                            $t('Reset') }}</q-btn>
                        </div>
                        <div class="row q-col-gutter-md">
                            <div>
                                <Link as="div" href="/admin/users">
                                <q-btn outline color="secondary" :disable="form.processing">{{ $t('Back') }}</q-btn>
                                </Link>
                            </div>
                            <div>
                                <q-btn type="submit" outline color="primary" :loading="form.processing" data-test="submission-button">{{ modeCreate ?
                                $t('Create') : $t('Update') }}
                                    <template v-slot:loading>
                                        <q-spinner-hourglass />
                                    </template>
                                </q-btn>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </q-form>
    </div>
    <q-dialog v-model="confirm" persistent>
        <q-card>
            <q-card-section class="row items-center bg-red-1">
                <q-avatar icon="front_hand" color="negative" text-color="white" />
                <span class="q-ml-sm" data-test="reset-text">{{ $t('You can lose all of your data put in the form') }}</span>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat :label="$t('Cancel')" color="primary" v-close-popup />
                <q-btn flat :label="$t('Reset')" color="negative" v-close-popup @click="resetForm"/>
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
