import { categoryData, LinkData } from "./PaginatedData";


export interface PaginatedCategory {
    'current_page': number;
    'data': Array<categoryData>;
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
