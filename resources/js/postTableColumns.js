import { trans } from 'laravel-vue-i18n';

export default [
    {
        name: 'postTitle',
        required: true,
        label: trans('Title'),
        align: 'left',
        sortable: true,
        field: row => row.title
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
        align: 'left',
        sortable: true,
        field: row => row.status,

    },
    {
        name: 'postFavorite',
        required: true,
        label: trans('Favorite'),
        align: 'left',
        sortable: true,
        field: row => row.favorite
    },
    {
        name: 'postViews',
        required: true,
        label: trans('Views'),
        align: 'left',
        sortable: true,
        field: row => row.views
    }
]


