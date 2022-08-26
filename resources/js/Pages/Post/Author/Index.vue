<script lang="ts">

import Layout from '@/Shared/LayoutAuthor.vue'

export default {
    layout: Layout,
}

</script>

<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import PostTableAuthor from '@/Shared/Tables/PostAuthorTable.vue'
import { postsAuthorBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { Paginated } from '@/Interfaces/PaginatedData'
import { SortingData, breadcrumbsData } from '@/Interfaces/PaginatedData'

interface Props {
    model: {
        posts: Paginated,
        sorting: SortingData
    }
}

const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(postsAuthorBreadcrumbs)

</script>

<template>
    <Head title="Blog posts" />
    <Breadcrumbs :data="breadcrumbs" />
    <PostTableAuthor :paginatedData="props.model.posts" :sortingData="props.model.sorting" />
</template>
