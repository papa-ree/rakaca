<?php

namespace Paparee\Rakaca\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateFormCommand extends Command
{
    protected $signature = 'rakaca:make-form 
        {--name= : Form name, e.g. Formulir VPS} 
        {--slug= : Slug for the form (optional)} 
        {--service= : Service slug to attach (required)}';

    protected $description = 'Generate a new dynamic form and attach it to an existing service';

    public function handle(): int
    {
        $name = $this->option('name');
        $serviceSlug = $this->option('service');

        if (empty($name)) {
            $name = $this->ask('What is the form name?');
        }

        if (empty($serviceSlug)) {
            $serviceSlug = $this->ask('What is the service slug to attach this form to?');
        }

        $slug = $this->option('slug') ?? Str::slug($name);

        if (! Schema::hasTable('rakaca_services') || ! Schema::hasTable('rakaca_forms')) {
            $this->error('Tabel "rakaca_services" atau "rakaca_forms" belum ada. Jalankan migrasi terlebih dahulu.');

            return Command::FAILURE;
        }

        $service = DB::table('rakaca_services')->where('slug', $serviceSlug)->first();

        if (! $service) {
            $this->error("Service dengan slug '{$serviceSlug}' tidak ditemukan.");

            return Command::FAILURE;
        }

        $exists = DB::table('rakaca_forms')->where('slug', $slug)->exists();

        if ($exists) {
            $this->error("Form dengan slug '{$slug}' sudah ada.");

            return Command::FAILURE;
        }

        $formId = (string) Str::uuid();

        DB::table('rakaca_forms')->insert([
            'id' => $formId,
            'rakaca_service_id' => $service->id,
            'name' => $name,
            'slug' => $slug,
            'meta' => json_encode(['fields' => []]),
            'actived' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Form '{$name}' berhasil dibuat dengan slug '{$slug}' dan ditautkan ke service '{$service->name}'.");

        return Command::SUCCESS;
    }
}
