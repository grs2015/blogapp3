<script setup lang="ts">

import { formatDistanceToNow, compareDesc } from 'date-fns'
import { RecentUser } from '@/Interfaces/DashboardData';

import { trans } from 'laravel-vue-i18n';

interface Props {
    data: Array<RecentUser>
}

const props = defineProps<Props>()

type tableColumns = {
    name: string;
    label: string;
    field: string | ((row: RecentUser) => string | number );
    required?: boolean;
    align?: "left" | "right" | "center";
    sortable?: boolean;
    style?: string;
}

const columns:tableColumns[] = [
    {
        name: 'userFullName',
        required: true,
        label: trans('Full name'),
        align: 'left',
        sortable: false,
        field: (row : RecentUser) => row.full_name,
        style: "width: 100%"
    },
    {
        name: 'userRoles',
        required: true,
        label: trans('Roles'),
        align: 'left',
        sortable: false,
        field: (row : RecentUser) => row.roles,
    },
    {
        name: 'userCreatedAt',
        required: true,
        label: trans('Created at'),
        align: 'left',
        sortable: false,
        field: (row : RecentUser) => row.created_at,
    },
]

</script>

<template>
    <q-table dense
        table-header-class="text-primary bg-blue-1"
        :row-key="row => row.id" :columns="columns" :rows="props.data"
        :rows-per-page-options="[0]"
        hide-pagination hide-bottom flat color="primary" >
        <template v-slot:body-cell-userCreatedAt="props">
            <q-td :props="props" auto-width>
                <span class="q-mr-sm">
                    {{ props.row.created_at }}
                </span>
                <template v-if="props.row.status === 'pending'">
                    <q-badge align="top" :class="[compareDesc(new Date(props.row.created_at), new Date()) === -1 ? 'text-green' : 'text-red']" outline>
                        <template v-if="compareDesc(new Date(props.row.created_at), new Date()) === -1">
                            + {{ formatDistanceToNow(new Date(props.row.created_at)) }}
                        </template>
                        <template v-else>
                            - {{ formatDistanceToNow(new Date(props.row.created_at)) }}
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
