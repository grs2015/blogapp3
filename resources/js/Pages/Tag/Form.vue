<script setup lang="ts">

import { Head } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { tagCreateBreadcrumbs, tagEditBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'
import { tagData, breadcrumbsData } from '@/Interfaces/PaginatedData'
import TagForm from '@/Shared/Forms/TagForm.vue'

interface Props {
    model: {
        tag?: tagData | null
    }
}

const props = defineProps<Props>()

const breadcrumbs = ref<breadcrumbsData[]>(!props.model.tag ? tagCreateBreadcrumbs : tagEditBreadcrumbs)

const title = props.model.tag ? 'Edit' : 'Create'

</script>

<template>
    <Head :title="`Blog Tag - ${ title }`" />
    <Breadcrumbs :data="breadcrumbs" />
    <TagForm :data="props.model" />
</template>


