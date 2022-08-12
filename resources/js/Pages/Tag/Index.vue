<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import TagTable from '@/Shared/Tables/TagTable.vue'
import { tagsBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { breadcrumbsData, SortingData } from '@/Interfaces/PaginatedData'
import { PaginatedTag } from '@/Interfaces/PaginatedTagData'

interface Props {
    model: {
        tags: PaginatedTag,
        sorting: SortingData
    }
}

const props = defineProps<Props>()
const breadcrumbs = ref<breadcrumbsData[]>(tagsBreadcrumbs)

</script>


<template>
    <Head title="Post tags" />
    <Breadcrumbs :data="breadcrumbs" />
    <TagTable :paginatedData="props.model.tags" :sortingData="props.model.sorting" />
</template>
