<?php

namespace Paparee\Rakaca;

use Bale\Api\Http\Controllers\Api\BaseApiController;
use Bale\Core\Services\MenuRegistry;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component as LivewireComponent;
use Livewire\Livewire;
use Paparee\Rakaca\Commands\AutoCancelTicketsCommand;
use Paparee\Rakaca\Commands\GenerateFormCommand;
use Paparee\Rakaca\Commands\GeneratePersonHasServiceCommand;
use Paparee\Rakaca\Commands\GenerateServiceCommand;
use Paparee\Rakaca\Commands\GenerateUserSubmissionCommand;
use Paparee\Rakaca\Commands\InstallRakacaCommand;
use Paparee\Rakaca\Commands\PublishMigrationCommand;
use Symfony\Component\Finder\Finder;

class RakacaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rakaca.php', 'rakaca');

        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        $commands = [
            'command.rakaca:make-service' => GenerateServiceCommand::class,
            'command.rakaca:make-person-service' => GeneratePersonHasServiceCommand::class,
            'command.rakaca:make-user-submission' => GenerateUserSubmissionCommand::class,
            'command.rakaca:make-form' => GenerateFormCommand::class,
            'command.rakaca:install' => InstallRakacaCommand::class,
            'command.rakaca:publish-migration' => PublishMigrationCommand::class,
            'command.rakaca:auto-cancel' => AutoCancelTicketsCommand::class,
        ];

        foreach ($commands as $key => $class) {
            $this->app->bind($key, $class);
        }

        $this->commands(array_keys($commands));
    }

    /**
     * Daftarkan scope API milik rakaca ke registry bale/api.
     */
    protected function registerApiScopes(): void
    {
        if (! function_exists('registerApiScopes')) {
            return;
        }

        registerApiScopes('rakaca', [
            'rakaca.form.read' => 'Membaca daftar dan detail formulir pengajuan.',
            'rakaca.submission.read' => 'Membaca daftar pengajuan (submission).',
            'rakaca.submission.write' => 'Membuat pengajuan baru.',
        ]);
    }

    /**
     * Method boot()
     *
     * Dipanggil setelah semua service diregistrasi.
     * Digunakan untuk load resource seperti:
     * - view
     * - migration
     * - konfigurasi
     * - Livewire component
     */
    public function boot(): void
    {
        $this->registerApiScopes();

        $this->app->booted(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

            if (class_exists(BaseApiController::class)) {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            }

            Schedule::command('rakaca:auto-cancel')->everyTenMinutes();
            // Register guest menu terpisah (tanpa ubah bale-core)
            try {
                $registry = $this->app->make(MenuRegistry::class);
                // Coba pakai registerFromProvider untuk menu-guest.php via refleksi manual
                $menuGuestPath = __DIR__.'/menu-guest.php';
                if (file_exists($menuGuestPath) && method_exists($registry, 'flush')) {
                    // Fallback: langsung include dan push ke groups via reflection (tidak ubah core)
                    $config = include $menuGuestPath;
                    if (is_array($config) && isset($config['type'], $config['groups'])) {
                        $ref = new \ReflectionClass($registry);
                        $prop = $ref->getProperty('groups');
                        $prop->setAccessible(true);
                        $existing = $prop->getValue($registry);
                        foreach ($config['groups'] as $group) {
                            $existing[] = array_merge($group, ['_type' => $config['type']]);
                        }
                        $prop->setValue($registry, $existing);
                    }
                }
            } catch (\Throwable $e) {
                // diam
            }
        });

        $this->registerViews();
        $this->loadMigrations();
        $this->offerPublishing();
        $this->registerLivewireComponents();
    }

    /**
     * Load migration langsung dari package.
     *
     * Dengan ini, user bisa langsung menjalankan migration
     * tanpa harus publish file ke aplikasi utama.
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'rakaca'
        );
    }

    /**
     * Publish file agar bisa diubah oleh user.
     */
    protected function offerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Publish config
        $this->publishes([
            __DIR__.'/../config/rakaca.php' => config_path('rakaca.php'),
        ], 'rakaca:config');

        $this->publishes($this->getMigrations(), 'rakaca:migrations');

        $this->publishes([
            __DIR__.'/../src/Database/Seeders/AduanCategorySeeder.php' => database_path('seeders/AduanCategorySeeder.php'),
        ], 'rakaca:seeders');

    }

    /**
     * Mengambil semua file migration dari direktori package.
     */
    protected function getMigrations(): array
    {
        $migrations = [];
        $sourcePath = __DIR__.'/../database/migrations/';

        // Pastikan direktori ada
        if (! is_dir($sourcePath)) {
            return $migrations;
        }

        // Loop semua file migration (baik .php maupun .stub)
        foreach (glob($sourcePath.'*.{php,stub}', GLOB_BRACE) as $file) {
            $filename = basename($file);

            // Jika file stub, ganti menjadi nama migration yang benar di aplikasi
            $targetFile = $this->getMigrationFileName($filename);

            $migrations[$file] = $targetFile;
        }

        return $migrations;
    }

    /**
     * Membuat nama file migration yang sesuai dengan timestamp laravel.
     */
    protected function getMigrationFileName(string $filename): string
    {
        $timestamp = date('Y_m_d_His');
        $migrationName = str_replace('.php.stub', '.php', $filename);

        return database_path('migrations/'.$timestamp.'_'.$migrationName);
    }

    /**
     * Registrasi semua Livewire Component yang ada di folder src/Livewire.
     *
     * Mekanisme:
     * - Cari semua file PHP di dalam folder Livewire
     * - Pastikan class tersebut adalah turunan Livewire\Component
     * - Buat alias secara otomatis dari struktur folder
     *
     * Contoh:
     *   src/Livewire/Dashboard.php
     *     => <livewire:rakaca.dashboard />
     */
    protected function registerLivewireComponents(): void
    {
        $namespace = 'Paparee\\Rakaca\\Livewire';
        $basePath = __DIR__.'/Livewire';

        // Jika folder Livewire tidak ada, hentikan proses
        if (! is_dir($basePath)) {
            return;
        }

        $finder = new Finder;
        $finder->files()->in($basePath)->name('*.php');

        foreach ($finder as $file) {
            $relativePathname = $file->getRelativePathname();

            // Normalisasi path (Windows/Linux)
            $nsPath = str_replace(['/', '\\'], '\\', $relativePathname);

            // Konversi ke FQCN (Fully Qualified Class Name)
            $class = $namespace.'\\'.Str::beforeLast($nsPath, '.php');

            // Skip jika class tidak ditemukan
            if (! class_exists($class)) {
                continue;
            }

            // Skip jika bukan turunan Livewire\Component
            if (! is_subclass_of($class, LivewireComponent::class)) {
                continue;
            }

            // Buat alias berdasarkan struktur folder (kebab-case)
            $withoutExt = Str::replaceLast('.php', '', $relativePathname);
            $segments = preg_split('#[\\/\\\\]#', $withoutExt);
            $kebab = array_map(fn ($s) => Str::kebab($s), $segments);

            $alias = 'rakaca.'.implode('.', $kebab);

            // Registrasi komponen ke Livewire
            Livewire::component($alias, $class);
        }
    }

    // public function configurePackage(Package $package): void
    // {
    //     /*
    //      * This class is a Package Service Provider
    //      *
    //      * More info: https://github.com/spatie/laravel-package-tools
    //      */
    //     $package
    //         ->name('rakaca')
    //         ->hasConfigFile()
    //         ->hasViews()
    //         ->hasMigration('create_rakaca_table')
    //         ->hasCommand(RakacaCommand::class);
    // }
}
