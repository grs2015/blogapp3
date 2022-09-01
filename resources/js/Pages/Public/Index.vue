<script lang="ts">
    export default {
        layout: Layout

    // layout: (h, page) => h(Layout, () => child),
}
</script>

<script setup lang="ts">

import Layout from '@/Shared/PublicLayout.vue'
import { Head } from '@inertiajs/inertia-vue3'
import { Paginated } from '@/Interfaces/PaginatedData'
import PublishedPosts from '@/Shared/Lists/PublishedPosts.vue'
import { Inertia } from '@inertiajs/inertia';
import { computed } from 'vue'

interface Props {
    model: {
        posts: Paginated,
    }
}

const props = defineProps<Props>()

const data = computed(() => props.model.posts)
const currentPage = computed(() => props.model.posts.current_page)
// const min = computed(() => props.model.posts.from)
const max = computed(() => props.model.posts.last_page)

const paginate = (value) => { Inertia.get('/', { per_page: 10, page: value }) }
</script>

<template>
    <Head title="Actual posts" />

        <div class="q-mt-xl">
            <PublishedPosts :paginatedData="data" />
            <div class="q-pa-lg flex flex-center">
                <q-pagination
                    v-model="currentPage"
                    :max=max
                    @update:model-value="paginate"
                    unelevated
                    color="teal" />
            </div>
        </div>

</template>


