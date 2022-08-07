<script setup lang="ts">

import { categoryData, Favorite, PostData, Status, tagData } from '@/Interfaces/PaginatedData'
import { ref } from 'vue'
import { statusOptions, favOptions } from '@/postData'
import { useForm, usePage, Link } from '@inertiajs/inertia-vue3'
import { useQuasar } from 'quasar'


interface Props {
    data: {
        categories: Array<categoryData>,
        tags: Array<tagData>,
        post?: PostData | null
    }
}

const $q = useQuasar()
const modeCreate = ref(true)
const props = defineProps<Props>()
const confirm = ref(false)
const postTitleRef = ref(null)
const currentHeroimagePaths = ref<string>('')
const currentGalleryImagePaths = ref<string[]>([])
const catOptions = props.data.categories.map(item => ({ label: item.title, value: item.id }))
const tagOptions = props.data.tags.map(item => ({ label: item.title, value: item.id }))

let form = useForm<PostData>({
    id: null,
    title: "",
    meta_title: "",
    summary: "",
    content: "",
    time_to_read: null,
    published_at: '',
    status: Status.Draft,
    favorite: Favorite.Usual,
    tag_ids: [],
    cat_ids: [],
    hero_image: null,
    images: null,
    author_id: 1
})

if (props.data.post) {
    modeCreate.value = false
    let cat_ids = []
    let tag_ids = []
    if (props.data.post.categories.length != 0) {
        cat_ids = props.data.post.categories.map(item => item.id)
    }
    if (props.data.post.tags.length != 0) {
        tag_ids = props.data.post.tags.map(item => item.id)
    }

    if (typeof props.data.post.hero_image === 'string') {
        currentHeroimagePaths.value = props.data.post.hero_image.split(',')[1]
    }

    if (props.data.post.galleries.length != 0) {
        currentGalleryImagePaths.value = props.data.post.galleries.map(item => item.thumbs.split(',')[1])
    }

    form = useForm<PostData>({
    id: props.data.post.id,
    title: props.data.post.title,
    meta_title: props.data.post.meta_title,
    summary: props.data.post.summary ?? '',
    content: props.data.post.content ?? '',
    time_to_read: props.data.post.time_to_read,
    published_at: props.data.post.published_at,
    status: props.data.post.status,
    favorite: props.data.post.favorite,
    tag_ids: tag_ids,
    cat_ids: cat_ids,
    hero_image: null,
    images: null,
    author_id: 1
})}

const storePost = () => form.post('/admin/posts', {
    onSuccess: () => onStoreSuccess(),
    onError: (errors) => onStoreFail(errors)
})
const resetForm = () => {
    form.reset()
    postTitleRef.value.resetValidation()
}

const onStoreFail = (errors) => {
    $q.notify({
        type: 'negative',
        message: `There were errors while storing: ${errors.cat_ids}`
    })
}

const onStoreSuccess = () => {
    $q.notify({
        type: 'positive',
        message: "The post have been stored successfully"
    })
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

const deleteHeroImage = () => {

}

</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="storePost">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    {{ modeCreate ? $t('Create Post') : $t('Edit Post') }}
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
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-thin ">
                                                {{ $t('Post Meta Description (SEO)') }}</div>
                                            <q-input v-model="form.meta_title" dense>
                                                <template v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                        <div class="col">
                                            <div class="row q-col-gutter-md">
                                                <div class="col-7">
                                                    <div
                                                        class="text-subtitle2 text-primary text-weight-thin" :class="{ 'text-negative': form.errors.cat_ids }">
                                                        {{ $t('Pick post category (at least one)') }}
                                                        </div>
                                                    <q-separator spaced />
                                                    <q-option-group v-model="form.cat_ids" :options="catOptions"
                                                        color="secondary" keep-color type="checkbox">
                                                        <template v-slot:label="opt">
                                                            <div class="row items-center">
                                                                <span class="text-secondary">{{ opt.label }}</span>
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
                                                        color="secondary" keep-color type="checkbox">
                                                        <template v-slot:label="opt">
                                                            <div class="row items-center">
                                                                <span class="text-secondary">{{ opt.label }}</span>
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
                                        <q-date color="secondary" mask="YYYY-MM-DD" v-model="form.published_at" minimal flat :options="calendarOptions" />
                                        <div class="form_header text-subtitle2 text-primary text-weight-thin">
                                            {{ $t('Post time to read (mins. approx.)') }}</div>
                                        <q-separator spaced />
                                        <q-slider class="q-yt-sm" v-model="form.time_to_read" :min="0" :max="50" track-size="4px"
                                            :step="1" label color="orange" />
                                        <div class="text-subtitle2 text-primary text-weight-thin q-mt-md">
                                            {{ $t('Post Status') }}</div>
                                        <q-separator spaced />
                                        <q-option-group v-model="form.status" :options="statusOptions"
                                            color="secondary" keep-color>
                                            <template v-slot:label="opt">
                                                <div class="row items-center">
                                                    <span class="text-secondary">{{ opt.label }}</span>
                                                </div>
                                            </template>
                                        </q-option-group>
                                        <div class="text-subtitle2 text-primary text-weight-thin q-mt-md">
                                            {{ $t('Favorite Post') }}
                                        </div>
                                        <q-separator spaced />
                                        <q-option-group v-model="form.favorite" :options="favOptions" color="secondary"
                                            inline keep-color>
                                            <template v-slot:label="opt">
                                                <div class="row items-center">
                                                    <span class="text-secondary">{{ opt.label }}</span>
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
                        <div v-if="!modeCreate" class="column q-gutter-md">
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="column">
                                        <div class="text-subtitle2 text-primary text-weight-thin">
                                            {{ $t('Pick new hero-image for your post') }}</div>
                                        <q-file max-file-size="1024000" v-model="form.hero_image" counter max-files="1"
                                            use-chips accept=".jpg, .png, image/*" @rejected="onRejected">
                                            <template v-slot:prepend>
                                                <q-icon color="orange" name="image" />
                                            </template>
                                            <template v-slot:hint>
                                                Only image format file, max. size &lt 1 MB
                                            </template>
                                        </q-file>
                                        <template v-if="currentHeroimagePaths">
                                            <div class="text-subtitle2 text-primary text-weight-thin q-mt-md">
                                                {{ $t('Post current hero-image') }}</div>
                                            <div class="row">
                                                <div class="col-3 relative-position">
                                                    <q-img fit="cover" height="200px" class="rounded-borders" :src="currentHeroimagePaths"/>
                                                    <q-btn round icon="delete" color="accent" class="absolute-top-right" style="top: 5px; right: 5px" @click="deleteHeroImage" />
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                        <div v-if="!modeCreate" class="column q-gutter-md">
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="column">
                                        <div class="text-subtitle2 text-primary text-weight-thin">
                                            {{ $t('Pick new images for your post gallery') }}</div>
                                        <q-file max-total-size="3000000" multiple v-model="form.images" counter
                                            max-files="5" accept=".jpg, .png, image/*" use-chips @rejected="onRejected">
                                            <template v-slot:prepend>
                                                <q-icon color="orange" name="collections" />
                                            </template>
                                            <template v-slot:hint>
                                                Only image format file(s), max. overall size &lt 3 MB
                                            </template>
                                        </q-file>
                                        <div class="text-subtitle2 text-primary text-weight-thin q-mt-md">
                                            {{ $t('Post images in your gallery') }}</div>
                                        <div class="row q-col-gutter-md">
                                            <div v-for="(path, idx) in currentGalleryImagePaths" :key="idx" class="col-3">
                                                <q-img fit="cover" height="200px" class="rounded-borders" :src="path"/>
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
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
                                <Link as="div" href="/admin/posts">
                                    <q-btn outline color="secondary" :disable="form.processing">{{ $t('Cancel') }}</q-btn>
                                </Link>
                            </div>
                            <div>
                                <q-btn type="submit" outline color="primary" :loading="form.processing">{{ modeCreate ? $t('Create') : $t('Update') }}
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
