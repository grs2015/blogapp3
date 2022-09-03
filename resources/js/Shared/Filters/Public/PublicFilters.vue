<script setup lang="ts">

import { categoryData, tagData } from '@/Interfaces/PaginatedData';
import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

interface Props {
    data: {
        cats: Array<categoryData>,
        tags: Array<tagData>
    }
}

const props = defineProps<Props>()
const groupCategory = ref([])
const groupTag = ref([])

const optionsCategory = props.data.cats.map(item => ({ label: item.title, value: item.id }))
const optionsTag = props.data.tags.map(item => ({ label: item.title, value: item.id }))

const filterPosts = () => {
    Inertia.post('/filters', { cat_ids: groupCategory.value, tag_ids: groupTag.value }, { preserveState: true, replace: true })
}
const filterReset = () => {
    groupTag.value = []
    groupCategory.value = []
    if ((usePage().component.value !== 'Public/Index')) {
        Inertia.get('/')
    }
}

</script>

<template>
    <q-form @submit="filterPosts" @reset="filterReset">
        <q-card flat bordered class="q-ma-md">
            <q-card-section>
                <div class="text-h6 text-bold">{{ $t('Categories:') }}</div>
                <q-option-group
                    v-model="groupCategory"
                    :options="optionsCategory"
                    color="teal"
                    type="checkbox"
                    keep-color dense
                />
            </q-card-section>
            <q-separator spaced inset />
            <q-card-section>
                <div class="text-h6 text-bold">{{ $t('Tags:') }}</div>
                <q-option-group
                    v-model="groupTag"
                    :options="optionsTag"
                    color="teal"
                    type="checkbox"
                    keep-color dense
                />
            </q-card-section>
            <q-card-actions>
                <q-btn type="submit" outline color="teal" class="full-width">{{ $t('Filter') }}</q-btn>
                <div v-if="groupTag.length != 0 || groupCategory.length != 0" class="q-mt-sm full-width">
                    <q-btn type="reset" outline color="negative" class="full-width">{{ $t('Filter Reset') }}</q-btn>
                </div>
            </q-card-actions>
        </q-card>
    </q-form>
</template>
