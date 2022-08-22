<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { userCreateBreadcrumbs, userEditBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { breadcrumbsData, userData } from '@/Interfaces/PaginatedData'
import UserForm from '@/Shared/Forms/UserForm.vue'

interface Props {
    model: {
        user?: userData | null
    }
}
const props = defineProps<Props>()
const breadcrumbs = ref<breadcrumbsData[]>(!props.model.user ? userCreateBreadcrumbs : userEditBreadcrumbs)
const title = props.model.user ? 'Edit' : 'Create'

</script>

<template>
    <Head :title="`Blog User - ${ title }`" />
    <Breadcrumbs :data="breadcrumbs" />
    <UserForm :data="props.model" />
</template>
