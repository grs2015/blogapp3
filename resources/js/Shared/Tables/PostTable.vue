<script setup>

import { ref } from 'vue'
import { trans } from 'laravel-vue-i18n';
// import columns from '@/postTableColumns.js'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3';

const props = defineProps({ paginatedData: Object })

const rows = ref(props.paginatedData.data)
const filter = ref('')
const loading = ref(false)
const loadingResetButton = ref(false)
const pagination = ref({
    sortBy: usePage().props.value.sorting.column,
    descending: (usePage().props.value.sorting.descending === 'true'),
    page: props.paginatedData.current_page,
    rowsPerPage: props.paginatedData.per_page,
    rowsNumber: props.paginatedData.total
})

const columns = [
    {
        name: 'postTitle',
        required: true,
        label: trans('Title'),
        align: 'left',
        sortable: true,
        field: row => row.title,
        style: "width: 30%"
    },
    {
        name: 'postAuthor',
        required: true,
        label: trans('Author'),
        align: 'left',
        field: row => row.user.first_name
    },
    {
        name: 'postStatus',
        required: true,
        label: trans('Status'),
        align: 'right',
        sortable: true,
        field: row => row.status,

    },
    {
        name: 'postFavorite',
        required: true,
        label: trans('Favorite'),
        align: 'right',
        sortable: true,
        field: row => row.favorite
    },
    {
        name: 'postViews',
        required: true,
        label: trans('Views'),
        align: 'right',
        sortable: true,
        field: row => row.views
    },
    {
        name: 'postActions',
        required: true,
        label: trans('Actions'),
        align: 'center',
        field: 'actions'
    }
]

const onRequest = (props) => {
    loading.value = true
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter
    Inertia.get('/admin/posts', { per_page: rowsPerPage, page: page, [sortBy]: descending ? "asc" : "desc", column: sortBy, descending: descending, search: filter })
    pagination.value.page = page
    pagination.value.rowsPerPage = rowsPerPage
    pagination.value.sortBy = sortBy
    pagination.value.descending = descending

}

const reset = () => {
    loading.value = true
    loadingResetButton.value = true
    Inertia.get('/admin/posts')
}

</script>

<template>
    <div class="q-mt-md">
        <q-table :title="$t('Posts')" :row-key="row => row.id" :columns="columns" :rows="rows"
            v-model:pagination="pagination" :filter="filter" @request="onRequest" :loading="loading" color="primary"
            flat binary-state-sort :rows-per-page-label="$t('Records per page')"
            :rows-per-page-options="[5, 10, 15, props.paginatedData.total, 0]" bordered>
            <template v-slot:header="props">
                <q-tr :props="props">
                    <q-th v-for="col in props.cols" :key="col.name" :props="props" class="text-primary">
                        {{ col.label }}
                    </q-th>
                </q-tr>
            </template>
            <template v-slot:no-data="{ icon, message, filter }">
                <div class="full-width row flex-center text-primary q-gutter-sm">
                    <q-icon size="2em" name="sentiment_dissatisfied" color="red" />
                    <span class="text-red">
                        {{ $t('No data available') }}
                    </span>
                </div>
            </template>
            <template v-slot:body-cell-postActions="props">
                <q-td :props="props" auto-width>
                    <div class="row flex-center q-gutter-x-sm no-wrap">
                        <q-btn outline color="accent" icon="edit">
                            <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                {{ $t('Edit post') }}
                            </q-tooltip>
                        </q-btn>
                        <q-btn outline color="red" icon="delete">
                            <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                {{ $t('Delete post') }}
                            </q-tooltip>
                        </q-btn>
                    </div>
                </q-td>
            </template>
            <template v-slot:body-cell-postViews="props">
                <q-td :props="props" auto-width>
                    {{ props.row.views }}
                </q-td>
            </template>
            <template v-slot:body-cell-postFavorite="props">
                <q-td :props="props" auto-width>
                    {{ props.row.favorite }}
                </q-td>
            </template>
            <template v-slot:body-cell-postStatus="props">
                <q-td :props="props" auto-width>
                    {{ props.row.status }}
                </q-td>
            </template>
            <template v-slot:top>
                <div class="q-table__title text-primary">{{ $t('Posts') }}</div>
                <q-space />
                <q-input clear-icon="close" dense debounce="300" label-color="primary" v-model="filter" label-slot>
                    <template v-slot:append>
                        <q-icon name="search" color="primary" />
                    </template>
                    <template v-slot:label>
                        <div>
                            {{ $t('Search') }}
                        </div>
                    </template>
                </q-input>
                <q-btn class="q-ml-md" color="primary" outline :disable="loading" @click="reset"
                    :loading="loadingResetButton" icon="restart_alt" />
                <q-separator vertical spaced inset />
                <q-btn color="green" unelevated>
                    <q-icon left name="post_add" />
                    <div>{{ $t('ADD POST') }}</div>
                </q-btn>
            </template>
        </q-table>
    </div>
</template>
