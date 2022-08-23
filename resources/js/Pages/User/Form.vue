<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { accountBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { breadcrumbsData, userData } from '@/Interfaces/PaginatedData'
import AccountForm from '@/Shared/Forms/AccountForm.vue'

interface Props {
    model: {
        user?: userData | null
    }
}
const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(accountBreadcrumbs)

</script>

<template>
    <Head title="User Account" />
    <Breadcrumbs :data="breadcrumbs" />
    <AccountForm :data="props.model" />
</template>
