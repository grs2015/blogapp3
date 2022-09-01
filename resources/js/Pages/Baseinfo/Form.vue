<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { settingsBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { breadcrumbsData, baseinfoData } from '@/Interfaces/PaginatedData'

import SettingsForm from '@/Shared/Forms/SettingsForm.vue'

interface Props {
    model: {
        baseinfo: baseinfoData
    }
}
const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(settingsBreadcrumbs)

</script>

<template>
    <Head title="Blog Settings" />
    <Breadcrumbs :data="breadcrumbs" />
    <SettingsForm :data="props.model" />
</template>


