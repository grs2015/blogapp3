<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { postCreateBreadcrumbs, postEditBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { tagData, categoryData, breadcrumbsData, PostData } from '@/Interfaces/PaginatedData'
import PostForm from '@/Shared/Forms/PostForm.vue'

interface Props {
    model: {
        categories: Array<categoryData>,
        tags: Array<tagData>,
        post?: PostData | null
    }
}
const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(!props.model.post ? postCreateBreadcrumbs : postEditBreadcrumbs)

const title = props.model.post ? 'Edit' : 'Create'

</script>


<template>
    <Head :title="`Blog Post - ${ title }`" />
    <Breadcrumbs :data="breadcrumbs" />
    <PostForm :data="props.model" />
</template>
