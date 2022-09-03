<script setup lang="ts">

import { trans } from 'laravel-vue-i18n'
import { Inertia } from '@inertiajs/inertia'
import { baseinfoData, UserRole } from '@/Interfaces/PaginatedData';
import { useQuasar } from 'quasar';
import { ref, computed, watch } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/inertia-vue3'

interface Props {
    data: {
        baseinfo: baseinfoData
    }
}

const props = defineProps<Props>()
const $q = useQuasar()

const imageLoad = ref(false) // Image processing indicator while deleting image
const deleteBtn = ref(false) // Button disable indicator while processing the action
const confirm = ref(false) // Confirmation of resetting form

const currentHeroimagePaths = ref<string>('')

if (typeof props.data.baseinfo.hero_image === 'string') {
    currentHeroimagePaths.value = props.data.baseinfo.hero_image.split(',')[0]
}

const form = useForm<baseinfoData>({
    _method: "put",
    id: props.data.baseinfo.id,
    title: props.data.baseinfo.title,
    meta_title: props.data.baseinfo.meta_title,
    content: props.data.baseinfo.content ?? '',
    hero_image: null,
    address: props.data.baseinfo.address,
    email: props.data.baseinfo.email,
    phone: props.data.baseinfo.phone,
    website: props.data.baseinfo.website
})

const actionInfo = () => {
    form.post('/admin/baseinfos/1', {
        onSuccess: () => onUpdateSuccess(),
        onError: (errors) => onUpdateFail(errors),
        onStart: () => deleteBtn.value = true,
        onFinish: () => deleteBtn.value = false
    })
}

const resetForm = () => {
    form.reset()
}

const validationErrors = (errors) => {
    return Object.entries(errors).map(item => item[1]).join(', ')
}

const onUpdateFail = (errors) => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while updating: ') + validationErrors(errors)
    })
}

const onUpdateSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The settings data has been updated successfully')
    })
    form.hero_image = null
}

const onRejected = (rejectedEntries) => {
    $q.notify({
        type: 'negative',
        message: rejectedEntries.length + trans(' file did not pass validation constraints')
    })
}

const deleteHeroImage = () => {
    Inertia.delete(`/admin/blog_image`, {
        onStart: () => {
            imageLoad.value = true,
            deleteBtn.value = true
        },
        onSuccess: () => onUpdateSuccess(),
        onError: (errors) => onUpdateFail(errors),
        onFinish: () => {
            imageLoad.value = false,
            deleteBtn.value = false
        },
        preserveScroll: true
    })
}

watch(() => props.data.baseinfo, () => {
    if (typeof props.data.baseinfo.hero_image === 'string') {
        currentHeroimagePaths.value = props.data.baseinfo.hero_image.split(',')[0]
    }
    if (!props.data.baseinfo.hero_image) {
        currentHeroimagePaths.value = ''
    }
})

</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="actionInfo">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    <div class="row justify-between items-center">
                        <div>
                            {{ $t('Blog Settings') }}
                        </div>
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="column q-col-gutter-md">
                        <div class="row q-col-gutter-x-md">
                            <div class="col q-col-gutter-y-md">
                                <div class="row q-col-gutter-md">
                                    <div class="col-4">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Blog title') }}</div>
                                                    <q-input v-model="form.title" dense data-test="title-input"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 40 || $t('Please use maximum characters: ') + '40']" ref="titleRef"
                                                    :error-message="form.errors.title"
                                                    :error="!!(form.errors.title)"
                                                    bottom-slots clearable clear-icon="close">
                                                        <template v-slot:prepend>
                                                            <q-icon name="edit" color="orange" />
                                                        </template>
                                                    </q-input>
                                                </div>
                                                <div class="col">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Blog meta title (SEO)') }}</div>
                                                    <q-input v-model="form.meta_title" dense data-test="meta-input"
                                                    ref="metaRef"
                                                    :error-message="form.errors.meta_title"
                                                    :error="!!(form.errors.meta_title)"
                                                    bottom-slots clearable clear-icon="close">
                                                        <template v-slot:prepend>
                                                            <q-icon name="ads_click" color="orange" />
                                                        </template>
                                                    </q-input>
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                    <div class="col-4">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Phone') }}</div>
                                                    <q-input v-model="form.phone" type="tel" dense data-test="phone-input"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 20 || $t('Please use maximum characters: ') + '20']" ref="phoneRef"
                                                    :error-message="form.errors.phone"
                                                    :error="!!(form.errors.phone)"
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
                                    <div class="col-4">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="col q-mb-md">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Address') }}</div>
                                                    <q-input v-model="form.address" dense data-test="title-input"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 100 || $t('Please use maximum characters: ') + '100']" ref="addressRef"
                                                    :error-message="form.errors.address"
                                                    :error="!!(form.errors.address)"
                                                    bottom-slots clearable clear-icon="close">
                                                        <template v-slot:prepend>
                                                            <q-icon name="business" color="orange" />
                                                        </template>
                                                    </q-input>
                                                </div>
                                                <div class="col">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                        {{ $t('Website') }}</div>
                                                    <q-input v-model="form.meta_title" dense data-test="web-input"
                                                    ref="webRef"
                                                    :rules="[val => !!val || $t('Field is required'),
                                                    val => val.length <= 100 || $t('Please use maximum characters: ') + '100']"
                                                    :error-message="form.errors.website"
                                                    :error="!!(form.errors.website)"
                                                    bottom-slots clearable clear-icon="close">
                                                        <template v-slot:prepend>
                                                            <q-icon name="language" color="orange" />
                                                        </template>
                                                    </q-input>
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </div>
                                <div class="q-col-gutter-md">
                                    <div class="col">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="form_header text-subtitle2 text-primary text-weight-regular">
                                                    {{ $t('Blog information') }}</div>
                                                <q-editor :placeholder="$t('Start creating here...')" v-model="form.content"
                                                    min-height="5rem" />
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </div>
                                <div class="q-col-gutter-md">
                                    <div class="col">
                                        <q-card flat bordered>
                                            <q-card-section>
                                                <div class="column">
                                                    <div class="text-subtitle2 text-primary text-weight-regular">
                                                    {{ $t('Pick new blog image') }}</div>
                                                    <q-file max-file-size="1024000" v-model="form.hero_image" counter max-files="1"
                                                        use-chips accept=".jpg, .png, image/*" @rejected="onRejected">
                                                        <template v-slot:prepend>
                                                            <q-icon color="orange" name="image" />
                                                        </template>
                                                        <template v-slot:hint>
                                                            {{ $t('Only image format file, max. size < 1 MB') }}
                                                        </template>
                                                    </q-file>
                                                    <template v-if="currentHeroimagePaths">
                                                        <div class="text-subtitle2 text-primary text-weight-regular q-mt-md" >
                                                            {{ $t('Current blog image') }}</div>
                                                        <div class="row">
                                                            <div class="col-3 relative-position">
                                                                <q-img fit="cover" height="200px" class="rounded-borders" :src="currentHeroimagePaths">
                                                                    <template v-slot:loading>
                                                                        <q-spinner-gears color="accent" />
                                                                    </template>
                                                                    <template v-slot:error>
                                                                        <div class="absolute-full flex flex-center bg-grey-3 text-accent">
                                                                            {{ $t('Cannot load image') }}
                                                                        </div>
                                                                    </template>
                                                                </q-img>
                                                                <q-btn :disable="deleteBtn" round icon="delete" color="accent" class="absolute-top-right" style="top: 5px; right: 5px" @click="deleteHeroImage" />
                                                                <q-spinner-gears size="4em" v-if="imageLoad" class="absolute-center z-max" color="accent"/>
                                                                <div v-if="imageLoad" class="light-dimmed transparent absolute-full bg-grey-3"></div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="text-subtitle2 text-primary text-weight-regular q-mt-md">
                                                            {{ $t('No blog image attached at this time') }}
                                                        </div>
                                                    </template>
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
                            <q-btn outline color="negative" @click="confirm = true" :disable="form.processing">{{ $t('Reset') }}</q-btn>
                        </div>
                        <div class="row q-col-gutter-md">
                            <div>
                                <Link as="div" :href="route('admin.index')">
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
    <q-dialog v-model="confirm" persistent>
        <q-card>
            <q-card-section class="row items-center bg-red-1">
                <q-avatar icon="front_hand" color="negative" text-color="white" />
                <span class="q-ml-sm">{{ $t('You can lose all of your data put in the form') }}</span>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat :label="$t('Cancel')" color="primary" v-close-popup />
                <q-btn flat :label="$t('Reset')" color="negative" v-close-popup @click="resetForm" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
