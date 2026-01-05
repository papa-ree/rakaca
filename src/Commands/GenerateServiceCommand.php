<?php

namespace Paparee\Rakaca\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GenerateServiceCommand extends Command
{
    protected $signature = 'rakaca:make-service 
        {--name : Service name, e.g. Bale CMS} 
        {--slug= : Slug for the service (optional)} 
        {--user= : User UUID to attach (optional)}';

    protected $description = 'Generate a new service and optionally attach it to a user';

    public function handle()
    {
        $name = $this->option('name');
        $slug = $this->option('slug') ?? Str::slug($name);
        $userUuid = $this->option('user');

        // Pastikan tabel tersedia
        if (!Schema::hasTable('rakaca_services') || !Schema::hasTable('person_has_services')) {
            $this->error('Tabel "services" dan "person_has_services" belum ada. Jalankan migrasi terlebih dahulu.');
            return Command::FAILURE;
        }

        // Cek apakah service sudah ada
        $service = DB::table('rakaca_services')->where('slug', $slug)->first();

        if (!$service) {
            $serviceId = (string) Str::uuid();

            DB::table('rakaca_services')->insert([
                'id' => $serviceId,
                'name' => $name,
                'slug' => $slug,
                'actived' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("✅ Service '{$name}' berhasil dibuat dengan slug '{$slug}'.");
        } else {
            $serviceId = $service->id;
            $this->warn("⚠️ Service '{$name}' sudah ada dengan slug '{$slug}'.");
        }

        // Jika ada user_uuid diberikan, tambahkan relasi
        if ($userUuid) {
            $exists = DB::table('person_has_services')
                ->where('user_uuid', $userUuid)
                ->where('rakaca_service_id', $serviceId)
                ->exists();

            if (!$exists) {
                DB::table('person_has_services')->insert([
                    'id' => (string) Str::uuid(),
                    'user_uuid' => $userUuid,
                    'rakaca_service_id' => $serviceId,
                    'actived' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("✅ User '{$userUuid}' berhasil ditautkan ke service '{$name}'.");
            } else {
                $this->warn("⚠️ User '{$userUuid}' sudah memiliki service '{$name}'.");
            }
        }

        $this->info('✨ Proses selesai.');
        return Command::SUCCESS;
    }
}
