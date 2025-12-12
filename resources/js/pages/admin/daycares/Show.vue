<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Director, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Building2, MapPin, Phone, Mail, Users, Calendar } from 'lucide-vue-next';

interface Daycare {
    id: number;
    name: string;
    address: string;
    city: string;
    postal_code: string;
    country: string;
    phone: string;
    email: string;
    capacity: number;
    description: string | null;
    opening_hours: any;
    full_address: string;
    created_at: string;
    director: Director;
}

const props = defineProps<{
    daycare: Daycare;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Daycares', href: admin.daycares.index().url },
    { title: props.daycare.name, href: admin.daycares.show({ daycare: props.daycare.id }).url },
];
</script>

<template>
    <Head :title="`Daycare: ${daycare.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
            <!-- Header -->
            <div class="mb-2">
                <Link
                    :href="admin.daycares.index().url"
                    class="mb-4 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back to Daycares
                </Link>
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">{{ daycare.name }}</h1>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Daycare Details
                        </p>
                    </div>
                    <Link
                        :href="admin.daycares.edit({ daycare: daycare.id }).url"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        <Edit class="h-4 w-4" />
                        Edit Daycare
                    </Link>
                </div>
            </div>

            <!-- Daycare Information Card -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden dark:border-sidebar-border">
                <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <h2 class="font-semibold">General Information</h2>
                </div>

                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <!-- Name -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <Building2 class="mr-2 h-4 w-4" />
                                Daycare Name
                            </dt>
                            <dd class="text-sm">
                                {{ daycare.name }}
                            </dd>
                        </div>

                        <!-- Capacity -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <Users class="mr-2 h-4 w-4" />
                                Capacity
                            </dt>
                            <dd class="text-sm">
                                {{ daycare.capacity }} children
                            </dd>
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-2">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <MapPin class="mr-2 h-4 w-4" />
                                Full Address
                            </dt>
                            <dd class="text-sm">
                                {{ daycare.full_address }}
                            </dd>
                        </div>

                        <!-- Phone -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <Phone class="mr-2 h-4 w-4" />
                                Phone
                            </dt>
                            <dd class="text-sm">
                                {{ daycare.phone }}
                            </dd>
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <Mail class="mr-2 h-4 w-4" />
                                Email
                            </dt>
                            <dd class="text-sm">
                                {{ daycare.email }}
                            </dd>
                        </div>

                        <!-- Created At -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 flex items-center text-sm font-medium text-muted-foreground">
                                <Calendar class="mr-2 h-4 w-4" />
                                Created At
                            </dt>
                            <dd class="text-sm">
                                {{ new Date(daycare.created_at).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Director Information Card -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden dark:border-sidebar-border">
                <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <h2 class="font-semibold">Director Information</h2>
                </div>

                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <!-- Director Name -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 text-sm font-medium text-muted-foreground">
                                Name
                            </dt>
                            <dd class="text-sm">
                                {{ daycare.director.name }}
                            </dd>
                        </div>

                        <!-- Director Email -->
                        <div class="sm:col-span-1">
                            <dt class="mb-1 text-sm font-medium text-muted-foreground">
                                Email
                            </dt>
                            <dd class="text-sm">
                                {{ daycare.director.email }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Description Card (if exists) -->
            <div v-if="daycare.description" class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden dark:border-sidebar-border">
                <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <h2 class="font-semibold">Description</h2>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-muted-foreground">
                        {{ daycare.description }}
                    </p>
                </div>
            </div>

            <!-- Activity Section (for future use) -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden dark:border-sidebar-border">
                <div class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border">
                    <h2 class="font-semibold">Activity</h2>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-muted-foreground">
                        Statistics and activity history will be displayed here in future updates.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>