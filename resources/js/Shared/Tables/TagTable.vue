<script setup lang="ts">

import { SortingData, tablePagination, tagData } from '@/Interfaces/PaginatedData'
import { PaginatedTag } from '@/Interfaces/PaginatedTagData';
import { useQuasar } from 'quasar'
import { ref, computed } from 'vue'
import { trans } from 'laravel-vue-i18n';
import { Inertia, PageProps } from '@inertiajs/inertia'

interface Props {
    paginatedData: PaginatedTag,
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
const selectedTags = ref<tagData[]>([])
const confirm = ref(false)

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
    field: string | ((row: tagData) => string);
    required?: boolean;
    align?: "left" | "right" | "center";
    sortable?: boolean;
    style?: string;
}

const columns: tableColumns[] = [
    {
        name: 'tagTitle',
        required: true,
        label: trans('Title'),
        align: 'left',
        sortable: true,
        field: (row: tagData) => row.title,
        style: "width: 30%"
    },
    {
        name: 'tagContent',
        required: true,
        label: trans('Content'),
        align: 'left',
        sortable: true,
        field: (row: tagData) => row.content,
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
    Inertia.get('/admin/tags', { per_page: rowsPerPage, page: page, [sortBy]: descending ? "asc" : "desc", column: sortBy, descending: descending, search: filter })
    pagination.value.page = page
    pagination.value.rowsPerPage = rowsPerPage
    pagination.value.sortBy = sortBy
    pagination.value.descending = descending
}

const reset = () => {
    loading.value = true
    loadingResetButton.value = true
    Inertia.get('/admin/tags')
}

const editTag = (row: Object) => {
    let tagSlug = row['slug']
    Inertia.get(`/admin/tags/${tagSlug}/edit`)
}

const deleteTag = (row: Object) => {
    let tagSlug = row['slug']
    loading.value = true
    Inertia.delete(`/admin/tags/${tagSlug}`, {
        onSuccess: () => onDeleteSuccess(),
        onError: () => onDeleteFail()
    })
}

const onDeleteFail = () => {
    $q.notify({
        type: 'negative',
        message: trans("Something went wrong with tag(s) deletion")
    })
}

const onDeleteSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans("The tag(s) have been deleted successfully")
    })
}

const addTag = () => {
    Inertia.get('/admin/tags/create')
}

const getSelectedString = (number) => selectedTags.value.length === 0 ? '' : `${selectedTags.value.length} record${selectedTags.value.length > 1 ? 's' : ''} selected of ${number}`

const massDelete = () => {
    // ids.value = selectedCats.value.map(cat => cat.id)
    Inertia.post('/admin/tagmassdelete', { data: selectedTags.value.map(tag => tag.id) }, {
        onStart: () => loading.value = true,
        onFinish: () => selectedTags.value.length = 0,
        onSuccess: () => onDeleteSuccess(),
        onError: () => onDeleteFail()
    })
}

</script>

<template>
    <div class="q-mt-md">
        <q-table :title="$t('Tags')" :row-key="row => row.id" :columns="columns" :rows="rows"
            v-model:pagination="pagination" :filter="filter" @request="onRequest" :loading="loading" color="primary"
            v-model:selected="selectedTags" selection="multiple" :selected-rows-label="getSelectedString"
            flat binary-state-sort :rows-per-page-label="$t('Records per page')"
            :rows-per-page-options="[5, 10, 15, props.paginatedData.total, 0]" bordered>
            <template v-slot:header-selection="scope">
                <q-checkbox color="primary" keep-color v-model="scope.selected" data-test="select-checkbox"/>
            </template>
            <template v-slot:header-cell="props">
                <q-th :props="props" class="text-primary">
                    {{ props.col.label }}
                </q-th>
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
                        <q-btn outline color="accent" icon="edit" :disable="loading" @click="editTag(props.row)"
                            data-test="edit-button">
                            <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                {{ $t('Edit tag') }}
                            </q-tooltip>
                        </q-btn>
                        <q-btn outline color="red" icon="delete" :disable="loading" @click="deleteTag(props.row)"
                            data-test="delete-button">
                            <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                {{ $t('Delete tag') }}
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
                    <div class="row rounded-borders flex-center color-box" :style="{background: props.row.color}">
                        <q-icon :name="props.row.icon" color="white" size="md"/>
                    </div>
                </q-td>
            </template>
            <template v-slot:top>
                <div class="q-table__title text-primary">{{ $t('Tags') }}</div>
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
                <q-btn color="green" unelevated @click="addTag" data-test="add-button">
                    <q-icon left name="post_add" />
                    <div>{{ $t('Add tag') }}</div>
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
            <template v-slot:bottom-row>
                <q-tr>
                    <q-td colspan="100%">
                        <div class="row justify-end">
                            <q-btn outline color="red" :disable="loading || selectedTags.length == 0" @click="confirm = true" data-test="massdelete-button">
                                <div>{{ $t('Delete selected') }}</div>
                            </q-btn>
                        </div>
                    </q-td>
                </q-tr>
            </template>
        </q-table>
    </div>
    <q-dialog v-model="confirm" persistent>
        <q-card>
            <q-card-section class="row items-center bg-red-1">
                <q-avatar icon="front_hand" color="negative" text-color="white" />
                <span class="q-ml-sm">{{ $t("You are going to delete selected tags, aren't you?") }}</span>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat :label="$t('Cancel')" color="primary" v-close-popup />
                <q-btn flat :label="$t('Delete')" color="negative" v-close-popup @click="massDelete" data-test="massdelete-confirm" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
