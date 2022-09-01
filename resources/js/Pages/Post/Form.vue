<script setup lang="ts">

import { Head, usePage } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { postCreateBreadcrumbs, postEditBreadcrumbs } from '@/breadcrumbsData.js'
import { ref, nextTick } from 'vue'
import { tagData, categoryData, breadcrumbsData, PostData, CommentData } from '@/Interfaces/PaginatedData'
import PostForm from '@/Shared/Forms/PostForm.vue'
import { Inertia } from '@inertiajs/inertia'

interface Props {
    model: {
        categories: Array<categoryData>,
        tags: Array<tagData>,
        post?: PostData | null,
        comments?: Array<CommentData>
    }
}
const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(!props.model.post ? postCreateBreadcrumbs : postEditBreadcrumbs)

const title = props.model.post ? 'Edit' : 'Create'

const statusChanged = async ({id, status}) => {
    await nextTick()
    // console.log(usePage().url.value)
    Inertia.post('/admin/comments/status', { id: id, status: status }, {
        // onStart: () => loading.value = true,
        // onFinish: () => Inertia.reload({ only: ['comments'] }),
        preserveScroll: true
    })

 }

</script>


<template>
    <Head :title="`Blog Post - ${ title }`" />
    <Breadcrumbs :data="breadcrumbs" />
    <PostForm :data="props.model" @status="statusChanged" />
</template>
