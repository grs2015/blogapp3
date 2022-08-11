<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { categoryCreateBreadcrumbs, categoryEditBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { categoryData, breadcrumbsData } from '@/Interfaces/PaginatedData'
import CategoryForm from '@/Shared/Forms/CategoryForm.vue'

interface Props {
    model: {
        category?: categoryData | null
    }
}
const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(!props.model.category ? categoryCreateBreadcrumbs : categoryEditBreadcrumbs)

const title = props.model.category ? 'Edit' : 'Create'

</script>


<template>
    <Head :title="`Blog Category - ${ title }`" />
    <Breadcrumbs :data="breadcrumbs" />
    <CategoryForm :data="props.model" />
</template>
