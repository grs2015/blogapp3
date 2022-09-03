<script setup lang="ts">

import { useQuasar } from 'quasar'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import { ref } from 'vue'
import { validationErrors } from '@/helpers'
import { trans } from 'laravel-vue-i18n';


type commentData = {
    title: string,
    content: string,
    post_id: number,
    slug: string,
    author: string
}

interface Props {
    data: {
        id: number,
        slug: string
    }
}
const props = defineProps<Props>()

let form = useForm<commentData>({
    title: '',
    content: '',
    post_id: props.data.id,
    slug: props.data.slug,
    author: usePage().props.value.auth.user.full_name
})


const $q = useQuasar()
const contentRef = ref(null)
const titleRef = ref(null)

const actionComment = () => {
    form.post('/member/comments', {
        preserveScroll: false,
        onSuccess: () => {
            form.reset('title', 'content')
            onStoreSuccess()}
        ,
        onError: (errors) => onStoreFail(errors),
        onFinish: () => resetForm()
    })
}

const onStoreFail = (errors) => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while sending comment: ') + validationErrors(errors)
    })
}

const onStoreSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The comment have been sent successfully. Thank you')
    })
}

const resetForm = () => {
    form.reset('title', 'content')
    contentRef.value.resetValidation()
    titleRef.value.resetValidation()
}
</script>

<template>
    <q-form @submit="actionComment">
        <q-card flat bordered>
            <q-card-section>
                <q-input
                    v-model="form.title"
                    dense clearable filled clear-icon="close" label-color="primary"
                    :label="$t('Title of your comment')"
                    ref="titleRef"
                    :rules="[val => !!val || 'Field is required']" >
                    <template v-slot:prepend>
                        <q-icon name="edit" color="orange" />
                    </template>
                </q-input>
                <q-input
                    v-model="form.content"
                    autogrow
                    filled clearable clear-icon="close" label-color="primary"
                    :label="$t('Content of your comment')"
                    dense
                    ref="contentRef"
                    :rules="[val => !!val || 'Field is required']"
                    class="q-mt-sm" >
                    <template v-slot:prepend>
                        <q-icon name="edit_note" color="orange" />
                    </template>
                </q-input>
            </q-card-section>
            <q-card-actions align="right">
                <q-btn flat :label="$t('Reset')" color="secondary" @click="resetForm" :disable="form.processing" />
                <q-btn flat :label="$t('Send comment')" color="primary" type="submit" :loading="form.processing">
                    <template v-slot:loading>
                        <q-spinner-hourglass />
                    </template>
                </q-btn>
            </q-card-actions>
        </q-card>
    </q-form>
</template>
