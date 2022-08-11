<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import CategoryTable from '@/Shared/Tables/CategoryTable.vue'
import { catsBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { breadcrumbsData, SortingData } from '@/Interfaces/PaginatedData'
import { PaginatedCategory } from '@/Interfaces/PaginatedCatData'

interface Props {
    model: {
        categories: PaginatedCategory,
        sorting: SortingData
    }
}

const props = defineProps<Props>()
const breadcrumbs = ref<breadcrumbsData[]>(catsBreadcrumbs)

</script>


<template>
    <Head title="Post categories" />
    <Breadcrumbs :data="breadcrumbs" />
    <CategoryTable :paginatedData="props.model.categories" :sortingData="props.model.sorting"/>
</template>


