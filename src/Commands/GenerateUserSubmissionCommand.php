<?php

namespace Paparee\Rakaca\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaSubmission;

class GenerateUserSubmissionCommand extends Command
{
    protected $signature = 'rakaca:make-user-submission 
                            {--username= : Username of the user} 
                            {--form_slug= : Slug of the form}';

    protected $description = 'Create a new submission for a user';

    public function handle(): int
    {
        $username = $this->option('username') ?? $this->ask('Masukkan username user');

        $user = DB::table('users')->where('username', $username)->first();

        if (! $user) {
            $this->error("❌ User dengan username '{$username}' tidak ditemukan.");

            return self::FAILURE;
        }

        $formSlug = $this->option('form_slug');

        if (! $formSlug) {
            $forms = Form::all();

            if ($forms->isEmpty()) {
                $this->error('❌ Tidak ada form yang tersedia di tabel rakaca_forms.');

                return self::FAILURE;
            }

            $formNames = $forms->pluck('name', 'slug')->toArray();
            $selectedName = $this->choice('Pilih Form', array_values($formNames));
            $formSlug = array_search($selectedName, $formNames);
        }

        $form = Form::where('slug', $formSlug)->first();

        if (! $form) {
            $this->error("❌ Form dengan slug '{$formSlug}' tidak ditemukan.");

            return self::FAILURE;
        }

        $submission = RakacaSubmission::create([
            'user_uuid' => $user->uuid,
            'rakaca_form_id' => $form->id,
            'code' => uniqid(),
            'status' => 'pending',
            'items' => [],
        ]);

        $this->info('✅ Submission berhasil dibuat!');
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $submission->id],
                ['User', $username." ({$user->uuid})"],
                ['Form', $form->name],
                ['Code', $submission->code],
                ['Status', $submission->status],
            ]
        );

        return self::SUCCESS;
    }
}
