import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    return toUrl(urlToCheck) === currentUrl;
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function getRoleBadgeColor(roles: string[]) {
    if (!roles || roles.length === 0) return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    const role = roles[0];
    
    const colors: Record<string, string> = {
        admin: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
        director: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
        staff: 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400',
        parent: 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-400',
    };
    
    return colors[role] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
};