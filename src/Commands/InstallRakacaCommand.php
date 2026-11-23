<?php

namespace Paparee\Rakaca\Commands;

use Bale\Core\Support\RootPermissions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InstallRakacaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rakaca:install {--with-wa-notify : Buat client Wara untuk notifikasi WhatsApp}';

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

        $this->ensureWaraClient();

        $this->info('Rakaca installation completed successfully!');

        return self::SUCCESS;
    }

    protected function ensureWaraClient(): void
    {
        if (! $this->option('with-wa-notify')) {
            return;
        }

        $name = (string) config('rakaca.notify.client_name', 'Rakaca (Package)');

        if (! $this->confirm('Buat client Wara "'.$name.'" untuk notifikasi WhatsApp?', false)) {
            return;
        }

        if (! Schema::hasTable('wara_clients')) {
            $this->warn('Tabel wara_clients belum ada. Jalankan: php artisan wara:install');

            return;
        }

        $class = 'Bale\\Wara\\Models\\WaraClient';

        if (! class_exists($class)) {
            $this->warn('Kelas WaraClient tidak ditemukan. Jalankan: php artisan wara:install');

            return;
        }

        $model = new $class;

        $client = $model::query()->firstOrCreate(
            ['name' => $name],
            [
                'type' => 'service',
                'bale_id' => null,
                'api_token_id' => null,
                'name' => $name,
                'is_active' => true,
            ]
        );

        $this->line('  + Client Wara: '.$client->name);
        $this->line('  Petakan purpose "'.config('rakaca.notify.purpose', 'rakaca').'" ke device di menu Wara > Client setelah wara:sync-devices.');
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
            'form.create',
            'form.read',
            'form.update',
            'form.delete',
            'submission.create',
            'submission.read',
            'submission.update',
            'submission.delete',
            'submission.export',
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
            $rootRole->syncPermissions(RootPermissions::assignable());

            // Clear cache
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $this->info('Permissions force synced and cache cleared for root role.');
        }

        $roleGuest = Role::firstOrCreate(['name' => 'guest']);

        // Role guest: no permissions (already empty by default or sync empty array)
        $roleGuest->syncPermissions(['guest.dashboard', 'guest.sidebar']);
    }
}
