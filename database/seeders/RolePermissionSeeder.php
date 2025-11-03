<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Daycare permissions
            'daycare.manage',
            'daycare.view',
            
            // Staff permissions
            'staff.manage',
            
            // Child permissions
            'child.manage',
            'child.view',
            
            // Parent permissions
            'parent.manage',
            
            // Transmission permissions
            'transmission.manage',
            'transmission.view',
            
            // Announcement permissions
            'announcement.manage',
            
            // Message permissions
            'message.manage',
            'message.view',
            'message.reply',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role - Super admin with all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Director role
        $directorRole = Role::create(['name' => 'director']);
        $directorRole->givePermissionTo([
            'daycare.manage',
            'staff.manage',
            'child.manage',
            'parent.manage',
            'transmission.manage',
            'announcement.manage',
            'message.manage',
        ]);

        // Staff role
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'daycare.view',
            'child.view',
            'parent.view',
            'transmission.manage',
            'message.manage',
        ]);

        // Parent role
        $parentRole = Role::create(['name' => 'parent']);
        $parentRole->givePermissionTo([
            'daycare.view',
            'child.view',
            'transmission.view',
            'message.view',
            'message.reply',
        ]);
    }
}