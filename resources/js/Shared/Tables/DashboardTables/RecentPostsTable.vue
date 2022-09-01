<script setup lang="ts">

import { formatDistanceToNow, compareDesc } from 'date-fns'
import { RecentPost, UserData} from '@/Interfaces/DashboardData';
import { Status } from '@/Interfaces/PaginatedData';
import { trans } from 'laravel-vue-i18n';

interface Props {
    data: Array<RecentPost>
}

const props = defineProps<Props>()

type tableColumns = {
name: string;
label: string;
field: string | ((row: RecentPost) => string | number | UserData | Status);
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
        sortable: false,
        field: (row : RecentPost) => row.title,
        style: "width: 100%"
    },
    {
        name: 'postAuthor',
        required: true,
        label: trans('Author'),
        align: 'left',
        sortable: false,
        field: (row : RecentPost) => row.user.full_name,
    },
    {
        name: 'postPublishedAt',
        required: true,
        label: trans('To be published at'),
        align: 'left',
        sortable: false,
        field: (row : RecentPost) => row.published_at,
    },
]

</script>

<template>
    <q-table dense
        table-header-class="text-primary bg-blue-1"
        :row-key="row => row.id" :columns="columns" :rows="props.data"
        :rows-per-page-options="[0]"
        hide-pagination hide-bottom flat color="primary" >
        <template v-slot:body-cell-postPublishedAt="props">
            <q-td :props="props" auto-width>
                    <span class="q-mr-sm">
                        {{ props.row.published_at }}
                    </span>
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
                <span v-else class="text-grey">
                    <q-badge outline class="text-grey">
                        {{ $t('no date set') }}
                    </q-badge>
                </span>
            </q-td>
        </template>
    </q-table>
</template>
