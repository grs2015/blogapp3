<script setup lang="ts">

import { categoryData } from '@/Interfaces/PaginatedData'

interface Props {
    paginatedData: Array<categoryData>
}

const props = defineProps<Props>()

</script>

<template>
    CategoryTable
</template>
