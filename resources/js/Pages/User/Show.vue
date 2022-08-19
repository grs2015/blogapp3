<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { profileBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { breadcrumbsData, userData } from '@/Interfaces/PaginatedData'
import ProfileForm from '@/Shared/Forms/ProfileForm.vue'

interface Props {
    model: {
        user?: userData | null
    }
}
const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(profileBreadcrumbs)

</script>

<template>
    <Head title="User Profile" />
    <Breadcrumbs :data="breadcrumbs" />
    <ProfileForm :data="props.model" />
</template>
