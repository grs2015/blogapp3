<script setup lang="ts">

import { Paginated } from '@/Interfaces/PaginatedData';
import { Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import { computed } from 'vue'

interface Props {
    paginatedData: Paginated
}

const props = defineProps<Props>()

const viewPost = (slug: string) => {
    Inertia.get(`/posts/${slug}`)
}

const postList = computed(() => props.paginatedData.data)

</script>

<template>
    <div class="column q-col-gutter-y-md q-mx-xl">
        <div v-for="(post, index) in postList" :key="post.id" class="full-width">
            <q-card flat>
                <q-card-section>
                    <div class="text-h6 text-bold">{{ post.title }}</div>
                    <template class="row justify-between items-center">
                        <div class="text-weight-light">Published <span class="text-primary">{{ post.published_at }}</span> by {{ post.user.full_name }} - {{ post.time_to_read }} mins. to read - rating: <span class="text-primary">{{ post.rating }}</span></div>
                        <div class="text-weight-light">Views: {{ post.views }}</div>
                    </template>
                </q-card-section>
                <q-separator inset />
                <q-card-section>
                    <div v-html="post.summary"></div>
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat @click="viewPost(post.slug)">Read more</q-btn>
                </q-card-actions>
            </q-card>
            <template v-if="!(index === props.paginatedData.data.length - 1)">
                <q-separator size="4px" color="teal" />
            </template>
        </div>
    </div>
</template>

<style scoped>
    .public-title {
        font-family: 'quicksand-regular';
    }

</style>
