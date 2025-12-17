import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles: string[];
}

export interface Paginated<T> {
    links: { url: string | null; label: string; active: boolean }[];
    data: T[];
    current_page: number;
    from: number | null;
    to: number | null;
    per_page: number;
    last_page: number;
    total: number;
    path: string;
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
}

export interface Daycare {
    id: number;
    name: string;
    city: string;
    address: string;
    postal_code: string;
    country: string;
    capacity: number;
    phone: string;
    email: string;
    description: string;
    created_at: string;
    director?: Director;
    director_id: number;
}

export interface Director {
    id: number;
    name: string;
    email?: string;
}

interface StaffWithDaycares extends User {
    profile: Profile;
    associated_daycares: Daycare[];
}

export interface Profile {
    phone?: string;
    address?: string;
    city?: string;
    postal_code?: string;
    country?: string;
    position?: string;
    hire_date?: string;
}

export interface Parent extends User {
    profile: Profile;
    associated_daycares: Daycare[];
}

export type BreadcrumbItemType = BreadcrumbItem;
