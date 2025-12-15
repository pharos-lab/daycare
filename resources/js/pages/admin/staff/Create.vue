<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Daycare, type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { UserPlus, ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps<{
    daycares: Daycare[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Staff', href: admin.staff.index().url },
    { title: 'Create', href: admin.staff.create().url },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    phone: '',
    address: '',
    city: '',
    postal_code: '',
    country: 'France',
    position: '',
    hire_date: '',
    daycare_ids: [] as number[],
});

const submit = () => {
    form.post(admin.staff.store().url);
};
</script>

<template>
    <Head title="Create Staff Member" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Create New Staff Member</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Add a new staff member to the system
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <a :href="admin.staff.index().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Staff
                    </a>
                </Button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="mx-auto w-full max-w-3xl space-y-6">
                <!-- Personal Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Personal Information</CardTitle>
                        <CardDescription>Basic information about the staff member</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name" required>Full Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="John Doe"
                                required
                            />
                            <p v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="email" required>Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="john.doe@example.com"
                                    required
                                />
                                <p v-if="form.errors.email" class="text-sm text-destructive">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="password" required>Password</Label>
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    placeholder="••••••••"
                                    required
                                />
                                <p v-if="form.errors.password" class="text-sm text-destructive">
                                    {{ form.errors.password }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="phone">Phone Number</Label>
                            <Input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                placeholder="+33 6 12 34 56 78"
                            />
                            <p v-if="form.errors.phone" class="text-sm text-destructive">
                                {{ form.errors.phone }}
                            </p>
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
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="position">Position</Label>
                                <Input
                                    id="position"
                                    v-model="form.position"
                                    type="text"
                                    placeholder="Educator, Assistant, etc."
                                />
                                <p v-if="form.errors.position" class="text-sm text-destructive">
                                    {{ form.errors.position }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="hire_date">Hire Date</Label>
                                <Input
                                    id="hire_date"
                                    v-model="form.hire_date"
                                    type="date"
                                />
                                <p v-if="form.errors.hire_date" class="text-sm text-destructive">
                                    {{ form.errors.hire_date }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Address Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Address Information</CardTitle>
                        <CardDescription>Staff member's residential address</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="address">Street Address</Label>
                            <Input
                                id="address"
                                v-model="form.address"
                                type="text"
                                placeholder="123 Main Street"
                            />
                            <p v-if="form.errors.address" class="text-sm text-destructive">
                                {{ form.errors.address }}
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="city">City</Label>
                                <Input
                                    id="city"
                                    v-model="form.city"
                                    type="text"
                                    placeholder="Paris"
                                />
                                <p v-if="form.errors.city" class="text-sm text-destructive">
                                    {{ form.errors.city }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="postal_code">Postal Code</Label>
                                <Input
                                    id="postal_code"
                                    v-model="form.postal_code"
                                    type="text"
                                    placeholder="75001"
                                />
                                <p v-if="form.errors.postal_code" class="text-sm text-destructive">
                                    {{ form.errors.postal_code }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="country">Country</Label>
                                <Input
                                    id="country"
                                    v-model="form.country"
                                    type="text"
                                    placeholder="France"
                                />
                                <p v-if="form.errors.country" class="text-sm text-destructive">
                                    {{ form.errors.country }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Daycare Assignment -->
                <Card>
                    <CardHeader>
                        <CardTitle>Daycare Assignment</CardTitle>
                        <CardDescription>Select which daycares this staff member works at</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-for="daycare in daycares"
                                :key="daycare.id"
                                class="flex items-start space-x-3"
                            >
                                <Checkbox
                                    :id="`daycare-${daycare.id}`"
                                    :model-value="form.daycare_ids.includes(daycare.id)"
                                    @update:modelValue="(value: boolean | 'indeterminate') => {
                                        const checked = value === true; // transforme en boolean strict
                                        form.daycare_ids = checked
                                        ? [...form.daycare_ids, daycare.id]
                                        : form.daycare_ids.filter(id => id !== daycare.id)
                                    }"
                                />
                                <Label
                                    :for="`daycare-${daycare.id}`"
                                    class="cursor-pointer text-sm font-normal"
                                >
                                    {{ daycare.name }}
                                </Label>
                            </div>
                            <p v-if="form.errors.daycare_ids" class="text-sm text-destructive">
                                {{ form.errors.daycare_ids }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" as-child>
                        <a :href="admin.staff.index().url">Cancel</a>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <UserPlus class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Creating...' : 'Create Staff Member' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>