<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Daycare, StaffWithDaycares, User, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Mail, Phone, MapPin, Building2, Calendar, Briefcase } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';

const props = defineProps<{
    staff: StaffWithDaycares;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Staff', href: admin.staff.index().url },
    { title: 'Details', href: admin.staff.show({ staff: props.staff.id }).url },
];
</script>

<template>
    <Head title="Staff Member Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ staff.name }}</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Staff member details and information
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button as-child>
                        <Link :href="admin.staff.edit({ staff: staff.id }).url">
                            <Edit class="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Personal Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Personal Information</CardTitle>
                        <CardDescription>Basic contact information</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <Mail class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Email</p>
                                <a :href="`mailto:${staff.email}`" class="text-sm text-primary hover:underline">
                                    {{ staff.email }}
                                </a>
                            </div>
                        </div>

                        <div v-if="staff.profile.phone" class="flex items-start gap-3">
                            <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Phone</p>
                                <a :href="`tel:${staff.profile.phone}`" class="text-sm text-primary hover:underline">
                                    {{ staff.profile.phone }}
                                </a>
                            </div>
                        </div>

                        <div v-if="staff.profile.address || staff.profile.city" class="flex items-start gap-3">
                            <MapPin class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Address</p>
                                <p class="text-sm text-muted-foreground">
                                    <template v-if="staff.profile.address">{{ staff.profile.address }}<br></template>
                                    <template v-if="staff.profile.postal_code || staff.profile.city">
                                        {{ staff.profile.postal_code }} {{ staff.profile.city }}
                                    </template>
                                    <template v-if="staff.profile.country"><br>{{ staff.profile.country }}</template>
                                </p>
                            </div>
                        </div>

                        <Separator />

                        <div class="flex items-start gap-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Member Since</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ staff.created_at }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Professional Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Professional Information</CardTitle>
                        <CardDescription>Employment details</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">

                        <div v-if="staff.profile.hire_date" class="flex items-start gap-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Hire Date</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ staff.profile.hire_date }}
                                </p>
                            </div>
                        </div>

                        <div v-if="!staff.profile.hire_date" class="text-center py-8">
                            <Briefcase class="mx-auto h-12 w-12 text-muted-foreground" />
                            <h3 class="mt-4 text-sm font-medium">No professional information</h3>
                            <p class="mt-2 text-sm text-muted-foreground">
                                Add employment details for this staff member.
                            </p>
                            <Button variant="outline" class="mt-4" as-child>
                                <Link :href="admin.staff.edit({ staff: staff.id }).url">
                                    Add Details
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Associated Daycares -->
            <Card>
                <CardHeader>
                    <CardTitle>Associated Daycares</CardTitle>
                    <CardDescription>Daycares where this staff member works</CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="staff.associated_daycares.length > 0" class="grid gap-4 md:grid-cols-2">
                        <div
                            v-for="daycare in staff.associated_daycares"
                            :key="daycare.id"
                            class="rounded-lg border border-sidebar-border/70 p-4 hover:bg-muted/50 transition-colors dark:border-sidebar-border"
                        >
                            <div class="flex items-start justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <Building2 class="h-4 w-4 text-muted-foreground" />
                                        <Link
                                            :href="admin.daycares.show({ daycare: daycare.id }).url"
                                            class="font-medium hover:text-primary"
                                        >
                                            {{ daycare.name }}
                                        </Link>
                                    </div>
                                    <p v-if="daycare.city" class="text-sm text-muted-foreground">
                                        {{ daycare.city }}
                                    </p>
                                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                        <span>Director:</span>
                                        <span class="font-medium">{{ daycare?.director?.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <Building2 class="mx-auto h-12 w-12 text-muted-foreground" />
                        <h3 class="mt-4 text-sm font-medium">No daycares assigned</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            This staff member is not currently assigned to any daycare.
                        </p>
                        <Button variant="outline" class="mt-4" as-child>
                            <Link :href="admin.staff.edit({ staff: staff.id }).url">
                                Assign Daycares
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>