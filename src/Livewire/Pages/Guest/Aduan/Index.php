<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Aduan;

use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Paparee\Rakaca\Models\AduanCategory;
use Paparee\Rakaca\Services\AduanService;

#[Layout('rakaca::layouts.guest')]
#[Title('Rakaca | Bantuan')]
class Index extends Component
{
    public string $nama_lengkap = '';

    public string $nip = '';

    public string $wa_number = '';

    public string $aduan_category_id = '';

    public string $deskripsi = '';

    public string $recaptchaToken = '';

    protected function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'min:21', 'max:21'],
            'wa_number' => ['required', 'digits_between:9,15'],
            'aduan_category_id' => ['required', 'exists:rakaca_aduan_categories,id'],
            'deskripsi' => ['required', 'string', 'min:10', 'max:5000'],
            'recaptchaToken' => ['required'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks.',
            'nama_lengkap.max' => 'Nama lengkap tidak boleh lebih dari :max karakter.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.min' => 'NIP harus terdiri dari 18 digit angka.',
            'nip.max' => 'NIP harus terdiri dari 18 digit angka.',
            'wa_number.required' => 'Nomor WhatsApp wajib diisi.',
            'wa_number.digits_between' => 'Nomor WhatsApp harus terdiri dari :min sampai :max digit angka.',
            'aduan_category_id.required' => 'Silakan pilih jenis aduan terlebih dahulu.',
            'aduan_category_id.exists' => 'Jenis aduan yang dipilih tidak valid.',
            'deskripsi.required' => 'Deskripsi keluhan wajib diisi.',
            'deskripsi.string' => 'Deskripsi keluhan harus berupa teks.',
            'deskripsi.min' => 'Deskripsi keluhan minimal :min karakter.',
            'deskripsi.max' => 'Deskripsi keluhan tidak boleh lebih dari :max karakter.',
            'recaptchaToken.required' => 'Verifikasi keamanan belum selesai, silakan tunggu sesaat lalu coba lagi.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'nama_lengkap' => __('Nama Lengkap'),
            'nip' => __('NIP'),
            'wa_number' => __('No. WhatsApp'),
            'aduan_category_id' => __('Jenis Aduan'),
            'deskripsi' => __('Deskripsi Keluhan'),
            'recaptchaToken' => __('reCAPTCHA'),
        ];
    }

    public function submit(): void
    {
        $key = 'rakaca-aduan:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, (int) config('rakaca.aduan.max_attempts', 5))) {
            $this->addError('nama_lengkap', __('Terlalu banyak percobaan, silakan coba lagi dalam :seconds detik.', [
                'seconds' => RateLimiter::availableIn($key),
            ]));

            return;
        }

        RateLimiter::hit($key, (int) config('rakaca.aduan.decay_seconds', 60));

        $validated = $this->validate();
        try {
            $score = RecaptchaV3::verify($validated['recaptchaToken'], config('rakaca.aduan.recaptcha_action', 'aduan'));
        } catch (\Throwable $e) {
            report($e);
            $this->addError('recaptchaToken', __('Verifikasi reCAPTCHA gagal, silakan coba lagi.'));

            return;
        }

        if (! $score || $score < (float) config('rakaca.aduan.min_score', 0.5)) {
            $this->addError('recaptchaToken', __('Verifikasi reCAPTCHA gagal, silakan coba lagi.'));

            return;
        }

        $validated['nip'] = preg_replace('/\s+/', '', $validated['nip']);

        $service = app(AduanService::class);

        $aduan = $service->store([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nip' => $validated['nip'],
            'wa_number' => $validated['wa_number'],
            'aduan_category_id' => $validated['aduan_category_id'],
            'deskripsi' => $validated['deskripsi'],
        ]);

        $this->redirect($service->buildWhatsAppLink([
            'ref_code' => $aduan->ref_code,
            'nama_lengkap' => $aduan->nama_lengkap,
            'nip' => $aduan->nip,
            'aduan_category' => AduanCategory::find($validated['aduan_category_id'])?->name ?? '-',
            'deskripsi' => $aduan->deskripsi,
        ]));
    }

    #[Computed]
    public function categories()
    {
        return AduanCategory::query()
            ->orderByRaw("name = 'Lainnya'") // 'Lainnya' selalu di paling belakang
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.aduan.index');
    }
}
