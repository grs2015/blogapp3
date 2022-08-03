<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import PostTable from '@/Shared/Tables/PostTable.vue'
import { postsBreadcrumbs } from '@/breadcrumbsData.js'
import { ref, Ref } from 'vue'
import { Paginated, SortingData } from '@/Interfaces/PaginatedData'

interface Props {
    model: {
        posts: Paginated,
        sorting: SortingData
    }
}

type breadType = Ref<({label: string; icon: string; route: string; } | {label: string; icon: string; route?: undefined;})[]>

const props = defineProps<Props>()

const breadcrumbsData = ref(postsBreadcrumbs)

</script>

<template>
    <Head title="Blog posts" />
    <Breadcrumbs :data="breadcrumbsData" />
    <PostTable :paginatedData="props.model.posts" :sortingData="props.model.sorting"/>
</template>
