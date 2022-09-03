<script setup lang="ts">


import { trans } from 'laravel-vue-i18n'
import { Inertia } from '@inertiajs/inertia'
import { userData, UserRole } from '@/Interfaces/PaginatedData';
import { useQuasar } from 'quasar';
import { ref, computed } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/inertia-vue3'


interface Props {
    data: {
        user?: userData
    }
}

type pwdData = {
    current_password: string,
    password: string,
    password_confirmation: string
}

type avatarData = {
    _method: string,
    avatar?: File | null,
    id: number
}

const props = defineProps<Props>()
const $q = useQuasar()

const imageLoad = ref(false) // Image processing indicator while deleting image
const deleteBtn = ref(false) // Button disable indicator while processing the action
const expandedPassword = ref(false)
const expandedAvatar = ref(false)
// const hasAvatarImage = ref(false)
// let currentAvatarPath = ref<string>('')

const userRole = usePage().props.value.auth.user.role
const authUser = ref(userRole === UserRole.SuperAdmin ? 'admin' : userRole === UserRole.Admin ? 'admin' : userRole === UserRole.Author ? 'author' : 'member')


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

const formPwd = useForm<pwdData>({
    current_password: null,
    password: null,
    password_confirmation: null
})

const formAvatar = useForm<avatarData>({
    _method: "put",
    avatar: null,
    id: props.data.user.id
})

const actionAccount = () => form.put('/user/profile-information', {
    onSuccess: () => onUpdateProfileSuccess(),
    onError: () => onUpdateProfileFail()
})
const isPassword = ref(true)
const symbols = computed(() => form.first_name[0] + form.last_name[0])

const updatePassword = () => formPwd.put('/user/password', {
        onSuccess: () => onUpdatePasswordSuccess(),
        onError: () => onUpdatePasswordFail(),
    })

const updateAvatar = () => formAvatar.post(`/${authUser.value}/avatar`, {
        onSuccess: () => onUpdateAvatarSuccess(),
        onError: () => onUpdateAvatarFail()
})

const removeAvatar = () => Inertia.post(`/${authUser.value}/avatar`, { 'id': props.data.user.id }, {
    onStart: () => {
        imageLoad.value = true,
        deleteBtn.value = true
    },
    onSuccess: () => onDeleteAvatarSuccess(),
    onError: () => onDeleteAvatarFail(),
    onFinish: () => {
        imageLoad.value = false,
        deleteBtn.value = false
    },
    preserveScroll: true
    })

const onUpdatePasswordSuccess = () => {
    formPwd.current_password = null,
    formPwd.password = null,
    formPwd.password_confirmation = null
    expandedPassword.value = false
    $q.notify({
        type: 'positive',
        message: trans('The password has been updated successfully')
    })
}

const onUpdatePasswordFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while updating password')
    })
}

const onUpdateAvatarSuccess = () => {
    formAvatar.avatar = null,
    expandedAvatar.value = false,
    $q.notify({
        type: 'positive',
        message: trans('The avatar has been updated successfully')
    })
}

const onUpdateAvatarFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while updating avatar')
    })
}

const onDeleteAvatarSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The avatar has been removed successfully')
    })
}
const onDeleteAvatarFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while removing avatar')
    })
}
const onUpdateProfileSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The profile has been updated successfully')
    })
}

const onUpdateProfileFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while updating profile')
    })
}


const onRejected = (rejectedEntries) => {
    $q.notify({
        type: 'negative',
        message: rejectedEntries.length + trans(' file(s) did not pass validation constraints')
    })
}

const currentPasswordError = computed(() => !!(formPwd.errors.updatePassword) && !!(formPwd.errors.updatePassword.current_password))
const passwordError = computed(() => !!(formPwd.errors.updatePassword) && !!(formPwd.errors.updatePassword.password))

const redirectBack = () => { window.history.back() }

</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="actionAccount">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    {{ $t('Account information') }}
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
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
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
                                    <div class="col-6">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
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
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Email') }}</div>
                                                    <q-input v-model="form.email" type="email" dense data-test="email-input"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 40 || $t('Please use maximum characters: ') + '40']" ref="emailRef"
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
                                </div>
                                <div class="row q-col-gutter-md">
                                    <div class="col">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Short introduction info') }}</div>
                                                    <q-editor :placeholder="$t('Drop some lines...')" v-model="form.intro"
                                                        min-height="5rem" />
                                                </div>
                                                <div class="col">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Profile description') }}</div>
                                                    <q-editor min-height="8rem" :placeholder="$t('Drop some lines...')"
                                                        v-model="form.profile" />
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
                                                <div v-if="hasAvatarImage" class="absolute-top-right">
                                                    <q-btn round icon="delete" color="accent" @click="removeAvatar">
                                                        <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                                            {{ $t('Remove avatar') }}
                                                        </q-tooltip>
                                                    </q-btn>
                                                </div>
                                                <q-spinner-gears size="4em" v-if="imageLoad" class="absolute-center z-max" color="accent"/>
                                                <div v-if="imageLoad" class="light-dimmed transparent absolute-full bg-grey-3"></div>
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
                                                    <q-btn size="sm" color="red" label="Verify Email" class="q-mr-sm"></q-btn>
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
                                <div class="col-auto">
                                    <q-list bordered class="rounded-borders">
                                        <q-expansion-item
                                            v-model="expandedPassword"
                                            expand-separator
                                            icon="lock_reset"
                                            :label="$t('Password update')"
                                            header-class="text-primary"
                                            data-test="expanded-list">
                                            <q-card>
                                                <q-card-section>
                                                    <div class="column">
                                                        <div>
                                                            <q-input v-model="formPwd.current_password" dense clearable clear-icon="close" :label="$t('Current password')"
                                                                :type="isPassword ? 'password' : 'text'" bottom-slots
                                                                :error-message="currentPasswordError ? formPwd.errors.updatePassword.current_password: null"
                                                                :error="currentPasswordError"
                                                                :disable="formPwd.processing">
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
                                                        <div>
                                                            <q-input v-model="formPwd.password" dense clearable clear-icon="close" :label="$t('New password')"
                                                                :type="isPassword ? 'password' : 'text'" bottom-slots
                                                                :error-message="passwordError ? formPwd.errors.updatePassword.password: null"
                                                                :error="passwordError"
                                                                :disable="formPwd.processing">
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
                                                        <div>
                                                            <q-input v-model="formPwd.password_confirmation" dense clearable clear-icon="close" :label="$t('Confirm new password')"
                                                                :type="isPassword ? 'password' : 'text'"
                                                                :error-message="formPwd.errors.password_confirmation"
                                                                :error="!!(formPwd.errors.password_confirmation)"
                                                                :disable="formPwd.processing">
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
                                                        <q-btn @click="updatePassword" flat color="primary" :loading="formPwd.processing">{{ $t('Update') }}</q-btn>
                                                    </div>
                                                </q-card-section>
                                            </q-card>
                                        </q-expansion-item>
                                    </q-list>
                                </div>
                                <div class="col-auto">
                                    <q-list bordered class="rounded-borders">
                                        <q-expansion-item
                                            v-model="expandedAvatar"
                                            expand-separator
                                            icon="perm_identity"
                                            :label="$t('Avatar update')"
                                            header-class="text-primary"
                                            data-test="expanded-list">
                                            <q-card>
                                                <q-card-section>
                                                    <div class="column">
                                                        <q-file dense  max-file-size="1024000" v-model="formAvatar.avatar" counter max-files="1"
                                                            accept=".jpg, .png, image/*" @rejected="onRejected">
                                                            <template v-slot:prepend>
                                                                <q-icon color="orange" name="image" />
                                                            </template>
                                                            <template v-slot:hint>
                                                                {{ $t('Only image format file, max. size < 1 MB') }}
                                                            </template>
                                                        </q-file>
                                                        <q-btn class="q-mt-md" @click="updateAvatar" flat color="primary" :loading="formAvatar.processing">{{ $t('Update') }}</q-btn>
                                                    </div>
                                                </q-card-section>
                                            </q-card>
                                        </q-expansion-item>
                                    </q-list>
                                </div>
                            </div>
                        </div>
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row justify-end items-center">
                        <div class="row q-col-gutter-md">
                            <div>
                                <Link as="div" @click="redirectBack">
                                    <q-btn outline color="secondary" :disable="form.processing">{{ $t('Back') }}</q-btn>
                                </Link>
                            </div>
                            <div>
                                <q-btn type="submit" outline color="primary" :loading="form.processing">{{ $t('Update') }}
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
</template>
