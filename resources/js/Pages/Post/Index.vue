<template>
    <Head title="Blog posts"></Head>
    <Breadcrumbs :data="breadcrumbsData" />
    {{ model }}
</template>

<script setup>

import { Inertia } from '@inertiajs/inertia'
import { Head, Link } from '@inertiajs/inertia-vue3'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import { postsBreadcrumbs } from '@/breadcrumbsData.js'
import { ref } from 'vue'

defineProps({ model: Object })

const breadcrumbsData = ref(postsBreadcrumbs)

</script>
