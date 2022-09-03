<script lang="ts">
    export default {
        layout: Layout
}
</script>

<script setup lang="ts">

import Layout from '@/Shared/PublicLayout.vue'
import { Head } from '@inertiajs/inertia-vue3'
import { Paginated } from '@/Interfaces/PaginatedData'
import PublishedPosts from '@/Shared/Lists/PublishedPosts.vue'


interface Props {
    model: {
        posts: Paginated,
    }
}

const props = defineProps<Props>()

</script>

<template>
    <Head title="Actual posts" />
    <div class="q-mt-xl">
        <div class="q-mx-xl text-teal text-weight-medium text-h6">{{ $t('Filtered results:') }}</div>
        <div v-if="props.model.posts">
            <PublishedPosts :paginatedData="props.model.posts" />
        </div>
        <div v-else class="row justify-center text-h6 text-negative">{{ $t('No posts found') }}</div>
    </div>
</template>
