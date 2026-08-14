<?php

namespace Paparee\Rakaca\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Paparee\Rakaca\Models\AduanCategory;

class AduanCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['SSO', 'Infrastruktur', 'Aplikasi', 'Lainnya'] as $name) {
            AduanCategory::firstOrCreate(
                ['name' => $name],
                ['id' => Str::uuid()]
            );
        }
    }
}
