export interface Paginated {
    'current_page': number;
    'data': Array<PostData>;
    'first_page_url': string;
    'from': number;
    'last_page': number;
    'last_page_url': string;
    'links': Array<LinkData>;
    'next_page_url': string | null;
    'path': string;
    'per_page': number;
    'prev_page_url': string | null;
    'to': number;
    'total': number;
}


export interface PostData {
    'author_id'?: number | null;
    'content'?: string | null;
    'favorite': Favorite;
    'hero_image'?: File | string | string[] | null,
    'id': number;
    'meta_title'?: string | null;
    'parent_id'?: number | null;
    'published_at'?: string | null;
    'slug'?: string | null;
    'status': Status;
    'summary'?: string | null;
    'time_to_read': number;
    'title': string;
    'user'?: UserData | null;
    'views'?: string | null;
    'galleries'?: Array<GalleryData> | null,
    'postmetas'?: Array<PostmetaData> | null,
    'tag_ids'?: Array<number> | null,
    'cat_ids': Array<number>,
    'categories'?: Array<categoryData> | null,
    'tags'?: Array<tagData> | null,
    'images'?: Array<File> | null,
}

export interface SortingData {
    column: string;
    descending: string;
}

export interface LinkData {
    'active': boolean;
    'label': string | null;
    'url': string |null;
}

type UserData = {
    'email': string;
    'first_name': string;
}

type PostmetaData = {
    id?: number | null,
    post_id: number,
    key: string,
    content: string
}

export interface GalleryData {
    id: number;
    post_id: number;
    original: string;
    lowres: string;
    thumbs: string;
}

export interface tablePagination {
    'sortBy': string | null,
    'descending': boolean | null,
    'page': number,
    'rowsPerPage': number,
    'rowsNumber': number,
}

export enum Favorite {
    Usual = "usual",
    Favorite = "favorite"
}

export enum Status {
    Published = 'published',
    Draft = 'draft',
    Pending = 'pending',
    Unpublished = 'unpublished',
}

export interface categoryData {
    content?: string,
    id: number,
    meta_title?: string,
    parent_id?: number,
    slug: string,
    title: string
}

export interface tagData {
    content?: string,
    id: number,
    meta_title?: string,
    slug: string,
    title: string
}

export interface breadcrumbsData {
    label: string,
    icon: string,
    route?: string
}
