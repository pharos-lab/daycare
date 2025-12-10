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
}

export interface Paginated<T> {
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

export type BreadcrumbItemType = BreadcrumbItem;
