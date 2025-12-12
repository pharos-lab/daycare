<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { Daycare, Director, type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    daycare: Daycare;
    directors: Director[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: admin.dashboard().url },
    { title: 'Daycares', href: admin.daycares.index().url },
    { title: 'Edit', href: admin.daycares.edit({ daycare: props.daycare.id }).url },
];

console.log();


const form = useForm({
    director_id: props.daycare.director_id,
    name: props.daycare.name,
    address: props.daycare.address,
    city: props.daycare.city,
    postal_code: props.daycare.postal_code,
    country: props.daycare.country,
    phone: props.daycare.phone,
    email: props.daycare.email,
    capacity: props.daycare.capacity,
    description: props.daycare.description || '',
});

const submit = () => {
    form.put(admin.daycares.update({ daycare: props.daycare.id }).url);
};
</script>

<template>
    <Head title="Edit Daycare" />

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
                <h1 class="text-2xl font-bold">Edit Daycare</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Update daycare information
                </p>
            </div>

            <!-- Form -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Director -->
                    <div>
                        <label for="director_id" class="block text-sm font-medium mb-2">
                            Director <span class="text-destructive">*</span>
                        </label>
                        <select
                            id="director_id"
                            v-model="form.director_id"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-destructive': form.errors.director_id }"
                            required
                        >
                            <option value="">Select a director</option>
                            <option v-for="director in directors" :key="director.id" :value="director.id">
                                {{ director.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.director_id" class="mt-1 text-sm text-destructive">
                            {{ form.errors.director_id }}
                        </p>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">
                            Daycare Name <span class="text-destructive">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-destructive': form.errors.name }"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Address -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="address" class="block text-sm font-medium mb-2">
                                Address <span class="text-destructive">*</span>
                            </label>
                            <input
                                id="address"
                                v-model="form.address"
                                type="text"
                                class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                :class="{ 'border-destructive': form.errors.address }"
                                required
                            />
                            <p v-if="form.errors.address" class="mt-1 text-sm text-destructive">
                                {{ form.errors.address }}
                            </p>
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium mb-2">
                                City <span class="text-destructive">*</span>
                            </label>
                            <input
                                id="city"
                                v-model="form.city"
                                type="text"
                                class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                :class="{ 'border-destructive': form.errors.city }"
                                required
                            />
                            <p v-if="form.errors.city" class="mt-1 text-sm text-destructive">
                                {{ form.errors.city }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="postal_code" class="block text-sm font-medium mb-2">
                                Postal Code <span class="text-destructive">*</span>
                            </label>
                            <input
                                id="postal_code"
                                v-model="form.postal_code"
                                type="text"
                                class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                :class="{ 'border-destructive': form.errors.postal_code }"
                                required
                            />
                            <p v-if="form.errors.postal_code" class="mt-1 text-sm text-destructive">
                                {{ form.errors.postal_code }}
                            </p>
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium mb-2">
                                Country
                            </label>
                            <input
                                id="country"
                                v-model="form.country"
                                type="text"
                                class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2">
                                Phone <span class="text-destructive">*</span>
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                :class="{ 'border-destructive': form.errors.phone }"
                                required
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-destructive">
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium mb-2">
                                Email <span class="text-destructive">*</span>
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                :class="{ 'border-destructive': form.errors.email }"
                                required
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-destructive">
                                {{ form.errors.email }}
                            </p>
                        </div>
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label for="capacity" class="block text-sm font-medium mb-2">
                            Capacity (number of children) <span class="text-destructive">*</span>
                        </label>
                        <input
                            id="capacity"
                            v-model="form.capacity"
                            type="number"
                            min="1"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-destructive': form.errors.capacity }"
                            required
                        />
                        <p v-if="form.errors.capacity" class="mt-1 text-sm text-destructive">
                            {{ form.errors.capacity }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium mb-2">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="block w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        ></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-sidebar-border/70 dark:border-sidebar-border">
                        <Link
                            :href="admin.daycares.index().url"
                            class="rounded-lg border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-muted"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Daycare' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>