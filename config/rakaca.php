<?php

// config for Paparee/Rakaca
//
// Catatan: blok config `whatsapp` dan `aduan` sudah dipindahkan ke
// bale/frasasti (config/frasasti.php) bersama fitur aduan.
return [
    'ticket' => [
        'auto_cancel_hours' => (int) env('RAKACA_TICKET_AUTO_CANCEL_HOURS', 72),
        'rejection_min_chars' => (int) env('RAKACA_TICKET_REJECTION_MIN_CHARS', 10),
    ],

    // Kalender kerja untuk hitung tenggat SLA. Mengikuti APP_TIMEZONE.
    'sla' => [
        'work_start' => env('RAKACA_SLA_WORK_START', '07:30'),
        'work_end' => env('RAKACA_SLA_WORK_END', '15:45'),
        'work_days' => array_map('intval', explode(',', (string) env('RAKACA_SLA_WORK_DAYS', '1,2,3,4,5'))),
    ],
];
