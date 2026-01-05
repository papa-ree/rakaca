<?php

namespace Paparee\Rakaca\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\Submission;

class GenerateUserSubmissionCommand extends Command
{
    protected $signature = 'rakaca:make-user-submission 
                            {--username= : Username of the user} 
                            {--service_slug= : Slug of the service}';

    protected $description = 'Create a new submission for a user';

    public function handle(): int
    {
        $username = $this->option('username') ?? $this->ask('Masukkan username user');

        // Look up user
        $user = DB::table('users')->where('username', $username)->first();

        if (!$user) {
            $this->error("❌ User dengan username '{$username}' tidak ditemukan.");
            return self::FAILURE;
        }

        $serviceSlug = $this->option('service_slug');

        if (!$serviceSlug) {
            $services = RakacaService::all();

            if ($services->isEmpty()) {
                $this->error("❌ Tidak ada service yang tersedia di tabel rakaca_services.");
                return self::FAILURE;
            }

            $serviceNames = $services->pluck('name', 'slug')->toArray();
            $selectedName = $this->choice('Pilih Service', array_values($serviceNames));
            $serviceSlug = array_search($selectedName, $serviceNames);
        }

        $service = RakacaService::where('slug', $serviceSlug)->first();

        if (!$service) {
            $this->error("❌ Service dengan slug '{$serviceSlug}' tidak ditemukan.");
            return self::FAILURE;
        }

        $submission = Submission::create([
            'user_uuid' => $user->uuid,
            'rakaca_service_id' => $service->id,
            'code' => uniqid(),
            'status' => 'pending',
            'data' => [],
        ]);

        $this->info("✅ Submission berhasil dibuat!");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $submission->id],
                ['User', $username . " ({$user->uuid})"],
                ['Service', $service->name],
                ['Code', $submission->code],
                ['Status', $submission->status],
            ]
        );

        return self::SUCCESS;
    }
}
