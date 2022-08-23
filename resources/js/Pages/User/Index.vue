<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import UserTable from '@/Shared/Tables/UserTable.vue'
import { usersBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { SortingData, breadcrumbsData } from '@/Interfaces/PaginatedData'
import { PaginatedUser } from '@/Interfaces/PaginatedUserData'

interface Props {
    model: {
        users: PaginatedUser,
        sorting: SortingData
    }
}

const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(usersBreadcrumbs)

</script>

<template>
    <Head title="Blog users" />
    <Breadcrumbs :data="breadcrumbs" />
    <UserTable :paginatedData="props.model.users" :sortingData="props.model.sorting"/>
</template>
