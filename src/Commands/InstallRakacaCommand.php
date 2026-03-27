<?php

namespace Paparee\Rakaca\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InstallRakacaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rakaca:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Rakaca: Seed permissions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Rakaca installation...');

        $this->seedPermissions();

        $this->info('Rakaca installation completed successfully!');

        return self::SUCCESS;
    }

    protected function seedPermissions(): void
    {
        $this->info('Seeding permissions...');

        $permissions = [
            'guest.dashboard',
            'guest.sidebar',
            'service.create',
            'service.read',
            'service.update',
            'service.delete',
            'submission.create',
            'submission.read',
            'submission.update',
            'submission.delete',
            'personal-service.create',
            'personal-service.read',
            'personal-service.update',
            'personal-service.delete',
            'organization.create',
            'organization.read',
            'organization.update',
            'organization.delete',
            'bale-list.create',
            'bale-list.read',
            'bale-list.update',
            'bale-list.delete',
            'bale-user.create',
            'bale-user.read',
            'bale-user.update',
            'bale-user.delete',
            'analytic.create',
            'analytic.read',
            'analytic.update',
            'analytic.delete',
            'select-bale',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission], ['guard_name' => 'web']);
        }

        $this->info('Permissions seeded and updated.');

        // Force sync to root role if exists
        $rootRole = Role::where('name', 'root')->first();
        if ($rootRole) {
            $this->info('Force syncing ALL permissions to root role...');
            $rootRole->syncPermissions(Permission::where('name', '!=', 'guest.sidebar')->get());

            // Clear cache
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            $this->info('Permissions force synced and cache cleared for root role.');
        }

        $roleGuest = Role::firstOrCreate(['name' => 'guest']);

        // Role guest: no permissions (already empty by default or sync empty array)
        $roleGuest->syncPermissions(['guest.dashboard', 'guest.sidebar']);
    }
}
