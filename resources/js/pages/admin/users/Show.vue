<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { getRoleBadgeColor } from '@/lib/utils';
import admin  from '@/routes/admin';
import { User, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Mail, Calendar, Shield } from 'lucide-vue-next';


const props = defineProps<{
    user: User;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Users', href: admin.users.index().url },
    { title: props.user.name, href: admin.users.show({ user: props.user.id }).url },
];
</script>

<template>
    <Head :title="`User: ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            <!-- Header -->
            <div class="mb-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">{{ user.name }}</h1>
                        <p class="mt-1 text-sm text-muted-foreground">
                            User Details
                        </p>
                    </div>
                    <Link
                        :href="admin.users.edit({ user: user.id }).url"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        <Edit class="h-4 w-4" />
                        Edit User
                    </Link>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden dark:border-sidebar-border">
                <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <h2 class="font-semibold">User Information</h2>
                </div>

                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <!-- Name -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <Shield class="mr-2 h-4 w-4" />
                                Full Name
                            </dt>
                            <dd class="text-sm">
                                {{ user.name }}
                            </dd>
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <Mail class="mr-2 h-4 w-4" />
                                Email Address
                            </dt>
                            <dd class="text-sm">
                                {{ user.email }}
                            </dd>
                        </div>

                        <!-- Role -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 text-sm font-medium text-muted-foreground">
                                Role
                            </dt>
                            <dd>
                                <span
                                    v-for="role in user.roles"
                                    :key="role"
                                    :class="getRoleBadgeColor(user.roles)"
                                    class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                                >
                                    {{ role.charAt(0).toUpperCase() + role.slice(1) }}
                                </span>
                            </dd>
                        </div>

                        <!-- Created At -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <Calendar class="mr-2 h-4 w-4" />
                                Created At
                            </dt>
                            <dd class="text-sm">
                                {{ new Date(user.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Additional Information (for future use) -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden dark:border-sidebar-border">
                <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <h2 class="font-semibold">Activity</h2>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-muted-foreground">
                        Activity history will be displayed here in future updates.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>