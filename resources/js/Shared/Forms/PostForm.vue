<script setup lang="ts">

import { categoryData, CommentData, Favorite, PostData, Status, tagData, UserRole } from '@/Interfaces/PaginatedData'
import { reactive, ref, watch, nextTick } from 'vue'
import { useForm, Link, usePage } from '@inertiajs/inertia-vue3'
import { useQuasar } from 'quasar'
import { Inertia } from '@inertiajs/inertia'
import { trans } from 'laravel-vue-i18n';
import CommentForm from '@/Shared/Forms/CommentForm.vue'


interface Props {
    data: {
        categories: Array<categoryData>,
        tags: Array<tagData>,
        post?: PostData | null,
    },
    readonly?: boolean
}

const $q = useQuasar()
const props = defineProps<Props>()

const modeCreate = ref(true) // Create/Edit mode of the component

const imageLoad = ref(false) // Image processing indicator while deleting image
const deleteBtn = ref(false) // Button disable indicator while processing the action
const imageGalleryLoad = reactive({})
const confirm = ref(false) // Confirmation of resetting form
const readonly = ref(props.readonly) // Readonly (view) mode
const processComment = ref(false)

const postTitleRef = ref(null) // Ref for title input
const currentHeroimagePaths = ref<string>('')
const currentGalleryImagePaths = ref<string[]>([])

const catOptions = readonly.value ?
        props.data.post.categories.map(item => ({ label: item.title, value: item.id })) :
        props.data.categories.map(item => ({ label: item.title, value: item.id }))

const tagOptions = readonly.value ?
        props.data.post.tags.map(item => ({ label: item.title, value: item.id })) :
        props.data.tags.map(item => ({ label: item.title, value: item.id }))

const userRole = usePage().props.value.auth.user.role
const authUser = ref(userRole === UserRole.SuperAdmin ? 'admin' : userRole === UserRole.Admin ? 'admin' : userRole === UserRole.Author ? 'author' : 'member')

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
    author_id: usePage().props.value.auth.user.id,
    views: 0
})

if (props.data.post) {
    modeCreate.value = false
    let cat_ids:number[] = []
    let tag_ids:number[] = []
    if (props.data.post.categories.length != 0) {
        cat_ids = props.data.post.categories.map(item => item.id)
    }
    if (props.data.post.tags.length != 0) {
        tag_ids = props.data.post.tags.map(item => item.id)
    }

    if (typeof props.data.post.hero_image === 'string') {
        currentHeroimagePaths.value = props.data.post.hero_image.split(',')[0]
    }

    if (props.data.post.galleries.length != 0) {
        props.data.post.galleries.forEach((item, idx) => {
            imageGalleryLoad[`name-${idx}`] = false
        })
        currentGalleryImagePaths.value = props.data.post.galleries.map(item => item.thumbs.split(',')[0])
    }

    form = useForm<PostData>({
    _method: "put",
    id: props.data.post.id,
    title: props.data.post.title,
    slug: props.data.post.slug,
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
    author_id: props.data.post.author_id,
    views: props.data.post.views
})}

const actionPost = () => {
    if (modeCreate.value) {
        form.post(`/${authUser.value}/posts`, {
        onSuccess: () => onStoreSuccess(),
        onError: (errors) => onStoreFail(errors)
        })
        return
    }

    form.post(`/${authUser.value}/posts/${form.slug}`, {
        onSuccess: () => onUpdateSuccess(),
        onError: (errors) => onUpdateFail(errors),
        onStart: () => deleteBtn.value = true,
        onFinish: () => deleteBtn.value = false
    })
}
const resetForm = () => {
    form.reset()
    postTitleRef.value.resetValidation()
}

const validationErrors = (errors) => {

    return Object.entries(errors).map(item => item[1]).join(', ')
}

const onStoreFail = (errors) => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while storing: ') + validationErrors(errors)
    })
}


const onUpdateFail = (errors) => {
    $q.notify({
        type: 'negative',
        message: trans('There were errors while updating: ') + validationErrors(errors)
    })
}

const onStoreSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The post have been stored successfully')
    })
}

const onUpdateSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans('The post have been updated successfully')
    })
    form.hero_image = null
    form.images = null
}

const onRejected = (rejectedEntries) => {
    $q.notify({
        type: 'negative',
        message: rejectedEntries.length + trans(' file(s) did not pass validation constraints')
    })
}

const currentDate = () => {
    let now = new Date()
    return now.getFullYear()  + "/" + (now.getMonth()+1).toString().padStart(2, '0') + "/" + now.getDate().toString().padStart(2, '0')
}

const calendarOptions = (date: string) => date >= currentDate()

const deleteHeroImage = () => {
    Inertia.post(`/${authUser.value}/hero_image`, { 'post_id': props.data.post.id, 'slug': props.data.post.slug }, {
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

const deleteGalleryImage = (idx) => {
    Inertia.post(`/${authUser.value}/gallery_image`, { 'post_id': props.data.post.id, 'slug': props.data.post.slug, 'image_idx': idx }, {
        onStart: () => {
            imageGalleryLoad[`name-${idx}`] = true,
            deleteBtn.value = true
        },
        onSuccess: () => onUpdateSuccess(),
        onError: (errors) => onUpdateFail(errors),
        onFinish: () => {
            imageGalleryLoad[`name-${idx}`] = false,
            deleteBtn.value = false
        },
        preserveScroll: true
    })
}

watch(() => props.data.post, () => {
    if (typeof props.data.post.hero_image === 'string') {
        currentHeroimagePaths.value = props.data.post.hero_image.split(',')[0]
    }
    if (!props.data.post.hero_image) {
        currentHeroimagePaths.value = ''
    }
    // Object.keys(imageGalleryLoad).forEach(key => delete imageGalleryLoad[key]);
    props.data.post.galleries.forEach((item, idx) => {
        imageGalleryLoad[`name-${idx}`] = false
    })
    currentGalleryImagePaths.value = props.data.post.galleries.map(item => item.thumbs.split(',')[0])
})

const statusChanged = async ({ id, status }) => {
    await nextTick()
    processComment.value = true
    Inertia.post('/admin/comments/status', { id: id, status: status }, {
        onFinish: () => processComment.value = false,
        preserveScroll: true
    })
}

const deleteComment = async({ id }) => {
    await nextTick()
    processComment.value = true
    Inertia.delete(`/admin/comments/${id}`, {
        onFinish: () => processComment.value = false,
        preserveScroll: true
    })
}

</script>

<template>
    <div class="q-mt-md">
        <q-form @submit="actionPost">
            <q-card flat bordered>
                <q-card-section class="text-primary text-h6">
                    <template v-if="readonly">
                        {{ $t('View Post') }}
                    </template>
                    <template v-else>
                        {{ modeCreate ? $t('Create Post') : $t('Edit Post') }}
                    </template>
                </q-card-section>
                <q-card-section>

                    <div class="column q-col-gutter-md">
                        <div class="row q-col-gutter-md">
                            <div class="col column">
                                <q-card flat bordered>
                                    <q-card-section>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-regular">
                                                {{ $t('Post Title') }}</div>
                                            <q-input v-model="form.title" dense :disable="readonly" :filled="readonly"
                                                :rules="[val => !!val || 'Field is required']" ref="postTitleRef"
                                                :input-class="[ readonly ? 'bg-grey-3': '' ]">
                                                <template v-if="!readonly" v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-regular">
                                                {{ $t('Short description') }}</div>
                                            <q-editor :placeholder="$t('Start creating here...')" v-model="form.summary"
                                                min-height="5rem" :disable="readonly" :content-class="[ readonly ? 'bg-grey-4': '' ]"
                                                :toolbar="[
                                                    [
                                                        {
                                                            label: $q.lang.editor.align,
                                                            icon: $q.iconSet.editor.align,
                                                            fixedLabel: true,
                                                            list: 'only-icons',
                                                            options: ['left', 'center', 'right', 'justify']
                                                        }
                                                    ],
                                                    [
                                                        {
                                                            label: 'Font',
                                                            icon: $q.iconSet.editor.bold,
                                                            fixedLabel: true,
                                                            list: 'only-icons',
                                                            options: ['bold', 'italic', 'strike', 'underline']
                                                        }
                                                    ],
                                                    ['token', 'hr', 'link', 'custom_btn'],
                                                    ['undo', 'redo'],
                                                    ['removeFormat']
                                                ]" />
                                        </div>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-regular">
                                                {{ $t('Post contents') }}</div>
                                            <q-editor min-height="15rem" max-height="400px" :placeholder="$t('Start creating here...')" :dense="$q.screen.lt.md"
                                                v-model="form.content" :disable="readonly" :content-class="[ readonly ? 'bg-grey-4': '' ]"
                                                :toolbar="[
                                                    [
                                                        {
                                                            label: $q.lang.editor.align,
                                                            icon: $q.iconSet.editor.align,
                                                            fixedLabel: true,
                                                            list: 'only-icons',
                                                            options: ['left', 'center', 'right', 'justify']
                                                        }
                                                    ],
                                                    [
                                                        {
                                                            label: 'Font',
                                                            icon: $q.iconSet.editor.bold,
                                                            fixedLabel: true,
                                                            list: 'only-icons',
                                                            options: ['bold', 'italic', 'strike', 'underline']
                                                        }
                                                    ],
                                                    ['token', 'hr', 'link', 'custom_btn'],
                                                    ['quote', 'unordered', 'ordered', 'outdent', 'indent'],
                                                    ['undo', 'redo'],
                                                    [
                                                        {
                                                            label: $q.lang.editor.formatting,
                                                            icon: $q.iconSet.editor.formatting,
                                                            list: 'no-icons',
                                                            options: [
                                                            'p',
                                                            'h1',
                                                            'h2',
                                                            'h3',
                                                            'h4',
                                                            'h5',
                                                            'h6',
                                                            'code'
                                                            ]
                                                        },
                                                        {
                                                            label: $q.lang.editor.fontSize,
                                                            icon: $q.iconSet.editor.fontSize,
                                                            fixedLabel: true,
                                                            fixedIcon: true,
                                                            list: 'no-icons',
                                                            options: [
                                                            'size-1',
                                                            'size-2',
                                                            'size-3',
                                                            'size-4',
                                                            'size-5',
                                                            'size-6',
                                                            'size-7'
                                                            ]
                                                        },
                                                        'removeFormat'
                                                    ]
                                                ]" />
                                        </div>
                                        <div class="col q-mb-md">
                                            <div class="form_header text-subtitle2 text-primary text-weight-regular ">
                                                {{ $t('Post Meta Description (SEO)') }}</div>
                                            <q-input v-model="form.meta_title" :disable="readonly" dense :filled="readonly" :input-class="[ readonly ? 'bg-grey-3': '' ]">
                                                <template v-if="!readonly" v-slot:prepend>
                                                    <q-icon name="edit" color="orange" />
                                                </template>
                                            </q-input>
                                        </div>
                                        <div class="col">
                                            <div class="row q-col-gutter-md">
                                                <div class="col-7">
                                                    <div
                                                        class="text-subtitle2 text-primary text-weight-regular" :class="{ 'text-negative': form.errors.cat_ids }">
                                                        <template v-if="readonly">
                                                            {{ $t('Picked post category(-ies)') }}
                                                        </template>
                                                        <template v-else>
                                                            {{ $t('Pick post category (at least one)') }}
                                                        </template>
                                                    </div>
                                                    <q-separator spaced />
                                                    <q-option-group v-model="form.cat_ids" :options="catOptions" :disable="readonly"
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
                                                        class="form_header text-subtitle2 text-primary text-weight-regular">
                                                        <template v-if="readonly">
                                                            {{ $t('Picked post tag(s)') }}
                                                        </template>
                                                        <template v-else>
                                                            {{ $t('Pick post tag') }}
                                                        </template>
                                                        </div>
                                                    <q-separator spaced />
                                                    <q-option-group v-model="form.tag_ids" :options="tagOptions" :disable="readonly"
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
                                        <div class="form_header text-subtitle2 text-primary text-weight-regular">
                                            {{ $t('Post publish date') }}</div>
                                        <q-separator spaced />
                                        <q-date color="secondary" :readonly="readonly" mask="YYYY-MM-DD" v-model="form.published_at" minimal flat :options="calendarOptions" />
                                        <div class="form_header text-subtitle2 text-primary text-weight-regular">
                                            {{ $t('Post time to read (mins. approx.)') }}</div>
                                        <q-separator spaced />
                                        <q-slider class="q-yt-sm" :readonly="readonly" v-model="form.time_to_read" :min="0" :max="50" track-size="4px"
                                            :step="1" label color="orange" />
                                        <div class="row q-mt-md justify-between items-center">
                                            <div class="text-subtitle2 text-primary text-weight-regular">
                                                {{ $t('Post status:') }}
                                            </div>
                                            <div class="text-right">
                                                {{ form.status }}
                                            </div>
                                        </div>
                                        <q-separator spaced />
                                        <div class="row q-mt-md justify-between items-center">
                                            <div class="text-subtitle2 text-primary text-weight-regular">
                                                {{ $t('Favorite post:') }}
                                            </div>
                                            <div class="text-right">
                                                {{ form.favorite }}
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                        </div>

                        <div v-if="!modeCreate && usePage().props.value.can.view_comments">
                            <CommentForm :data="props.data.post.comments" @status="statusChanged" @delete="deleteComment" :loading="processComment"/>
                        </div>

                        <div v-if="modeCreate" class="column q-gutter-md">
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="column">
                                        <div class="text-subtitle2 text-primary text-weight-regular">
                                            {{ $t('Pick hero-image for your post') }}</div>
                                        <q-file max-file-size="1024000" v-model="form.hero_image" counter max-files="1"
                                            use-chips accept=".jpg, .png, image/*" @rejected="onRejected">
                                            <template v-slot:prepend>
                                                <q-icon color="orange" name="image" />
                                            </template>
                                            <template v-slot:hint>
                                                {{ $t('Only image format file, max. size < 1 MB') }}
                                            </template>
                                        </q-file>
                                    </div>
                                    <div class="column q-mt-md">
                                        <div class="text-subtitle2 text-primary text-weight-regular">
                                            {{ $t('Pick images for your post gallery') }}</div>
                                        <q-file max-total-size="3000000" multiple v-model="form.images" counter
                                            max-files="5" accept=".jpg, .png, image/*" use-chips @rejected="onRejected">
                                            <template v-slot:prepend>
                                                <q-icon color="orange" name="collections" />
                                            </template>
                                            <template v-slot:hint>
                                                {{ $t('Only image format file(s), max. overall size < 3 MB') }}
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
                                        <template v-if="!readonly">
                                            <div class="text-subtitle2 text-primary text-weight-regular">
                                            {{ $t('Pick new hero-image for your post') }}</div>
                                            <q-file max-file-size="1024000" v-model="form.hero_image" counter max-files="1"
                                                use-chips accept=".jpg, .png, image/*" @rejected="onRejected">
                                                <template v-slot:prepend>
                                                    <q-icon color="orange" name="image" />
                                                </template>
                                                <template v-slot:hint>
                                                    {{ $t('Only image format file, max. size < 1 MB') }}
                                                </template>
                                            </q-file>
                                        </template>
                                        <template v-if="currentHeroimagePaths">
                                            <div class="text-subtitle2 text-primary text-weight-regular"
                                                :class="{ 'q-mt-md': !readonly }" >
                                                {{ $t('Post current hero-image') }}</div>
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
                                                    <template v-if="!readonly">
                                                        <q-btn :disable="deleteBtn" round icon="delete" color="accent" class="absolute-top-right" style="top: 5px; right: 5px" @click="deleteHeroImage" />
                                                        <q-spinner-gears size="4em" v-if="imageLoad" class="absolute-center z-max" color="accent"/>
                                                        <div v-if="imageLoad" class="light-dimmed transparent absolute-full bg-grey-3"></div>
                                                    </template>

                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="text-subtitle2 text-primary text-weight-regular"
                                                :class="{ 'q-mt-md': !readonly }" >
                                                {{ $t('No hero-image attached at this time') }}</div>
                                        </template>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                        <div v-if="!modeCreate" class="column q-gutter-md">
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="column">
                                        <template v-if="!readonly">
                                            <div class="text-subtitle2 text-primary text-weight-regular">
                                                {{ $t('Pick new images for your post gallery') }}</div>
                                            <q-file max-total-size="3000000" multiple v-model="form.images" counter
                                                max-files="5" accept=".jpg, .png, image/*" use-chips @rejected="onRejected">
                                                <template v-slot:prepend>
                                                    <q-icon color="orange" name="collections" />
                                                </template>
                                                <template v-slot:hint>
                                                    {{ $t('Only image format file(s), max. overall size < 3 MB') }}
                                                </template>
                                            </q-file>
                                        </template>
                                        <template v-if="currentGalleryImagePaths.length != 0">
                                            <div class="text-subtitle2 text-primary text-weight-regular"
                                                :class="{ 'q-mt-md': !readonly }">
                                                {{ $t('Post images in your gallery') }}</div>
                                            <div class="row q-col-gutter-md">
                                                <div v-for="(path, idx) in currentGalleryImagePaths" :key="idx" class="col-3 relative-position">
                                                    <q-img fit="cover" height="200px" class="rounded-borders" :src="path">
                                                        <template v-slot:loading>
                                                            <q-spinner-gears color="accent" />
                                                        </template>
                                                        <template v-slot:error>
                                                            <div class="absolute-full flex flex-center bg-grey-3 text-accent">
                                                                {{ $t('Cannot load image') }}
                                                            </div>
                                                        </template>
                                                    </q-img>
                                                    <template v-if="!readonly">
                                                        <q-btn :disable="deleteBtn" round icon="delete" color="accent" class="absolute-top-right" style="top: 21px; right: 7px" @click="deleteGalleryImage(idx)" />
                                                        <q-spinner-gears size="4em" v-if="imageGalleryLoad[`name-${idx}`]" class="absolute-center z-max" color="accent"/>
                                                        <div v-if="imageGalleryLoad[`name-${idx}`]" class="light-dimmed transparent absolute-full bg-grey-3"></div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="text-subtitle2 text-primary text-weight-regular"
                                                :class="{ 'q-mt-md': !readonly }" >
                                                {{ $t('No image gallery attached at this time') }}</div>
                                        </template>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                    </div>
                </q-card-section>
                <q-card-section>
                    <div v-if="!readonly" class="row justify-between items-center">
                        <div>
                            <q-btn outline color="negative" @click="confirm = true" :disable="form.processing">{{ $t('Reset') }}</q-btn>
                        </div>
                        <div class="row q-col-gutter-md">
                            <div>
                                <Link as="div" :href="`/${authUser}/posts`">
                                    <q-btn outline color="secondary" :disable="form.processing">{{ modeCreate ? $t('Cancel') : $t('Back') }}</q-btn>
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
                    <div v-else class="row justify-end items-center">
                        <div class="row q-col-gutter-md">
                            <div>
                                <Link as="div" :href="`/${authUser}/posts`">
                                    <q-btn outline color="secondary" :disable="form.processing">{{ $t('Back') }}</q-btn>
                                </Link>
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
