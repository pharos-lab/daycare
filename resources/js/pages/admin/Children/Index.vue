<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Daycare, Paginated, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Baby, Search, Edit, Trash2, Eye, UserPlus } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';

interface Child {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    birth_date: string;
    age: number;
    gender?: string;
    daycare: {
        id: number;
        name: string;
    };
    parents: Array<{
        id: number;
        name: string;
    }>;
    created_at: string;
}

interface Filters {
    search?: string;
    daycare_id?: number;
    sort?: string;
    direction?: string;
}

const props = defineProps<{
    children: Paginated<Child>;
    daycares: Daycare[];
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Children', href: admin.children.index().url },
];

const search = ref(props.filters.search || '');
const daycareFilter = ref(props.filters.daycare_id?.toString() || '');

watch([search, daycareFilter], () => {
    router.get(admin.children.index().url, {
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

const deleteChild = (childId: number, childName: string) => {
    if (confirm(`Are you sure you want to delete ${childName}? This action cannot be undone.`)) {
        router.delete(admin.children.destroy({ child: childId }).url, {
            preserveScroll: true,
        });
    }
};

const getGenderBadge = (gender?: string) => {
    if (!gender) return null;
    const variants: Record<string, 'default' | 'secondary' | 'outline'> = {
        male: 'default',
        female: 'secondary',
        other: 'outline',
    };
    return variants[gender] || 'outline';
};
</script>

<template>
    <Head title="Children Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Children Management</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage all children enrolled in daycares
                    </p>
                </div>
                <Link
                    :href="admin.children.create().url"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    <UserPlus class="h-4 w-4" />
                    Add Child
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
                        placeholder="Search by first name or last name..."
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
                                    Age
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Gender
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Daycare
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Parents
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                            <tr v-for="child in children.data" :key="child.id" class="hover:bg-muted/50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-medium">
                                        {{ child.first_name }} {{ child.last_name }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm text-muted-foreground">
                                        {{ new Date(child.birth_date).toLocaleDateString() }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <Badge v-if="child.gender" :variant="getGenderBadge(child.gender)" class="text-xs capitalize">
                                        {{ child.gender }}
                                    </Badge>
                                    <span v-else class="text-sm text-muted-foreground">-</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm text-muted-foreground">
                                        {{ child.daycare.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="parent in child.parents"
                                            :key="parent.id"
                                            variant="outline"
                                            class="text-xs"
                                        >
                                            {{ parent.name }}
                                        </Badge>
                                        <span v-if="child.parents.length === 0" class="text-sm text-muted-foreground">
                                            No parent
                                        </span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="admin.children.show({ child: child.id }).url"
                                            class="text-primary hover:text-primary/80"
                                            title="View details"
                                        >
                                            <Eye class="size-4" />
                                        </Link>
                                        <Link
                                            :href="admin.children.edit({ child: child.id }).url"
                                            class="text-primary hover:text-primary/80"
                                            title="Edit"
                                        >
                                            <Edit class="size-4" />
                                        </Link>
                                        <button
                                            @click="deleteChild(child.id, child.full_name)"
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
                    <div v-if="children.data.length === 0" class="py-12 text-center">
                        <Baby class="mx-auto h-12 w-12 text-muted-foreground" />
                        <h3 class="mt-4 text-lg font-semibold">No children found</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            {{ search || daycareFilter ? 'Try adjusting your filters' : 'Get started by adding a new child' }}
                        </p>
                        <Link
                            v-if="!search && !daycareFilter"
                            :href="admin.children.create().url"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                        >
                            <UserPlus class="h-4 w-4" />
                            Add Child
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="children.links.length" class="border-t border-sidebar-border/70 bg-muted/30 px-4 py-3 dark:border-sidebar-border">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-muted-foreground">
                            Page {{ children.current_page }} of {{ children.last_page }}
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in children.links"
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