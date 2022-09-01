<script lang="ts">
    export default {
        layout: Layout
}
</script>

<script setup lang="ts">

import Layout from '@/Shared/PublicLayout.vue'
import { Head } from '@inertiajs/inertia-vue3'
import { PostData } from '@/Interfaces/PaginatedData';
import PublishedPost from '@/Shared/Views/PublishedPost.vue'
import { Inertia } from '@inertiajs/inertia';
import { computed } from 'vue'

interface Props {
    model: {
        post: PostData
    }
}

const props = defineProps<Props>()

</script>

<template>
    <Head>
        <title>Post - {{ props.model.post.title }}</title>
        <meta name="description" :content="props.model.post.meta_title">
    </Head>

    <div class="q-mt-xl">
        <PublishedPost :data="props.model.post" />
    </div>

</template>
