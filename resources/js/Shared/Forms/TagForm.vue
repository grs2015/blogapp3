<script setup lang="ts">

import { tagData } from '@/Interfaces/PaginatedData'

interface Props {
    data: {
        tag?: tagData | null
    }
}

const props = defineProps<Props>()


</script>

<template>
    <div>TagForm</div>
</template>
