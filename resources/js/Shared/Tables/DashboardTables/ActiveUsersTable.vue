<script setup lang="ts">

import { ActiveUser } from '@/Interfaces/DashboardData'
import { trans } from 'laravel-vue-i18n';
import { formatDistanceToNow, compareDesc } from 'date-fns'

interface Props {
    data: Array<ActiveUser>
}

const props = defineProps<Props>()

type tableColumns = {
    name: string;
    label: string;
    field: string | ((row: ActiveUser) => string | number );
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
        field: (row : ActiveUser) => row.full_name,
        style: "width: 100%"
    },
    {
        name: 'userPost',
        required: true,
        label: trans('Posts'),
        align: 'left',
        sortable: false,
        field: (row : ActiveUser) => row.posts_count,
    },
    {
        name: 'userLastLogin',
        required: true,
        label: trans('Last Log-in'),
        align: 'left',
        sortable: false,
        field: (row : ActiveUser) => row.last_login,
    },
]

</script>


<template>
    <q-table dense
        table-header-class="text-primary bg-blue-1"
        :row-key="row => row.id" :columns="columns" :rows="props.data"
        :rows-per-page-options="[0]"
        hide-pagination hide-bottom flat color="primary" >
        <template v-slot:body-cell-userPost="props">
            <q-td :props="props" auto-width class="bg-green-1">
                <span class="text-primary text-weight-medium">
                    {{ props.row.posts_count }}
                </span>
            </q-td>
        </template>
        <template v-slot:body-cell-userLastLogin="props">
            <q-td :props="props" auto-width>
                <span class="q-mr-sm">
                    {{ props.row.last_login }}
                </span>
                <q-badge align="top" :class="[compareDesc(new Date(props.row.last_login), new Date()) === -1 ? 'text-green' : 'text-red']" outline>
                    <template v-if="compareDesc(new Date(props.row.last_login), new Date()) === -1">
                        + {{ formatDistanceToNow(new Date(props.row.last_login)) }}
                    </template>
                    <template v-else>
                        - {{ formatDistanceToNow(new Date(props.row.last_login)) }}
                    </template>
                </q-badge>
            </q-td>
        </template>
    </q-table>
</template>
