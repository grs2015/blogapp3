<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { postCreateBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { tagData, categoryData, breadcrumbsData } from '@/Interfaces/PaginatedData'
import PostCreateTable from '@/Shared/Tables/PostCreateTable.vue'

interface Props {
    model: {
        categories: Array<categoryData>,
        tags: Array<tagData>,
    }
}
const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(postCreateBreadcrumbs)

</script>


<template>
    <Head title="Blog Post - Create" />
    <Breadcrumbs :data="breadcrumbs" />
    <PostCreateTable :data="props.model" />
    <!-- {{ props.model.tags }} -->
</template>
