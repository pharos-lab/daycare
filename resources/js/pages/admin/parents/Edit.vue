<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Daycare, Parent, User, type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Save, ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps<{
    parent: Parent;
    daycares: Daycare[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Parents', href: admin.parents.index().url },
    { title: 'Edit', href: admin.parents.edit({ parent: props.parent.id }).url },
];

const form = useForm({
    name: props.parent.name,
    email: props.parent.email,
    password: '',
    phone: props.parent.profile?.phone || '',
    address: props.parent.profile?.address || '',
    city: props.parent.profile?.city || '',
    postal_code: props.parent.profile?.postal_code || '',
    country: props.parent.profile?.country || 'France',
    daycare_ids: props.parent.associated_daycares.map(daycare => daycare.id) || [] as number[],
});

const submit = () => {
    form.put(admin.parents.update({ parent: props.parent.id }).url);
};
</script>

<template>
    <Head title="Edit Parent" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Edit Parent</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Update parent information
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <a :href="admin.parents.index().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Parents
                    </a>
                </Button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="mx-auto w-full max-w-3xl space-y-6">
                <!-- Personal Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Personal Information</CardTitle>
                        <CardDescription>Basic information about the parent</CardDescription>
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
                                <Label for="password">New Password</Label>
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    placeholder="Leave blank to keep current"
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

                <!-- Address Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Address Information</CardTitle>
                        <CardDescription>Parent's residential address</CardDescription>
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
                        <CardDescription>Select which daycares this parent is associated with</CardDescription>
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
                        <a :href="admin.parents.index().url">Cancel</a>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Save class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>