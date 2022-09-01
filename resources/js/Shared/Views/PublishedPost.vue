<script setup lang="ts">

import { Favorite, PostData, UserStatus } from '@/Interfaces/PaginatedData';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';
import  { ref } from 'vue'
import { useQuasar } from 'quasar'
import { trans } from 'laravel-vue-i18n';
import PublishedComments from '@/Shared/Lists/PublishedComments.vue'
import CommentCreator from '@/Shared/Forms/CommentCreator.vue'
import { scroll } from 'quasar'
const { getScrollTarget, setVerticalScrollPosition } = scroll

interface Props {
    data: PostData
}

const props = defineProps<Props>()
const currentHeroimagePath = ref('')
const slide = ref(1)
const rating = ref(3)
const $q = useQuasar()

currentHeroimagePath.value = props.data.hero_image ? props.data.hero_image.split(',')[0] : null

const validationErrors = (errors) => {
    return Object.entries(errors).map(item => item[1]).join(', ')
}

const updateRating = (value: number) => {
    let postSlug = props.data.slug
    Inertia.post(`/member/posts/${postSlug}/rate`, { rating: value }, {
        onSuccess: () => onUpdateSuccess(),
        onError: (errors) => onUpdateFail(errors),
        preserveScroll: true
    } )}

const onUpdateSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans("Post rating updated successfully. Thank you")
    })
}
const onUpdateFail = (errors) => {
    $q.notify({
        type: 'negative',
        message: trans("Something went wrong with post rating update: ") + validationErrors(errors)
    })
}


</script>


<template>
    <div class="column q-col-gutter-y-lg">
        <!-- <q-parallax :height="400" :speed="0.8">
            <template v-slot:media>
                <img :src="currentHeroimagePath" style="filter: saturate(0.8)">
            </template>
        </q-parallax> -->
        <q-img fit="cover" height="400px" :src="currentHeroimagePath" style="filter: saturate(0.8)" class="rounded-borders">
            <div v-if="!currentHeroimagePath" class="absolute-full flex flex-center bg-grey-3 text-grey-5 text-h4">
                No post image selected
            </div>
        </q-img>
        <div class="column q-mx-xl">
            <template class="row justify-between items-center">
                <div class="text-h6 text-bold">{{ props.data.title }}</div>
                <div v-if="props.data.favorite === Favorite.Favorite">
                    <q-icon name="star" color="green" size="24px" />
                </div>
            </template>
            <template class="row justify-between items-center">
                <div class="text-weight-light">Published <span class="text-primary">{{ props.data.published_at }}</span> by {{ props.data.user.full_name }} - {{ props.data.time_to_read }} mins. to read</div>
                <div class="text-weight-light">Views: {{ props.data.views }}</div>
            </template>
            <div class="row justify-end text-weight-light items-center q-mt-sm">
                <div class="q-mr-sm">Categories:</div>
                <div v-for="(catIcon, index) in props.data.categories" :key="catIcon.id">
                        <div class="row rounded-borders flex-center color-box" :style="{background: catIcon.color}"
                            :class="[index !== props.data.categories.length - 1 ? 'q-mr-sm' : '']">
                            <q-icon :name="catIcon.icon" color="white" size="sm">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ catIcon.title }}
                                </q-tooltip>
                            </q-icon>
                        </div>
                </div>
            </div>
            <div v-if="props.data.tags.length > 0" class="row justify-end text-weight-light items-center q-mt-sm">
                <div class="q-mr-sm">Tags:</div>
                <div class="row q-col-gutter-x-sm">
                    <div v-for="(tag, index) in props.data.tags" :key="tag.id">
                        <q-badge outline color="primary" :label="tag.title" />
                    </div>
                </div>
            </div>
            <div class="row justify-end text-weight-light items-center q-mt-sm">
                <div class="q-mr-sm">Post rating:</div>
                <div class="text-subtitle2 text-primary">{{ props.data.rating }}</div>
            </div>
            <div class="q-mt-xl">
                <div v-html="props.data.content"></div>
            </div>
            <div v-if="props.data.galleries.length !== 0" class="q-mt-md">
                <q-carousel
                    animated
                    v-model="slide"
                    arrows
                    navigation
                    infinite
                    control-color="primary"
                    class="rounded-borders" >
                    <q-carousel-slide v-for="(image, index) in props.data.galleries" :key="image.id"
                        :name="index + 1" :img-src="image.thumbs" />
                </q-carousel>
            </div>
            <q-separator spaced />
            <div class="row justify-start items-center q-mt-md q-col-gutter-x-sm">
                <div>Rate the post:</div>
                <q-rating
                    v-model="rating"
                    size="2em"
                    :max="5"
                    color="green"
                    :disable="!(usePage().props.value.auth.user && usePage().props.value.can.see_credentials && usePage().props.value.auth.user.status === UserStatus.Enabled)"
                    @update:model-value="updateRating"
                />
            </div>
            <template v-if="props.data.comments.length !== 0">
                <div class="text-h6 text-bold q-mt-sm">Comments ({{ props.data.comments.length }})</div>
                <PublishedComments :data="props.data.comments" />
            </template>
            <template v-if="usePage().props.value.auth.user && usePage().props.value.can.see_credentials && usePage().props.value.auth.user.status === UserStatus.Enabled">
                <div class="text-h6 text-bold q-mt-sm">Your comment:</div>
                <CommentCreator :data="{ id: props.data.id, slug: props.data.slug }" />
            </template>
            <!-- <CommentCreator :data="{ id: props.data.id, slug: props.data.slug }" /> -->
            <div class="q-mt-xl">

            </div>
        </div>
    </div>
</template>

<style lang="sass" scoped>
.color-box
    width: 27px
    height: 27px

.q-parallax
    position: relative
    width: 100%
    overflow: hidden
    border-radius: inherit
    height: 400px
</style>
