<script lang="ts">

import Layout from '@/Shared/LayoutAuthor.vue'

export default {
    layout: Layout,
}

</script>

<script setup lang="ts">

import { DashboardPostData } from '@/Interfaces/DashboardData'
import { breadcrumbsData } from '@/Interfaces/PaginatedData'
import { dashboardBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import AuthorDashboardTable from '@/Shared/Tables/AuthorDashboardTable.vue'

interface Props {
    model: DashboardPostData
}

const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(dashboardBreadcrumbs)

</script>

<template>
    <Head title="Author dashboard" />
    <Breadcrumbs :data="breadcrumbs" />
    <AuthorDashboardTable :dashboardData="props.model" />
</template>
