<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Guest;

use App\Models\User;
use Bale\Cms\Models\BaleList;
use Bale\Cms\Models\BaleOrganization;
use Bale\Cms\Models\BaleUser;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Livewire\Pages\Guest\SelectBale\Index;

/**
 * Demo Test: Memilih Bale (Modul Rakaca)
 *
 * File ini mendemonstrasikan cara melakukan automated testing untuk komponen Livewire
 * menggunakan Pest PHP.
 */
it('menampilkan daftar bale yang dimiliki oleh user', function () {
    // 1. Persiapan Data (Setup)
    // Membuat organisasi sebagai induk dari Bale
    $org = BaleOrganization::create([
        'id' => Str::uuid(),
        'name' => 'Demo Org',
        'slug' => 'demo-org',
    ]);

    $user = User::factory()->create();

    // Membuat 2 Bale (Project)
    $bale1 = BaleList::create([
        'id' => Str::uuid(),
        'organization_id' => $org->id,
        'name' => 'Bale Satu',
        'slug' => 'bale-satu',
        'database_host' => 'localhost',
        'database_name' => 'bale1_db',
        'database_username' => 'user',
        'database_password' => 'secret',
        'is_active' => true,
    ]);

    $bale2 = BaleList::create([
        'id' => Str::uuid(),
        'organization_id' => $org->id,
        'name' => 'Bale Dua',
        'slug' => 'bale-dua',
        'database_host' => 'localhost',
        'database_name' => 'bale2_db',
        'database_username' => 'user',
        'database_password' => 'secret',
        'is_active' => true,
    ]);

    // Menghubungkan user hanya ke Bale Satu
    BaleUser::create([
        'id' => Str::uuid(),
        'bale_id' => $bale1->id,
        'user_uuid' => $user->uuid,
        'role' => 'admin',
    ]);

    // 2. Simulasi Bertindak sebagai User (ActingAs)
    // 3. Mengetes Komponen Livewire
    Livewire::actingAs($user)
        ->test(Index::class)
        // Pastikan Bale Satu muncul di UI
        ->assertSee('Bale Satu')
        // Pastikan Bale Dua TIDAK muncul (karena user tidak punya akses)
        ->assertDontSee('Bale Dua');
});

it('dapat memilih bale dan diarahkan ke overview', function () {
    // 1. Setup
    $org = BaleOrganization::create([
        'id' => Str::uuid(),
        'name' => 'Demo Org',
        'slug' => 'demo-org',
    ]);
    $user = User::factory()->create();
    $bale = BaleList::create([
        'id' => Str::uuid(),
        'organization_id' => $org->id,
        'name' => 'Bale Active',
        'slug' => 'bale-active',
        'database_host' => 'localhost',
        'database_name' => 'active_db',
        'database_username' => 'user',
        'database_password' => 'secret',
        'is_active' => true,
    ]);
    BaleUser::create([
        'id' => Str::uuid(),
        'bale_id' => $bale->id,
        'user_uuid' => $user->uuid,
        'role' => 'editor',
    ]);

    // 2. Simulasi Aksi
    Livewire::actingAs($user)
        ->test(Index::class)
        // Memanggil fungsi selectBale dengan ID bale yang dibuat
        ->call('selectBale', $bale->id)
        // 3. Asersi Hasil
        // Cek apakah session tersimpan dengan benar sesuai logika di komponen
        ->assertSessionHas('bale_active_uuid', $bale->id)
        ->assertSessionHas('bale_active_slug', $bale->slug)
        ->assertSessionHas('bale_active_user_role', 'editor')
        // Cek apakah diarahkan ke route yang benar
        ->assertRedirect(route('bale.cms.overview'));
});
