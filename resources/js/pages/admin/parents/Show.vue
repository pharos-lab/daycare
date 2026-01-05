<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Daycare, Parent, User, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Mail, Phone, MapPin, Building2, Calendar } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';

const props = defineProps<{
    parent: Parent;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Parents', href: admin.parents.index().url },
    { title: 'Details', href: admin.parents.show({ parent: props.parent.id }).url },
];
</script>

<template>
    <Head title="Parent Details" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ parent.name }}</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Parent details and information
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <a :href="admin.parents.index().url">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Parents
                        </a>
                    </Button>
                    <Button as-child>
                        <Link :href="admin.parents.edit({ parent: parent.id }).url">
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
                                <a :href="`mailto:${parent.email}`" class="text-sm text-primary hover:underline">
                                    {{ parent.email }}
                                </a>
                            </div>
                        </div>

                        <div v-if="parent.profile.phone" class="flex items-start gap-3">
                            <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Phone</p>
                                <a :href="`tel:${parent.profile.phone}`" class="text-sm text-primary hover:underline">
                                    {{ parent.profile.phone }}
                                </a>
                            </div>
                        </div>

                        <div v-if="parent.profile.address || parent.profile.city" class="flex items-start gap-3">
                            <MapPin class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Address</p>
                                <p class="text-sm text-muted-foreground">
                                    <template v-if="parent.profile.address">{{ parent.profile.address }}<br></template>
                                    <template v-if="parent.profile.postal_code || parent.profile.city">
                                        {{ parent.profile.postal_code }} {{ parent.profile.city }}
                                    </template>
                                    <template v-if="parent.profile.country"><br>{{ parent.profile.country }}</template>
                                </p>
                            </div>
                        </div>

                        <Separator />

                        <div class="flex items-start gap-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm font-medium">Member Since</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ parent.created_at }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Associated Daycares -->
                <Card>
                    <CardHeader>
                        <CardTitle>Associated Daycares</CardTitle>
                        <CardDescription>Daycares this parent is registered with</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="parent.associated_daycares.length > 0" class="space-y-4">
                            <div
                                v-for="daycare in parent.associated_daycares"
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
                                            <span class="font-medium">{{ daycare.director?.name }}</span>
                                        </div>
                                    </div>
                                    <Badge variant="secondary">Active</Badge>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <Building2 class="mx-auto h-12 w-12 text-muted-foreground" />
                            <h3 class="mt-4 text-sm font-medium">No daycares assigned</h3>
                            <p class="mt-2 text-sm text-muted-foreground">
                                This parent is not currently associated with any daycare.
                            </p>
                            <Button variant="outline" class="mt-4" as-child>
                                <Link :href="admin.parents.edit({ parent: parent.id }).url">
                                    Assign Daycares
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>