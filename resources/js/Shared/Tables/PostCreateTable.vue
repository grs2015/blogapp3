<script setup lang="ts">

import { categoryData, Favorite, Status, tagData } from '@/Interfaces/PaginatedData'
import { ref, watch } from 'vue'
import { statusOptions, favOptions } from '@/postData'
import { useForm } from '@inertiajs/inertia-vue3'
import { useQuasar } from 'quasar'


interface Props {
    data: {
        categories: Array<categoryData>,
        tags: Array<tagData>,
    }
}

interface postData {
    title: string,
    summary?: string,
    content?: string,
    time_to_read?: number,
    published_at?: string,
    published: Status,
    favorite: Favorite,
    tag_ids?: Array<number>,
    cat_ids: Array<number>,
    hero_image: File,
    images: Array<File>
}

const props = defineProps<Props>()
const $q = useQuasar()
const storePost = () => {
    console.log('Hit!')
    form.post('/admin/posts')
}
const resetForm = () => {
    form.title = ''
    form.summary = ''
    form.content = ''
    form.time_to_read = null
    form.published_at = ''
    form.published = Status.Draft
    form.favorite = Favorite.Usual
    form.tag_ids = []
    form.cat_ids = []
    form.hero_image = null
    form.images = null
    postTitleRef.value.resetValidation()
}
const onRejected = (rejectedEntries) => {
    $q.notify({
        type: 'negative',
        message: `${rejectedEntries.length} file(s) did not pass validation constraints`
    })
}

const currentDate = () => {
    let now = new Date()
    return now.getFullYear()  + "/" + (now.getMonth()+1).toString().padStart(2, '0') + "/" + now.getDate().toString().padStart(2, '0')
}

const calendarOptions = (date: string) => date >= currentDate()

const confirm = ref(false)
const postTitleRef = ref(null)
const catOptions = props.data.categories.map(item => ({ label: item.title, value: item.id }))
const tagOptions = props.data.tags.map(item => ({ label: item.title, value: item.id }))

const form = useForm<postData>({
    title: "",
    summary: "",
    content: "",
    time_to_read: null,
    published_at: '',
    published: Status.Draft,
    favorite: Favorite.Usual,
    tag_ids: [],
    cat_ids: [],
    hero_image: null,
    images: null
})

watch(form, () => console.log(form))

</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="storePost">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    {{ $t('Create Post') }}
                </q-card-section>
                <q-card-section>

                    <div class="column q-col-gutter-md">
                        <div class="row q-col-gutter-md">
                            <div class="col column">
                                <q-card flat bordered>
                                    <q-card-section>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Post Title') }}</div>
                                            <q-input v-model="form.title" dense
                                                :rules="[val => !!val || 'Field is required']" ref="postTitleRef">
                                                <template v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Short description') }}</div>
                                            <q-editor :placeholder="$t('Start creating here...')" v-model="form.summary"
                                                min-height="5rem" />
                                        </div>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                                {{ $t('Post contents') }}</div>
                                            <q-editor min-height="15rem" :placeholder="$t('Start creating here...')"
                                                v-model="form.content" />
                                        </div>
                                        <div class="col">
                                            <div class="row q-col-gutter-md">
                                                <div class="col-7">
                                                    <div
                                                        class="form_header text-subtitle2 text-primary text-weight-thin">
                                                        {{ $t('Pick post category') }}</div>
                                                    <q-separator spaced />
                                                    <q-option-group v-model="form.cat_ids" :options="catOptions"
                                                        color="primary" keep-color type="checkbox">
                                                        <template v-slot:label="opt">
                                                            <div class="row items-center">
                                                                <span class="text-primary">{{ opt.label }}</span>
                                                            </div>
                                                        </template>
                                                    </q-option-group>
                                                </div>
                                                <div class="col-5">
                                                    <div
                                                        class="form_header text-subtitle2 text-primary text-weight-thin">
                                                        {{ $t('Pick post tag') }}</div>
                                                    <q-separator spaced />
                                                    <q-option-group v-model="form.tag_ids" :options="tagOptions"
                                                        color="primary" keep-color type="checkbox">
                                                        <template v-slot:label="opt">
                                                            <div class="row items-center">
                                                                <span class="text-primary">{{ opt.label }}</span>
                                                            </div>
                                                        </template>
                                                    </q-option-group>
                                                </div>
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                            <div class="col-auto">
                                <q-card flat bordered>
                                    <q-card-section class="column">
                                        <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                            {{ $t('Post publish date') }}</div>
                                        <q-separator spaced />
                                        <q-date v-model="form.published_at" minimal flat :options="calendarOptions" />
                                        <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                            {{ $t('Post time to read (mins. approx.)') }}</div>
                                        <q-separator spaced />
                                        <q-slider class="q-yt-sm" v-model="form.time_to_read" :min="0" :max="50"
                                            :step="1" label color="orange" />
                                        <div class="text-subtitle2 text-primary text-weight-thin q-mt-md">
                                            {{ $t('Post Status') }}</div>
                                        <q-separator spaced />
                                        <q-option-group v-model="form.published" :options="statusOptions"
                                            color="primary" keep-color>
                                            <template v-slot:label="opt">
                                                <div class="row items-center">
                                                    <span class="text-primary">{{ opt.label }}</span>
                                                </div>
                                            </template>
                                        </q-option-group>
                                        <div class="text-subtitle2 text-primary text-weight-thin q-mt-md">
                                            {{ $t('Favorite Post') }}
                                        </div>
                                        <q-separator spaced />
                                        <q-option-group v-model="form.favorite" :options="favOptions" color="primary"
                                            inline keep-color>
                                            <template v-slot:label="opt">
                                                <div class="row items-center">
                                                    <span class="text-primary">{{ opt.label }}</span>
                                                </div>
                                            </template>
                                        </q-option-group>
                                    </q-card-section>
                                </q-card>
                            </div>
                        </div>

                        <div class="column q-gutter-md">
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="column">
                                        <div class="text-subtitle2 text-primary text-weight-thin">
                                            {{ $t('Pick hero-image for your post') }}</div>
                                        <q-file max-file-size="1024000" v-model="form.hero_image" counter max-files="1"
                                            use-chips accept=".jpg, .png, image/*" @rejected="onRejected">
                                            <template v-slot:prepend>
                                                <q-icon color="orange" name="image" />
                                            </template>
                                            <template v-slot:hint>
                                                Only image format file, max. size &lt 1 MB
                                            </template>
                                        </q-file>
                                    </div>
                                    <div class="column q-mt-md">
                                        <div class="text-subtitle2 text-primary text-weight-thin">
                                            {{ $t('Pick images for your post gallery') }}</div>
                                        <q-file max-total-size="3000000" multiple v-model="form.images" counter
                                            max-files="5" accept=".jpg, .png, image/*" use-chips @rejected="onRejected">
                                            <template v-slot:prepend>
                                                <q-icon color="orange" name="collections" />
                                            </template>
                                            <template v-slot:hint>
                                                Only image format file(s), max. overall size &lt 3 MB
                                            </template>
                                        </q-file>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row justify-between items-center">
                        <div>
                            <q-btn outline color="negative" @click="confirm = true">{{ $t('Reset') }}</q-btn>
                        </div>
                        <div class="row q-col-gutter-md">
                            <div>
                                <q-btn outline color="secondary">{{ $t('Cancel') }}</q-btn>
                            </div>
                            <div>
                                <q-btn type="submit" outline color="primary">{{ $t('Submit') }}</q-btn>
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
