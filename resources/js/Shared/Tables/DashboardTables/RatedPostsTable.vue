<script setup lang="ts">

import { RatedPost, UserData } from '@/Interfaces/DashboardData'
import { Status } from '@/Interfaces/PaginatedData';
import { trans } from 'laravel-vue-i18n';

interface Props {
    data: Array<RatedPost>
}

const props = defineProps<Props>()

type tableColumns = {
name: string;
label: string;
field: string | ((row: RatedPost) => string | number | UserData | Status);
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
        field: (row : RatedPost) => row.title,
        style: "width: 100%"
    },
    {
        name: 'postRating',
        required: true,
        label: trans('Rating'),
        align: 'left',
        sortable: false,
        field: (row : RatedPost) => row.rating,
    },
    {
        name: 'postAuthor',
        required: true,
        label: trans('Author'),
        align: 'left',
        sortable: false,
        field: (row : RatedPost) => row.user.full_name,
    },
    {
        name: 'postPublishedAt',
        required: true,
        label: trans('Published at'),
        align: 'left',
        sortable: false,
        field: (row : RatedPost) => row.published_at,
    },
]

</script>

<template>
    <q-table dense
        table-header-class="text-primary bg-blue-1"
        :row-key="row => row.id" :columns="columns" :rows="props.data"
        :rows-per-page-options="[0]"
        hide-pagination hide-bottom flat color="primary" >
        <template v-slot:body-cell-postRating="props">
            <q-td :props="props" auto-width class="bg-green-1">
                <span class="text-primary text-weight-medium">
                    {{ props.row.rating }}
                </span>
            </q-td>
        </template>
    </q-table>
</template>
