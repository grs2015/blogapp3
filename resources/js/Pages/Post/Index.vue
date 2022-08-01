<template>
    <Head title="Blog posts" />
    <Breadcrumbs :data="breadcrumbsData" />
    <PostTable :paginatedData="model.posts" />
</template>

<script setup lang="ts">

// import { Inertia } from '@inertiajs/inertia'
import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import PostTable from '@/Shared/Tables/PostTable.vue'
import { postsBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'

defineProps({ model: Object })

const breadcrumbsData = ref(postsBreadcrumbs)

</script>
