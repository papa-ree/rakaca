# paparee/rakaca

Lapisan **service management** (GUI) untuk layanan Aptika. Package ini berada di
atas `bale/cms` dan menjadi service aktif untuk tenant — mengelola pengajuan
layanan pengguna, formulir dinamis, dan data organisasi.

> **Catatan:** fitur Aduan Masuk sudah diekstrak menjadi package mandiri
> [`bale/frasasti`](../frasasti). Tabel `rakaca_aduans` dan
> `rakaca_aduan_categories` sengaja **dipertahankan** di database sebagai arsip
> data lama; kode(fitur) Aduan sudah tidak ada di package ini. Gunakan
> `php artisan frasasti:import-legacy-aduan` untuk memindahkan data tersebut.

## Kebutuhan

| Dependency | Alasan |
|------------|--------|
| `bale/core` | Auth, permission, komponen UI, layout |
| `bale/cms` | Multi-tenancy, tenant connection, form dinamis |
| `bale/api` | Endpoint API dan token scope |
| `awssat/laravel-visits` | Pencatatan visitor |

## Instalasi

```bash
composer require paparee/rakaca
```

```bash
php artisan vendor:publish --tag="rakaca:migrations"
php artisan migrate
```

```bash
php artisan vendor:publish --tag="rakaca:config"
```

## Command

| Command | Fungsi |
|---------|--------|
| `rakaca:install` | Seed permission bawaan |
| `rakaca:publish-migration` | Publish migration stub ke aplikasi (opsi All/Auto/Specific) |
| `rakaca:make-form` | Generate form dinamis baru dan menempelkannya ke service |
| `rakaca:make-service` | Generate service baru, opsional menempelkannya ke user |
| `rakaca:make-person-service` | Menautkan user ke service yang sudah ada (mode interaktif) |
| `rakaca:make-user-submission` | Membuat pengajuan baru untuk seorang user |
| `rakaca:auto-cancel` | Batalkan otomatis tiket menunggu-berkas yang belum difinalisasi dalam 3x24 jam |

## Modul

### Guest (publik)

| Komponen | Keterangan |
|----------|------------|
| `Guest\Dashboard\Index` | Dashboard layanan aktif & pengajuan berjalan |
| `Guest\SelectBale\Index` | Pemilihan tenant sebelum mengakses layanan |
| `Guest\Submission\Index` | Daftar pengajuan milik pengguna |
| `Guest\Submission\Create` | Membuat pengajuan baru |
| `Guest\Submission\Edit` | Editing pengajuan yang masih bisa diubah |
| `Guest\Submission\Show` | Detail pengajuan |

### Landlord (admin)

| Komponen | Keterangan |
|----------|------------|
| `Landlord\Dashboard\Index` | Dashboard ringkasan |
| `Landlord\BaleList\*` | Manajemen daftar tenant (Bale) |
| `Landlord\Organization\*` | Manajemen organisasi |
| `Landlord\Service\Index` / `Form` | Manajemen layanan |
| `Landlord\Form\Index` / `Form` | Manajemen form dinamis |
| `Landlord\Submission\Index` / `Detail` | Monitoring pengajuan masuk |
| `Landlord\PersonalService\*` | Manajemen layanan personal |
| `Landlord\Analytic\*` | Analitik layanan |
| `Landlord\BaleUser\Index` / `Form` | Manajemen pengguna tenant |

## Testing

```bash
vendor\bin\pest packages\rakaca
```

> Saat ini terdapat 2 test yang gagal pada `SubmissionDetailTest`
> (`public method [reject] not found`) — warisan dari refactor, belum
> diperbaiki dan tidak berkaitan dengan fitur Aduan.

## License

MIT. Lihat [LICENSE.md](LICENSE.md).
