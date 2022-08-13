<script setup lang="ts">

import { SortingData, tablePagination, userData } from '@/Interfaces/PaginatedData'
import { PaginatedUser } from '@/Interfaces/PaginatedUserData';
import { useQuasar } from 'quasar'
import { ref, computed } from 'vue'
import { trans } from 'laravel-vue-i18n';
import { Inertia, PageProps } from '@inertiajs/inertia'

interface Props {
    paginatedData: PaginatedUser,
    sortingData: SortingData
}

const props = defineProps<Props>()

</script>

<template>
    GGG
</template>
