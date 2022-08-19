<script setup lang="ts">

import { SortingData, tablePagination, userData } from '@/Interfaces/PaginatedData'
import { PaginatedUser } from '@/Interfaces/PaginatedUserData';
import { useQuasar } from 'quasar'
import { ref, computed, reactive, nextTick } from 'vue'
import { trans } from 'laravel-vue-i18n';
import { Inertia, PageProps } from '@inertiajs/inertia'

interface Props {
    paginatedData: PaginatedUser,
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

const rowStatuses = reactive({})

props.paginatedData.data.forEach((item, idx) => {
    rowStatuses[`${item.id}`] = ref(`${item.status}`)
})

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
    field: string | ((row: userData) => string | number);
    required?: boolean;
    align?: "left" | "right" | "center";
    sortable?: boolean;
    style?: string;
}

const columns:tableColumns[] = [
    {
        name: 'userAvatar',
        required: true,
        label: trans('Avatar'),
        align: 'center',
        sortable: false,
        field: 'avatar',
    },
    {
        name: 'userFirstname',
        required: true,
        label: trans('First name'),
        align: 'left',
        sortable: true,
        field: (row : userData) => row.first_name,
        // style: "width: 30%"
    },
    {
        name: 'userLastname',
        required: true,
        label: trans('Last name'),
        align: 'left',
        sortable: true,
        field: (row : userData) => row.last_name,
        // style: "width: 30%"
    },
    {
        name: 'userRole',
        required: true,
        label: trans('Role'),
        align: 'center',
        sortable: false,
        field: (row : userData) => row.roles,
        style: "width: 10%"
    },
    {
        name: 'userStatus',
        required: true,
        label: trans('Status'),
        align: 'center',
        sortable: true,
        field: (row : userData) => row.status,
        style: "width: 15%"
    },
    {
        name: 'userRegistered',
        required: true,
        label: trans('Registered at'),
        align: 'left',
        sortable: true,
        field: (row : userData) => row.registered_at
    },
    {
        name: 'userLastLoggedin',
        required: true,
        label: trans('Last Logged-in'),
        align: 'left',
        sortable: true,
        field: (row : userData) => row.last_login
    },
    {
        name: 'userPostcount',
        required: true,
        label: trans('Posts count'),
        align: 'center',
        sortable: true,
        field: (row : userData) => row.posts_count
    },
    {
        name: 'userActions',
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
    Inertia.get('/admin/users', { per_page: rowsPerPage, page: page, [sortBy]: descending ? "asc" : "desc", column: sortBy, descending: descending, search: filter })
    pagination.value.page = page
    pagination.value.rowsPerPage = rowsPerPage
    pagination.value.sortBy = sortBy
    pagination.value.descending = descending
}

const reset = () => {
    loading.value = true
    loadingResetButton.value = true
    Inertia.get('/admin/users')
}

const editPost = (row: Object) => {
    let postSlug = row['slug']
    Inertia.get(`/admin/posts/${postSlug}/edit`)
}

const deletePost = (row: Object) => {
    let postSlug = row['slug']
    loading.value = true
    Inertia.delete(`/admin/posts/${postSlug}`, {
        onSuccess: () => onDeleteSuccess(),
        onError: () => onDeleteFail()
    })
}

const onDeleteFail = () => {
    $q.notify({
        type: 'negative',
        message: "Something went wrong with post deletion"
    })
}

const onDeleteSuccess = () => {
    $q.notify({
        type: 'positive',
        message: "The post have been deleted successfully"
    })
}

const addPost = () => {
    Inertia.get('/admin/posts/create')
}

const statusChanged = async (id) => {
    await nextTick()
    Inertia.post('/admin/users/status', { id: id, status: rowStatuses[id] }, {
        onStart: () => loading.value = true,
        onFinish: () => loading.value = false,
        preserveScroll: true
    })
}

</script>

<template>
    <div class="q-mt-md">
        <q-table :title="$t('Users')" :row-key="row => row.id" :columns="columns" :rows="rows"
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
            <template v-slot:body-cell-userActions="props">
                <q-td :props="props" auto-width>
                    <div class="row flex-center q-gutter-x-sm no-wrap">
                        <template v-if="props.row.status === 'pending' || props.row.roles === 'admin' || props.row.roles === 'super-admin'">
                            <q-btn outline color="accent" icon="edit" :disable="loading" @click="editPost(props.row)" data-test="edit-button">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ $t('Edit user') }}
                                </q-tooltip>
                            </q-btn>
                        </template>
                        <template v-else>
                            <q-btn outline color="secondary" icon="visibility" :disable="loading" @click="editPost(props.row)" data-test="edit-button">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ $t('View user') }}
                                </q-tooltip>
                            </q-btn>
                        </template>
                        <template v-if="props.row.status === 'pending' || props.row.roles === 'admin'">
                            <q-btn outline color="red" icon="delete" :disable="loading" @click="deletePost(props.row)" data-test="delete-button">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ $t('Delete user') }}
                                </q-tooltip>
                            </q-btn>
                        </template>
                        <template v-else>
                            <q-btn outline color="grey" icon="delete" disable data-test="edit-button">
                                <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                    {{ $t('View user') }}
                                </q-tooltip>
                            </q-btn>
                        </template>

                        <!-- <q-btn outline color="red" icon="delete" :disable="loading" @click="deletePost(props.row)" data-test="delete-button">
                            <q-tooltip :delay="1000" anchor="bottom middle" self="center middle">
                                {{ $t('Delete user') }}
                            </q-tooltip>
                        </q-btn> -->
                    </div>
                </q-td>
            </template>
            <template v-slot:body-cell-userAvatar="props">
                <template v-if="!props.row.avatar">
                    <q-td :props="props" auto-width>
                        <q-avatar size="40px" color="orange" class="text-white">{{ props.row.first_name[0] + props.row.last_name[0] }}</q-avatar>
                    </q-td>
                </template>
                <template v-else>
                    <q-td :props="props" auto-width>
                        <q-avatar size="40px">
                            <q-img height="40px" :src="props.row.avatar" />
                        </q-avatar>
                    </q-td>
                </template>
            </template>

            <template v-slot:body-cell-userPostcount="props">
                <q-td :props="props" auto-width>
                    <span class="text-primary text-subtitle1">
                        {{ props.row.posts_count }}
                    </span>
                </q-td>
            </template>

            <template v-slot:body-cell-userRole="props">
                <q-td :props="props" auto-width>
                    <q-badge outline :class="[
                        (props.row.roles === 'author') ? 'text-green' :
                        (props.row.roles === 'member') ? 'text-primary' :
                        (props.row.roles === 'admin') ? 'text-accent' :
                        'text-red']" :label="props.row.roles" />
                </q-td>
            </template>

            <template v-slot:body-cell-userStatus="props">
                <q-td :props="props" auto-width>
                    <template v-if="props.row.status === 'pending'">
                        <q-toggle
                            :label="rowStatuses[`${props.row.id}`]"
                            color="red"
                            class="text-red"
                            false-value="pending"
                            true-value="enabled"
                            @update:model-value="statusChanged(props.row.id)"
                            keep-color
                            v-model="rowStatuses[`${props.row.id}`]"
                        />
                    </template>
                    <template v-else>
                        <q-toggle
                            :label="rowStatuses[`${props.row.id}`]"
                            color="primary"
                            class="text-primary"
                            false-value="disabled"
                            true-value="enabled"
                            @update:model-value="statusChanged(props.row.id)"
                            keep-color
                            :disable="!!(props.row.roles === 'super-admin')"
                            v-model="rowStatuses[`${props.row.id}`]"
                        />
                    </template>
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
                <div class="q-table__title text-primary">{{ $t('Users') }}</div>
                <q-space />
                <q-input clear-icon="close" dense debounce="300" label-color="primary" v-model="filter" label-slot data-test="search-input">
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
                <q-btn color="green" unelevated @click="addAdminUser" data-test="add-button">
                    <q-icon left name="post_add" />
                    <div>{{ $t('ADD ADMIN USER') }}</div>
                </q-btn>
            </template>
            <template v-slot:loading>
                <q-inner-loading showing color="primary" />
            </template>
        </q-table>
    </div>
</template>
