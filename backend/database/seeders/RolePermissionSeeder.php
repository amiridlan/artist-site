<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions by event type
        $eventTypes = [
            'artist_performance',
            'artist_appearance',
            'content_filming',
            'practice_day',
            'day_off',
            'staff_event',
            'social_media_post',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        // Create permissions for each event type and action
        foreach ($eventTypes as $type) {
            foreach ($actions as $action) {
                Permission::create(['name' => "{$action}-{$type}"]);
            }
        }

        // Additional permissions
        Permission::create(['name' => 'view-all-schedules']); // Cross-visibility
        Permission::create(['name' => 'manage-resources']);
        Permission::create(['name' => 'manage-kanban']);
        Permission::create(['name' => 'override-conflicts']); // Super Admin only
        Permission::create(['name' => 'view-conflict-logs']);
        Permission::create(['name' => 'resolve-conflicts']);

        // Create roles and assign permissions

        // 1. Super Admin - Full access
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Marketing Department
        $marketing = Role::create(['name' => 'Marketing Department']);
        $marketing->givePermissionTo([
            // Full CRUD on marketing-related events
            'view-social_media_post', 'create-social_media_post', 'edit-social_media_post', 'delete-social_media_post',
            'view-content_filming', 'create-content_filming', 'edit-content_filming', 'delete-content_filming',
            'view-practice_day', 'create-practice_day', 'edit-practice_day', 'delete-practice_day',

            // View-only on other events
            'view-artist_performance', 'view-artist_appearance', 'view-day_off', 'view-staff_event',

            // Additional permissions
            'view-all-schedules',
            'manage-kanban',
            'view-conflict-logs',
        ]);

        // 3. Events Department
        $events = Role::create(['name' => 'Events Department']);
        $events->givePermissionTo([
            // Full CRUD on events-related
            'view-artist_performance', 'create-artist_performance', 'edit-artist_performance', 'delete-artist_performance',
            'view-artist_appearance', 'create-artist_appearance', 'edit-artist_appearance', 'delete-artist_appearance',
            'view-staff_event', 'create-staff_event', 'edit-staff_event', 'delete-staff_event',

            // View-only on other events
            'view-social_media_post', 'view-content_filming', 'view-practice_day', 'view-day_off',

            // Additional permissions
            'view-all-schedules',
            'manage-resources',
            'manage-kanban',
            'view-conflict-logs',
        ]);

        // 4. Artist Role
        $artist = Role::create(['name' => 'Artist']);
        $artist->givePermissionTo([
            // View own schedule (will be filtered in policy)
            'view-artist_performance', 'view-artist_appearance', 'view-content_filming',
            'view-practice_day', 'view-staff_event', 'view-social_media_post',

            // Manage own day-offs
            'view-day_off', 'create-day_off', 'edit-day_off', 'delete-day_off',
        ]);
    }
}
