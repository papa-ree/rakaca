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
];
