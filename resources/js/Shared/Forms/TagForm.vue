<script setup lang="ts">

import { tagData } from '@/Interfaces/PaginatedData'
import { trans } from 'laravel-vue-i18n';
import { useQuasar } from 'quasar'
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/inertia-vue3'
import { emit } from 'process';

interface Props {
    data: {
        tag?: tagData | null
    }
}

const props = defineProps<Props>()

const $q = useQuasar()
const modeCreate = ref(true)
const confirm = ref(false)
const tagTitleRef = ref(null)

let form = useForm<tagData>({
    id: null,
    title: '',
    content: '',
    meta_title: '',
    slug: ''
})

if (props.data.tag) {
    modeCreate.value = false

    form = useForm<tagData>({
        id: props.data.tag.id,
        title: props.data.tag.title,
        meta_title: props.data.tag.meta_title,
        content: props.data.tag.content ?? '', // To supress validation error on the input field (null value)
        slug: props.data.tag.slug
    })
}

const actionTag = () => {

    if (modeCreate.value) {
        form.post('/admin/tags', {
        onSuccess: () => onStoreSuccess(),
        onError: () => onStoreFail()
        })
        return
    }

    form.put(`/admin/tags/${form.slug}`, {
        onSuccess: () => onUpdateSuccess(),
        onError: () => onUpdateFail(),
    })
}

const onStoreSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The tag have been stored successfully')
    })
}

const onStoreFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while storing tag')
    })
}

const onUpdateSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The tag have been updated successfully')
    })
}

const onUpdateFail = () => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while updating')
    })
}

const resetForm = () => {
    form.reset()
    tagTitleRef.value.resetValidation()
}


</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="actionTag">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    {{ modeCreate ? $t('Create Tag') : $t('Edit Tag') }}
                </q-card-section>
                <q-card-section>
                    <div class="column q-col-gutter-md">
                        <div class="row q-col-gutter-md">
                            <div class="col-6">
                                <q-card flat bordered>
                                    <q-card-section>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Tag Title') }}</div>
                                            <q-input v-model="form.title" dense data-test="title-input"
                                                :rules="[val => !!val || 'Field is required',
                                                val => val.length <= 30 || 'Please use maximum 30 characters']" ref="tagTitleRef"
                                                :error-message="form.errors.title"
                                                :error="form.errors.title && form.errors.title.length > 0">
                                                <template v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Tag Description') }}</div>
                                            <q-input v-model="form.content" dense data-test="description-input"
                                                :rules="[val => val.length <= 50 || 'Please use maximum 50 characters']">
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
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Tag Meta Description (SEO)') }}</div>
                                            <q-input v-model="form.meta_title" dense data-test="meta-input">
                                                <template v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                    </q-card-section>
                                </q-card>
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
                                <Link as="div" href="/admin/tags">
                                <q-btn outline color="secondary" :disable="form.processing">{{ $t('Cancel') }}</q-btn>
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
