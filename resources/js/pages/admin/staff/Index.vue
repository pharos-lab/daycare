<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { User, Daycare, Paginated, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Users, Search, Edit, Trash2, Eye, UserPlus } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';

interface StaffWithDaycares extends User {
    daycares: Daycare[];
    profile: {
        position?: string;
        city?: string;
    };
    associated_daycares: Daycare[];
}

interface Filters {
    search?: string;
    daycare_id?: number;
    position?: string;
    sort?: string;
    direction?: string;
}

const props = defineProps<{
    staff: Paginated<StaffWithDaycares>;
    daycares: Daycare[];
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Staff', href: admin.staff.index().url },
];

const search = ref(props.filters.search || '');
const daycareFilter = ref(props.filters.daycare_id?.toString() || '');

watch([search, daycareFilter], () => {
    router.get(admin.staff.index().url, {
        search: search.value,
        daycare_id: daycareFilter.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
});

const resetFilters = () => {
    search.value = '';
    daycareFilter.value = '';
};

const deleteStaff = (staffId: number) => {
    if (confirm('Are you sure you want to delete this staff member? This action cannot be undone.')) {
        router.delete(admin.staff.destroy({ staff: staffId }).url, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Staff Management" />
    <!-- <pre>{{  staff }}</pre> -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Staff Management</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage all staff members in the system
                    </p>
                </div>
                <Link
                    :href="admin.staff.create().url"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    <UserPlus class="h-4 w-4" />
                    Create Staff Member
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="relative grow">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <Search class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Search by name, email..."
                        class="h-10 pl-8"
                    />
                </div>

                <!-- Daycare Filter -->
                <Select v-model="daycareFilter" multiple>
                    <SelectTrigger class="w-full md:w-[250px]">
                        <SelectValue placeholder="Filter by daycare" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="daycare in daycares"
                            :key="daycare.id"
                            :value="daycare.id.toString()"
                        >
                            {{ daycare.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <!-- Reset Button -->
                <div v-if="search || daycareFilter" class="flex items-center">
                    <button
                        @click="resetFilters"
                        class="text-sm text-primary hover:underline"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>

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
                                    City
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Daycares
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                            <tr v-for="member in staff.data" :key="member.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-medium">
                                        {{ member.name }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm text-muted-foreground">
                                        {{ member.email }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm text-muted-foreground">
                                        {{ member.profile.city || '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="daycare in member.associated_daycares"
                                            :key="daycare.id"
                                            variant="secondary"
                                            class="text-xs"
                                        >
                                            {{ daycare.name }}
                                        </Badge>
                                        <span v-if="member.associated_daycares.length === 0" class="text-sm text-muted-foreground">
                                            No daycare
                                        </span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="admin.staff.show({ staff: member.id }).url"
                                            class="text-primary hover:text-primary/80"
                                            title="See details"
                                        >
                                            <Eye class="size-4" />
                                        </Link>
                                        <Link
                                            :href="admin.staff.edit({ staff: member.id }).url"
                                            class="text-primary hover:text-primary/80"
                                            title="Edit"
                                        >
                                            <Edit class="size-4" />
                                        </Link>
                                        <button
                                            @click="deleteStaff(member.id)"
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
                    <div v-if="staff.data.length === 0" class="rounded-xl border border-sidebar-border/70 bg-card p-12 text-center dark:border-sidebar-border">
                        <Users class="mx-auto h-12 w-12 text-muted-foreground" />
                        <h3 class="mt-4 text-lg font-semibold">No staff members found</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Get started by creating a new staff member.
                        </p>
                        <Link
                            :href="admin.staff.create().url"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                        >
                            <UserPlus class="h-4 w-4" />
                            Create Staff Member
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="staff.links.length" class="border-t border-sidebar-border/70 bg-muted/30 px-4 py-3 dark:border-sidebar-border">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-muted-foreground">
                            Page {{ staff.current_page }} of {{ staff.last_page }}
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in staff.links"
                                :key="link.label"
                                :href="link.url || ''"
                                :class="[
                                    'rounded border px-3 py-1 text-sm',
                                    link.active
                                        ? 'border-primary bg-primary text-primary-foreground'
                                        : 'border-input bg-background hover:bg-muted',
                                    !link.url ? 'cursor-not-allowed opacity-50' : ''
                                ]"
                                :disabled="!link.url"
                                preserve-scroll
                            >{{ link.label.replace(/&amp;laquo;/, '').replace(/&amp;raquo;/g, '') }}</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>