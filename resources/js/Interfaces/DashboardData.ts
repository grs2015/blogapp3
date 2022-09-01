import { Status, UserStatus } from "./PaginatedData"

export interface DashboardData extends DashboardPostData {
    active_authors: Array<ActiveUser>,
    count_author_users: number,
    count_member_users: number,
    enabled_users_count: number,
    pending_users_count: number,
    recent_users: Array<RecentUser>
}

export interface DashboardPostData {
    recent_posts: Array<RecentPost>,
    draft_posts_count: number,
    pending_posts_count: number,
    posts_count: number,
    published_posts_count: number,
    most_rated: Array<RatedPost>,
    most_viewed: Array<ViewedPost>,
    most_commented: Array<CommentedPost>
}

export interface UserData {
    email: string,
    full_name: string
}

export interface ActiveUser {
    id: number,
    email: string,
    full_name: string,
    last_login: string,
    posts_count: number,
    roles: string
}
export interface RecentUser {
    id: number,
    created_at: string,
    email: string,
    roles: string,
    full_name: string,
    status: UserStatus,
}
export interface RecentPost {
    id: number,
    published_at: string,
    status: Status,
    title: string,
    user?: UserData
}
export interface RatedPost {
    published_at: string,
    rating: number,
    title: string,
    user?: UserData,
}
export interface ViewedPost {
    published_at: string,
    title: string,
    views: number,
    user?: UserData
}
export interface CommentedPost {
    comments_count: number,
    published_at: string,
    title: string,
    user?: UserData
}
