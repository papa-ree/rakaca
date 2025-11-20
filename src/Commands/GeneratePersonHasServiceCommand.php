<?php

namespace Paparee\Rakaca\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GeneratePersonHasServiceCommand extends Command
{
    protected $signature = 'rakaca:make-person-service';

    protected $description = 'Menautkan user ke service yang sudah ada di tabel services (mode interaktif).';

    public function handle()
    {
        $this->info('🔧 Menautkan user ke service...');

        // Cek tabel
        if (!Schema::hasTable('rakaca_services') || !Schema::hasTable('person_has_services')) {
            $this->error('Tabel "services" atau "person_has_services" belum ada. Jalankan migrasi terlebih dahulu.');
            return Command::FAILURE;
        }

        // Input username
        $username = $this->ask('Masukkan username user');

        $user = User::where('username', $username)->first();

        if (!$user) {
            $this->error("❌ User dengan username '{$username}' tidak ditemukan.");
            return Command::FAILURE;
        }

        $userUuid = $user->uuid;
        $this->info("👤 Ditemukan user: {$user->name} ({$userUuid})");

        // Ambil daftar service yang aktif
        $services = DB::table('rakaca_services')->where('actived', true)->pluck('name', 'slug')->toArray();

        if (empty($services)) {
            $this->error('❌ Tidak ada service yang aktif di tabel services.');
            return Command::FAILURE;
        }

        // Pilih service dari daftar
        $serviceSlug = $this->choice('Pilih service yang ingin ditautkan:', array_keys($services));

        // Ambil data service
        $service = DB::table('rakaca_services')->where('slug', $serviceSlug)->first();

        // Cek apakah relasi sudah ada
        $exists = DB::table('person_has_services')
            ->where('user_uuid', $userUuid)
            ->where('rakaca_service_id', $service->id)
            ->exists();

        if ($exists) {
            $this->warn("⚠️ User '{$username}' sudah memiliki relasi dengan service '{$service->name}'.");
            return Command::SUCCESS;
        }

        // Insert data baru
        DB::table('person_has_services')->insert([
            'id' => (string) Str::uuid(),
            'user_uuid' => $userUuid,
            'rakaca_service_id' => $service->id,
            'actived' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("✅ Relasi antara user '{$username}' dan service '{$service->name}' berhasil dibuat.");
        return Command::SUCCESS;
    }
}
