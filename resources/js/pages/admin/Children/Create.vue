<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Daycare, User, type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { UserPlus, ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps<{
    daycares: Daycare[];
    parents: User[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Children', href: admin.children.index().url },
    { title: 'Create', href: admin.children.create().url },
];

const form = useForm({
    daycare_id: '',
    first_name: '',
    last_name: '',
    birth_date: '',
    gender: '',
    emergency_contact: '',
    enrollment_date: '',
    parent_ids: [] as number[],
});

const submit = () => {
    form.post(admin.children.store().url);
};
</script>

<template>
    <Head title="Add Child" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Add New Child</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Add a new child to the system
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <a :href="admin.children.index().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Children
                    </a>
                </Button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="mx-auto w-full max-w-3xl space-y-6">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Basic Information</CardTitle>
                        <CardDescription>Child's personal details</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="first_name" required>First Name</Label>
                                <Input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    placeholder="Emma"
                                    required
                                />
                                <p v-if="form.errors.first_name" class="text-sm text-destructive">
                                    {{ form.errors.first_name }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="last_name" required>Last Name</Label>
                                <Input
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    placeholder="Smith"
                                    required
                                />
                                <p v-if="form.errors.last_name" class="text-sm text-destructive">
                                    {{ form.errors.last_name }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="birth_date" required>Birth Date</Label>
                                <Input
                                    id="birth_date"
                                    v-model="form.birth_date"
                                    type="date"
                                    required
                                />
                                <p v-if="form.errors.birth_date" class="text-sm text-destructive">
                                    {{ form.errors.birth_date }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="gender">Gender</Label>
                                <Select v-model="form.gender">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select gender" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="male">Male</SelectItem>
                                        <SelectItem value="female">Female</SelectItem>
                                        <SelectItem value="other">Other</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.gender" class="text-sm text-destructive">
                                    {{ form.errors.gender }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="daycare_id" required>Daycare</Label>
                            <Select v-model="form.daycare_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select daycare" />
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
                            <p v-if="form.errors.daycare_id" class="text-sm text-destructive">
                                {{ form.errors.daycare_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="enrollment_date">Enrollment Date</Label>
                            <Input
                                id="enrollment_date"
                                v-model="form.enrollment_date"
                                type="date"
                            />
                            <p v-if="form.errors.enrollment_date" class="text-sm text-destructive">
                                {{ form.errors.enrollment_date }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Parents Assignment -->
                <Card>
                    <CardHeader>
                        <CardTitle>Parents Assignment</CardTitle>
                        <CardDescription>Select which parents are associated with this child</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            <div
                                v-for="parent in parents"
                                :key="parent.id"
                                class="flex items-start space-x-3"
                            >
                                <Checkbox
                                    :id="`parent-${parent.id}`"
                                    :checked="form.parent_ids.includes(parent.id)"
                                    @update:modelValue="(value: boolean | 'indeterminate') => {
                                        const checked = value === true; // transforme en boolean strict
                                        form.parent_ids = checked
                                        ? [...form.parent_ids, parent.id]
                                        : form.parent_ids.filter(id => id !== parent.id)
                                    }"
                                />
                                <Label
                                    :for="`parent-${parent.id}`"
                                    class="cursor-pointer text-sm font-normal"
                                >
                                    {{ parent.name }}
                                </Label>
                            </div>
                            <p v-if="form.errors.parent_ids" class="text-sm text-destructive">
                                {{ form.errors.parent_ids }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" as-child>
                        <a :href="admin.children.index().url">Cancel</a>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <UserPlus class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Creating...' : 'Add Child' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>