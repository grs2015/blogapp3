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
    'hero_image'?: File | any | any[] | null,
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
    '_method'?: "put" | null
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

export enum UserStatus {
    Enabled = 'enabled',
    Disabled = 'disabled',
    Pending = 'pending'
}

export enum UserRole {
    Admin = 'admin',
    Author = 'author',
    Member = 'member',
    SuperAdmin = 'super-admin'
}

export interface categoryData {
    id: number,
    title: string,
    meta_title?: string,
    content?: string,
    parent_id?: number,
    slug?: string,
    icon?: string,
    color?: string
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

export interface userData {
    avatar?: string,
    email: string,
    first_name: string,
    full_name?: string,
    id: number,
    intro?: string,
    last_login?: string,
    last_name?: string,
    middle_name?: string,
    mobile?: string,
    posts_count?: number,
    profile?: string,
    registered_at?: string,
    roles?: string,
    status?: UserStatus,
    email_verified_at?: string,
}

export interface Role {
    created_at: string,
    updated_at: string,
    guard_name: string,
    id: number,
    name: string,
    pivot: Object

}
