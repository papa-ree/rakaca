<?php

// config for Paparee/Rakaca
return [
    'whatsapp' => [
        'support_number' => env('RAKACA_WA_SUPPORT_NUMBER', '6285126061182'),
    ],

    'aduan' => [
        'recaptcha_action' => env('RAKACA_ADUAN_RECAPTCHA_ACTION', 'aduan'),
        'min_score' => (float) env('RAKACA_ADUAN_RECAPTCHA_MIN_SCORE', 0.5),
        'max_attempts' => (int) env('RAKACA_ADUAN_MAX_ATTEMPTS', 5),
        'decay_seconds' => (int) env('RAKACA_ADUAN_DECAY_SECONDS', 60),
    ],
];
