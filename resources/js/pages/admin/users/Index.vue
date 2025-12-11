<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import  admin  from '@/routes/admin';
import type  { Paginated, User, BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { UserPlus, Search, Edit, Trash2, Eye } from 'lucide-vue-next';
import { getRoleBadgeColor } from '@/lib/utils';

const props = defineProps<{
    users: Paginated<User>;
    filters: {
        search?: string;
        role?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Users', href: admin.users.index().url },
];

const search = ref(props.filters.search || '');
const role = ref(props.filters.role || '');

watch([search, role], () => {
    router.get(admin.users.index().url, {
        search: search.value,
        role: role.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
});

const resetFilters = () => {
    search.value = '';
    role.value = '';
};

const deleteUser = (userId: number) => {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        router.delete(admin.users.destroy({ user: userId }).url, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Users Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Users Management</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage all users in the system
                    </p>
                </div>
                <Link
                    :href="admin.users.create().url"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    <UserPlus class="h-4 w-4" />
                    Create User
                </Link>
            </div>

            <!-- Filters -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium">
                            Search
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <Search class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search by name or email..."
                                class="block w-full rounded-lg border border-input bg-background pl-10 pr-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label class="mb-2 block text-sm font-medium">
                            Role
                        </label>
                        <select
                            v-model="role"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        >
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="director">Director</option>
                            <option value="staff">Staff</option>
                            <option value="parent">Parent</option>
                        </select>
                    </div>
                </div>

                <!-- Reset Button -->
                <div v-if="search || role" class="mt-4">
                    <button
                        @click="resetFilters"
                        class="text-sm text-primary hover:underline"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden dark:border-sidebar-border">
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
                                    Created At
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-medium">
                                        {{ user.name }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm text-muted-foreground">
                                        {{ user.email }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
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
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="admin.users.show({ user: user.id }).url"
                                            class="text-primary hover:text-primary/80"
                                            title="See details"
                                        >
                                            <Eye class="size-4" />
                                        </Link>
                                        <Link
                                            :href="admin.users.edit({ user: user.id }).url"
                                            class="text-primary hover:text-primary/80"
                                            title="Edit"
                                        >
                                            <Edit class="size-4" />
                                        </Link>
                                        <button
                                            @click="deleteUser(user.id)"
                                            class="text-destructive hover:text-destructive/80"
                                            title="Delete"
                                        >
                                            <Trash2 class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Empty State -->
                    <div v-if="users.data.length === 0" class="py-12 text-center">
                        <p class="text-muted-foreground">No users found.</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="users.links.length" class="border-t border-sidebar-border/70 bg-muted/30 px-4 py-3 dark:border-sidebar-border">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-muted-foreground">
                            Page {{ users.current_page }} of {{ users.last_page }}
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in users.links"
                                :key="link.label"
                                :href="link.url || ''"
                                v-html="link.label"
                                :class="[
                                    'rounded border px-3 py-1 text-sm',
                                    link.active
                                        ? 'border-primary bg-primary text-primary-foreground'
                                        : 'border-input bg-background hover:bg-muted',
                                    !link.url ? 'cursor-not-allowed opacity-50' : ''
                                ]"
                                :disabled="!link.url"
                                preserve-scroll
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>