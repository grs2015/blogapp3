<script setup lang="ts">

import { ref, computed, reactive, nextTick } from 'vue'
import { trans } from 'laravel-vue-i18n';
import { Inertia, PageProps } from '@inertiajs/inertia'
import { PostData, LinkData, tablePagination } from '@/Interfaces/PaginatedData';
import { useQuasar } from 'quasar'
import { format, formatDistanceToNow, compareDesc } from 'date-fns'
import { usePage } from '@inertiajs/inertia-vue3';

interface Paginated {
    paginatedData: {
        current_page: number;
        data: Array<PostData>;
        first_page_url: string;
        from: number;
        last_page: number;
        last_page_url: string;
        links: Array<LinkData>;
        next_page_url: string | null;
        path: string;
        per_page: number;
        prev_page_url: string | null;
        to: number;
        total: number;
    },
    sortingData: {
        column: string;
        descending: string;
    }
}

interface pageActions extends PageProps
{
    sorting: {
        column: string;
        descending: string;
    }
}

const props = defineProps<Paginated>()
const $q = useQuasar()

const rows = computed(() => {
    loading.value = false
    props.paginatedData.data.forEach((item, idx) => {
        rowStatuses[`${item.id}`] = ref(`${item.status}`)
    })
    return props.paginatedData.data
})
const filter = ref<string>(usePage().props.value.search as string)
const loading = ref(false)
const loadingResetButton = ref(false)

const rowStatuses = reactive({})

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
    field: string | ((row: PostData) => string | number);
    required?: boolean;
    align?: "left" | "right" | "center";
    sortable?: boolean;
    style?: string;
}

const columns:tableColumns[] = [
    {
        name: 'postTitle',
        required: true,
        label: trans('Title'),
        align: 'left',
        sortable: true,
        field: (row : PostData) => row.title,
        style: "width: 35%"

    },
    {
        name: 'postPublishedAt',
        required: true,
        label: trans('To be published at'),
        align: 'left',
        sortable: true,
        field: (row : PostData) => row.published_at,
        style: "width: 20%"
    },
    {
        name: 'postStatus',
        required: true,
        label: trans('Status'),
        align: 'center',
        sortable: true,
        field: (row : PostData) => row.status,
        // style: "width: 20%"
    },
    {
        name: 'postFavorite',
        required: true,
        label: trans('Favorite'),
        align: 'center',
        sortable: true,
        field: (row : PostData) => row.favorite,
        // style: "width: 15%"
    },
    {
        name: 'postRating',
        required: true,
        label: trans('Rating'),
        align: 'center',
        sortable: true,
        field: (row : PostData) => row.rating
    },
    {
        name: 'postViews',
        required: true,
        label: trans('Views'),
        align: 'center',
        sortable: true,
        field: (row : PostData) => row.views
    },
    {
        name: 'postComments',
        required: true,
        label: trans('Comments'),
        align: 'center',
        sortable: true,
        field: (row : PostData) => row.comments_count
    },
    {
        name: 'postActions',
        required: true,
        label: trans('Actions'),
        align: 'center',
        field: 'actions'
    }
]

const onRequest = (props: { pagination : tablePagination, filter: string }) => {
    loading.value = true
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter
    Inertia.get('/author/posts', { per_page: rowsPerPage, page: page, [sortBy]: descending ? "asc" : "desc", column: sortBy, descending: descending, search: filter })
    pagination.value.page = page
    pagination.value.rowsPerPage = rowsPerPage
    pagination.value.sortBy = sortBy
    pagination.value.descending = descending
}

const reset = () => {
    loading.value = true
    loadingResetButton.value = true
    Inertia.get('/author/posts')
}

const editPost = (row: PostData) => {
    let postSlug = row['slug']
    Inertia.get(`/author/posts/${postSlug}/edit`)
}

const viewPost = (row: PostData) => {
    let postSlug = row['slug']
    Inertia.get(`/author/posts/${postSlug}`)
}

const deletePost = (row: PostData) => {
    let postSlug = row['slug']
    loading.value = true
    Inertia.delete(`/author/posts/${postSlug}`, {
        onSuccess: () => onDeleteSuccess(),
        onError: () => onDeleteFail()
    })
}

const onDeleteFail = () => {
    $q.notify({
        type: 'negative',
        message: trans("Something went wrong with post deletion")
    })
}

const onDeleteSuccess = () => {
    $q.notify({
        type: 'positive',
        message: trans("The post have been deleted successfully")
    })
}

const addPost = () => {
    Inertia.get('/author/posts/create')
}

const statusChanged = async (id) => {
    await nextTick()
    Inertia.post('/author/posts/status', { id: id, status: rowStatuses[id], page: props.paginatedData.current_page, per_page: props.paginatedData.per_page, search: filter.value }, {
        onStart: () => loading.value = true,
        onFinish: () => loading.value = false,
        preserveScroll: true
    })
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
            <template v-slot:no-data>
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
                        <template v-if="props.row.status === 'draft' || props.row.status === 'pending'">
                            <q-btn outline color="accent" icon="edit" :disable="loading" @click="editPost(props.row)" data-test="edit-button">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ $t('Edit post') }}
                                </q-tooltip>
                            </q-btn>
                        </template>
                        <template v-else>
                            <q-btn outline color="secondary" icon="visibility" :disable="loading" @click="viewPost(props.row)" data-test="view-button">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ $t('View post') }}
                                </q-tooltip>
                            </q-btn>
                        </template>
                        <template v-if="props.row.status === 'draft' || props.row.status === 'pending'">
                            <q-btn outline color="red" icon="delete" :disable="loading" @click="deletePost(props.row)" data-test="delete-button">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ $t('Delete post') }}
                                </q-tooltip>
                            </q-btn>
                        </template>
                        <template v-else>
                            <q-btn outline color="grey" icon="delete" disable data-test="delete-button">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ $t('Delete post') }}
                                </q-tooltip>
                            </q-btn>
                        </template>

                    </div>
                </q-td>
            </template>
            <template v-slot:body-cell-postRating="props">
                <q-td :props="props" auto-width>
                    <span class="text-primary text-subtitle1">
                        {{ props.row.rating }}
                    </span>
                </q-td>
            </template>
            <template v-slot:body-cell-postViews="props">
                <q-td :props="props" auto-width>
                    <span class="text-primary text-subtitle1">
                        {{ props.row.views }}
                    </span>
                </q-td>
            </template>
            <template v-slot:body-cell-postComments="props">
                <q-td :props="props" auto-width>
                    <span class="text-primary text-subtitle1">
                        {{ props.row.comments_count }}
                    </span>
                </q-td>
            </template>
            <template v-slot:body-cell-postFavorite="props">
                <q-td :props="props" auto-width>
                    <template v-if="props.row.favorite === 'usual'">
                        <q-icon color="grey-4" name="star_border" size="24px"/>
                    </template>
                    <template v-else>
                        <q-icon color="green" name="star" size="24px"/>
                    </template>
                </q-td>
            </template>
            <template v-slot:body-cell-postStatus="props">
                <q-td :props="props" auto-width>
                    <template v-if="props.row.status === 'draft'">
                        <q-toggle
                            color="green"
                            checked-icon="hourglass_empty"
                            unchecked-icon="mode_edit"
                            false-value="draft"
                            true-value="pending"
                            @update:model-value="statusChanged(props.row.id)"
                            v-model="rowStatuses[`${props.row.id}`]"
                        >
                            <q-tooltip anchor="bottom middle" self="center middle" :delay="500">
                                {{ $t('Draft > Pending') }}
                            </q-tooltip>
                        </q-toggle>
                    </template>
                    <template v-else-if="props.row.status === 'pending'">
                        <q-icon color="grey" name="schedule" size="24px">
                            <q-tooltip anchor="bottom middle" self="center middle" :delay="500">
                                {{ $t('Pending') }}
                            </q-tooltip>
                        </q-icon>
                    </template>
                    <template v-else-if="props.row.status === 'published'">
                        <q-icon color="green" name="visibility" size="24px">
                            <q-tooltip anchor="bottom middle" self="center middle" :delay="500">
                                {{ $t('Published') }}
                            </q-tooltip>
                        </q-icon>
                    </template>
                    <template v-else>
                        <q-icon color="grey" name="visibility_off" size="24px">
                            <q-tooltip anchor="bottom middle" self="center middle" :delay="500">
                                {{ $t('Unpublished') }}
                            </q-tooltip>
                        </q-icon>
                    </template>
                </q-td>
            </template>
            <template v-slot:body-cell-postPublishedAt="props">
                <q-td :props="props" auto-width>
                    <span v-if="props.row.published_at">
                        {{ props.row.published_at }}
                        <template v-if="props.row.status === 'pending'">
                            <q-badge align="top" :class="[compareDesc(new Date(props.row.published_at), new Date()) === -1 ? 'text-green' : 'text-red']" outline>
                                <template v-if="compareDesc(new Date(props.row.published_at), new Date()) === -1">
                                    + {{ formatDistanceToNow(new Date(props.row.published_at)) }}
                                </template>
                                <template v-else>
                                    - {{ formatDistanceToNow(new Date(props.row.published_at)) }}
                                </template>
                            </q-badge>
                        </template>
                    </span>
                    <span v-else class="text-grey">
                        <q-badge outline class="text-grey">
                            {{ $t('no date set') }}
                        </q-badge>
                    </span>
                </q-td>
            </template>
            <template v-slot:top>
                <div class="q-table__title text-primary">{{ $t('My Posts') }}</div>
                <q-space />
                <q-input clearable clear-icon="close" dense debounce="300" label-color="primary" v-model="filter" label-slot data-test="search-input">
                    <template v-slot:append>
                        <q-icon name="search" color="primary" />
                    </template>
                    <template v-slot:label>
                        <div>
                            {{ $t('Search') }}
                        </div>
                    </template>
                </q-input>
                <q-btn class="q-ml-md" color="primary" outline :disable="loading" @click="reset" data-test="refresh-button"
                    :loading="loadingResetButton" icon="restart_alt" />
                <q-separator vertical spaced inset />
                <q-btn color="green" unelevated @click="addPost" data-test="add-button" :disable="!usePage().props.value.can.create_post">
                    <q-icon left name="post_add" />
                    <div>{{ $t('ADD POST') }}</div>
                </q-btn>
            </template>
            <template v-slot:loading>
                <q-inner-loading showing color="primary" />
            </template>
        </q-table>
    </div>
</template>
