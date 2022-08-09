<script setup lang="ts">
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

interface breadcrumbsData {
    data: Array<{
        label: string,
        icon: string,
        route?: string
    }>

}

const props = defineProps<breadcrumbsData>()
const link = ref('/admin/posts')

</script>

<template>
    <Link as="div" :href="link">
        <q-breadcrumbs class="text-orange">
            <template #separator>
                <q-icon size="1.5em" name="chevron_right" color="primary" />
            </template>
            <template v-for="item in data" :key="item.label">
                <q-breadcrumbs-el :label="$t(item.label)" :icon="item.icon" :href="item.route" @click="link = item.route" />
            </template>
        </q-breadcrumbs>
    </Link>
</template>
