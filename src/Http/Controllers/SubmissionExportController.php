<?php

namespace Paparee\Rakaca\Http\Controllers;

use Illuminate\Http\Response;
use Paparee\Rakaca\Models\RakacaSubmission;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SubmissionExportController
{
    /** @var array<int, string> */
    private const HEADERS = [
        'Kode',
        'Layanan',
        'Form',
        'Pemohon',
        'Status',
        'Dibuat',
        'Berkas Final',
        'Target SLA (hari kerja)',
        'Tenggat SLA',
        'Selesai',
        'Status SLA',
    ];

    public function download(string $format): Response
    {
        $rows = RakacaSubmission::query()
            ->with(['form.service', 'user', 'response'])
            ->latest('created_at')
            ->get()
            ->map(fn (RakacaSubmission $submission): array => [
                $submission->code,
                $submission->form?->service?->name ?? '-',
                $submission->form?->name ?? '-',
                $submission->user?->name ?? '-',
                $submission->statusLabel,
                $submission->created_at?->format('Y-m-d H:i'),
                $submission->files_finalized_at?->format('Y-m-d H:i'),
                $submission->sla_target_days,
                $submission->sla_due_at?->format('Y-m-d H:i'),
                $submission->response?->resolved_at?->format('Y-m-d H:i'),
                $submission->slaLabel() ?? '-',
            ])
            ->all();

        return $format === 'xlsx'
            ? $this->xlsx($rows)
            : $this->csv($rows);
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     */
    private function csv(array $rows): Response
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, self::HEADERS);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = "\xEF\xBB\xBF".stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rakaca-submissions-'.now()->format('Ymd-His').'.csv"',
        ]);
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     */
    private function xlsx(array $rows): Response
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([self::HEADERS, ...$rows]);

        ob_start();
        (new Xlsx($spreadsheet))->save('php://output');
        $content = ob_get_clean();

        $spreadsheet->disconnectWorksheets();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="rakaca-submissions-'.now()->format('Ymd-His').'.xlsx"',
        ]);
    }
}
