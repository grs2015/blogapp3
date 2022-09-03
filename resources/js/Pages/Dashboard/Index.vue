<script setup lang="ts">

import { usePage } from '@inertiajs/inertia-vue3';
import { DashboardData } from '@/Interfaces/DashboardData'
import { breadcrumbsData } from '@/Interfaces/PaginatedData'
import { dashboardBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import DashboardTable from '@/Shared/Tables/DashboardTable.vue'

interface Props {
    model: DashboardData
}

const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(dashboardBreadcrumbs)

</script>

<template>
    <Head title="Admin dashboard" />
    <Breadcrumbs :data="breadcrumbs" />
    <DashboardTable :dashboardData="props.model" />
</template>


