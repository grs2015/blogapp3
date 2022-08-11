<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import PostTable from '@/Shared/Tables/PostTable.vue'
import { postsBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { Paginated, SortingData, breadcrumbsData } from '@/Interfaces/PaginatedData'

interface Props {
    model: {
        posts: Paginated,
        sorting: SortingData
    }
}

const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(postsBreadcrumbs)

</script>

<template>
    <Head title="Blog posts" />
    <Breadcrumbs :data="breadcrumbs" />
    <PostTable :paginatedData="props.model.posts" :sortingData="props.model.sorting"/>
</template>
