<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin   from '@/routes/admin';
import { Daycare, Paginated, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Building2, Search, Edit, Trash2, MapPin, Users } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Card } from '@/components/ui/card';

interface Filters {
    search?: string;
    director_id?: number;
    sort?: string;
    direction?: string;
}

const props = defineProps<{
    daycares: Paginated<Daycare>;
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Daycares', href: admin.daycares.index().url },
];

const search = ref(props.filters.search || '');

watch(search, () => {
    router.get(admin.daycares.index().url, {
        search: search.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
});

const resetFilters = () => {
    search.value = '';
};

const deleteDaycare = (daycareId: number) => {
    if (confirm('Are you sure you want to delete this daycare? This action cannot be undone.')) {
        router.delete(admin.daycares.destroy({ daycare: daycareId }).url, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Daycares Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Daycares Management</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Manage all daycares in the system
                    </p>
                </div>
                <Link
                    :href="admin.daycares.create().url"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    <Building2 class="h-4 w-4" />
                    Create Daycare
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
                        placeholder="Search by name, city or adress..."
                        class="h-10 pl-8"
                    />
                </div>

                <!-- Reset Button -->
                <div v-if="search" class="mt-4">
                    <button
                        @click="resetFilters"
                        class="text-sm text-primary hover:underline"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>

            <!-- Daycares Grid -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="daycare in daycares.data"
                    :key="daycare.id"
                    class="group relative p-6 transition-all shadow-sm  hover:shadow-md dark:border-sidebar-border"
                >
                    <!-- Daycare Name -->
                    <div class="">
                        <h3 class="text-lg font-semibold">{{ daycare.name }}</h3>
                        <p class="text-sm text-muted-foreground">
                            <MapPin class="inline-block h-3 w-3 mr-1" />
                            {{ daycare.city }}
                        </p>
                    </div>

                    <!-- Info -->
                    <div class="space-y-2 text-sm grow flex flex-col justify-end">
                        <div class="flex items-center gap-2">
                            <Users class="h-4 w-4 text-muted-foreground" />
                            <span>Capacity: {{ daycare.capacity }} children</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Building2 class="h-4 w-4 text-muted-foreground" />
                            <span>Director: {{ daycare.director.name }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-4 border-t border-sidebar-border/70 dark:border-sidebar-border">
                        <Link
                            :href="admin.daycares.show({ daycare: daycare.id }).url"
                            class="flex-1 rounded-lg border border-input bg-background px-3 py-2 text-center text-sm font-medium hover:bg-muted"
                        >
                            View
                        </Link>
                        <Link
                            :href="admin.daycares.edit({ daycare: daycare.id }).url"
                            class="rounded-lg border border-input bg-background px-3 py-2 text-sm hover:bg-muted"
                        >
                            <Edit class="h-4 w-4" />
                        </Link>
                        <button
                            @click="deleteDaycare(daycare.id)"
                            class="rounded-lg border border-destructive/50 bg-background px-3 py-2 text-sm text-destructive hover:bg-destructive/10"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-if="daycares.data.length === 0" class="rounded-xl border border-sidebar-border/70 bg-card p-12 text-center dark:border-sidebar-border">
                <Building2 class="mx-auto h-12 w-12 text-muted-foreground" />
                <h3 class="mt-4 text-lg font-semibold">No daycares found</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Get started by creating a new daycare.
                </p>
                <Link
                    :href="admin.daycares.create().url"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    <Building2 class="h-4 w-4" />
                    Create Daycare
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="daycares.links.length > 3" class="rounded-xl border border-sidebar-border/70 bg-card px-4 py-3 dark:border-sidebar-border">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-muted-foreground">
                        Page {{ daycares.current_page }} of {{ daycares.last_page }}
                    </div>
                    <div class="flex gap-2">
                        <Link
                            v-for="link in daycares.links"
                            :key="link.label"
                            :href="link.url || '#'"
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
    </AppLayout>
</template>