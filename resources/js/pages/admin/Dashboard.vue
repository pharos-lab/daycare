<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { getRoleBadgeColor } from '@/lib/utils';
import admin, { dashboard } from '@/routes/admin';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Users, UserCheck, Building2, Eye } from 'lucide-vue-next';

interface Stats {
    total_children: number;
    total_directors: number;
    total_staff: number;
    total_parents: number;
}

interface RecentUser {
    id: number;
    name: string;
    email: string;
    created_at: string;
    is_active: boolean;
    roles: string[];
}

const props = defineProps<{
    stats: Stats;
    recent_users: RecentUser[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: dashboard().url,
    },
];

const statCards = [
    {
        title: 'Directors',
        value: props.stats.total_directors,
        icon: Building2,
        color: 'text-indigo-600 dark:text-indigo-400',
        bgColor: 'bg-indigo-100 dark:bg-indigo-900/20',
    },
    {
        title: 'Staff',
        value: props.stats.total_staff,
        icon: Users,
        color: 'text-teal-600 dark:text-teal-400',
        bgColor: 'bg-teal-100 dark:bg-teal-900/20',
    },
    {
        title: 'Parents',
        value: props.stats.total_parents,
        icon: Users,
        color: 'text-pink-600 dark:text-pink-400',
        bgColor: 'bg-pink-100 dark:bg-pink-900/20',
    },
    {
        title: 'Children',
        value: props.stats.total_children,
        icon: Users,
        color: 'text-purple-600 dark:text-purple-400',
        bgColor: 'bg-purple-100 dark:bg-purple-900/20',
    },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            <!-- Stats Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div
                    v-for="stat in statCards"
                    :key="stat.title"
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-muted-foreground">
                                {{ stat.title }}
                            </p>
                            <p class="mt-2 text-3xl font-bold">
                                {{ stat.value }}
                            </p>
                        </div>
                        <div :class="[stat.bgColor, stat.color, 'rounded-lg p-3']">
                            <component :is="stat.icon" class="h-6 w-6" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users by Role & Recent Users -->
            <div class="recent-users">
                <!-- Recent Users Card -->
                <div class="relative col-span-3 overflow-hidden rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                    <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                        <h2 class="font-semibold">Recent Users</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                        Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                        Created
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                        Details
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                                <tr v-for="user in recent_users" :key="user.id" class="hover:bg-muted/50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                        {{ user.name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">
                                        {{ user.email }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span
                                            v-for="role in user.roles"
                                            :key="role"
                                            :class="getRoleBadgeColor(user.roles)"
                                            class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                        >
                                            {{ role }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">
                                        {{ new Date(user.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <Link
                                            :href="admin.users.edit({ user: user.id }).url"
                                            class="text-sky-600 hover:text-sky-600/80 rounded-md transition"
                                            title="See User Details"
                                        >
                                            <Eye class="size-6" />
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>