<script setup lang="ts">

import { SortingData, tablePagination, categoryData } from '@/Interfaces/PaginatedData'
import { PaginatedCategory } from '@/Interfaces/PaginatedCatData';
import { useQuasar } from 'quasar'
import { ref, computed } from 'vue'
import { trans } from 'laravel-vue-i18n';
import { Inertia, PageProps } from '@inertiajs/inertia'

interface Props {
    paginatedData: PaginatedCategory,
    sortingData: SortingData
}


const props = defineProps<Props>()
const $q = useQuasar()

const rows = computed(() => {
    loading.value = false
    return props.paginatedData.data
})
const filter = ref('')
const loading = ref(false)
const loadingResetButton = ref(false)

const pagination = ref<tablePagination>({
    sortBy: props.sortingData.column,
    descending: props.sortingData.descending === 'true',
    page: props.paginatedData.current_page,
    rowsPerPage: props.paginatedData.per_page,
    rowsNumber: props.paginatedData.total
})

type tableColumns = {
    name: string;
    label: string;
    field: string | ((row: categoryData) => string);
    required?: boolean;
    align?: "left" | "right" | "center";
    sortable?: boolean;
    style?: string;
}

const columns: tableColumns[] = [
    {
        name: 'catIcon',
        required: true,
        label: trans('Category Icon'),
        align: 'center',
        sortable: true,
        field: (row: categoryData) => row.icon
    },
    {
        name: 'catTitle',
        required: true,
        label: trans('Title'),
        align: 'left',
        sortable: true,
        field: (row: categoryData) => row.title,
        style: "width: 30%"
    },
    {
        name: 'catContent',
        required: true,
        label: trans('Content'),
        align: 'left',
        sortable: true,
        field: (row: categoryData) => row.content,
    },
    {
        name: 'catActions',
        required: true,
        label: trans('Actions'),
        align: 'center',
        field: 'actions'
    }
]

const onRequest = (props: { pagination: tablePagination, filter: string }) => {
    loading.value = true
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter
    Inertia.get('/admin/categories', { per_page: rowsPerPage, page: page, [sortBy]: descending ? "asc" : "desc", column: sortBy, descending: descending, search: filter })
    pagination.value.page = page
    pagination.value.rowsPerPage = rowsPerPage
    pagination.value.sortBy = sortBy
    pagination.value.descending = descending
}

const reset = () => {
    loading.value = true
    loadingResetButton.value = true
    Inertia.get('/admin/categories')
}

const editCategory = (row: Object) => {
    let catSlug = row['slug']
    Inertia.get(`/admin/categories/${catSlug}/edit`)
}

const deleteCategory = (row: Object) => {
    let catSlug = row['slug']
    loading.value = true
    Inertia.delete(`/admin/categories/${catSlug}`, {
        onSuccess: () => onDeleteSuccess(),
        onError: () => onDeleteFail()
    })
}

const onDeleteFail = () => {
    $q.notify({
        type: 'negative',
        message: trans("Something went wrong with category deletion")
    })
}

const onDeleteSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans("The category have been deleted successfully")
    })
}

const addPost = () => {
    Inertia.get('/admin/categories/create')
}

</script>

<template>
    <div class="q-mt-md">
        <q-table :title="$t('Categories')" :row-key="row => row.id" :columns="columns" :rows="rows"
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
            <template v-slot:no-data>
                <div class="full-width row flex-center text-primary q-gutter-sm">
                    <q-icon size="2em" name="sentiment_dissatisfied" color="red" />
                    <span class="text-red">
                        {{ $t('No data available') }}
                    </span>
                </div>
            </template>
            <template v-slot:body-cell-catActions="props">
                <q-td :props="props" auto-width>
                    <div class="row flex-center q-gutter-x-sm no-wrap">
                        <q-btn outline color="accent" icon="edit" :disable="loading" @click="editCategory(props.row)"
                            data-test="edit-button">
                            <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                {{ $t('Edit category') }}
                            </q-tooltip>
                        </q-btn>
                        <q-btn outline color="red" icon="delete" :disable="loading" @click="deleteCategory(props.row)"
                            data-test="delete-button">
                            <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                {{ $t('Delete category') }}
                            </q-tooltip>
                        </q-btn>
                    </div>
                </q-td>
            </template>
            <template v-slot:loading>
                <q-inner-loading showing color="accent" />
            </template>
            <template v-slot:body-cell-catIcon="props">
                <q-td :props="props" auto-width>
                    <q-icon size="md" :color="props.row.color" :name="props.row.icon" />
                </q-td>
            </template>
            <template v-slot:top>
                <div class="q-table__title text-primary">{{ $t('Categories') }}</div>
                <q-space />
                <q-input clear-icon="close" dense debounce="300" label-color="primary" v-model="filter" label-slot
                    data-test="search-input">
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
                    data-test="refresh-button" :loading="loadingResetButton" icon="restart_alt" />
                <q-separator vertical spaced inset />
                <q-btn color="green" unelevated @click="addPost" data-test="add-button">
                    <q-icon left name="post_add" />
                    <div>{{ $t('Add category') }}</div>
                </q-btn>
            </template>
            <template v-slot:pagination="scope">
                <q-btn v-if="scope.pagesNumber > 2" icon="first_page" color="grey-8" round dense flat
                    :disable="scope.isFirstPage" @click="scope.firstPage" />

                <q-btn icon="chevron_left" color="grey-8" round dense flat :disable="scope.isFirstPage"
                    @click="scope.prevPage" />

                <q-btn icon="chevron_right" color="grey-8" round dense flat :disable="scope.isLastPage"
                    @click="scope.nextPage" />

                <q-btn v-if="scope.pagesNumber > 2" icon="last_page" color="grey-8" round dense flat
                    :disable="scope.isLastPage" @click="scope.lastPage" />
            </template>
        </q-table>
    </div>
</template>
