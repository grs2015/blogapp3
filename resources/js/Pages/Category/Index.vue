<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import CategoryTable from '@/Shared/Tables/CategoryTable.vue'
import { catsBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { categoryData, breadcrumbsData } from '@/Interfaces/PaginatedData'

interface Props {
    model: {
        categories: Array<categoryData>
    }
}

const props = defineProps<Props>()
const breadcrumbs = ref<breadcrumbsData[]>(catsBreadcrumbs)

</script>


<template>
    <Head title="Post categories" />
    <Breadcrumbs :data="breadcrumbs" />
    <CategoryTable :paginatedData="props.model.categories"/>
</template>


