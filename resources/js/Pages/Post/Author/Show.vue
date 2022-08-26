<script lang="ts">

import Layout from '@/Shared/LayoutAuthor.vue'

export default {
    layout: Layout,
}

</script>

<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { postAuthorViewBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { tagData, categoryData, breadcrumbsData, PostData } from '@/Interfaces/PaginatedData'
import PostForm from '@/Shared/Forms/PostForm.vue'

interface Props {
    model: {
        categories: Array<categoryData>,
        tags: Array<tagData>,
        post: PostData
    }
}
const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(postAuthorViewBreadcrumbs)

</script>


<template>
    <Head title="Blog Post - View" />
    <Breadcrumbs :data="breadcrumbs" />
    <PostForm :data="props.model" :readonly="true" />
</template>
