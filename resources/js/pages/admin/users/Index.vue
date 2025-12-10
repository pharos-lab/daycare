<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import type { Paginated, User, BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    users: Paginated<User>;
    filters: Object;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            
            <pre>
                {{ props }}

            </pre>
        </div>
    </AppLayout>
</template>