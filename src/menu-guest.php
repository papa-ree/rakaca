<?php

/**
 * Menu definisi untuk package paparee/rakaca (Guest Layout).
 *
 * Beranda + Pengajuan + Bale — dibaca oleh RakacaGuestSidebar
 * via MenuRegistry fallback (tanpa ubah bale-core) dan difilter permission.
 *
 * Catatan: menu "Bantuan" (/bantuan) sudah dipindahkan ke bale/frasasti
 * dan tidak lagi terdaftar di sini.
 */
return [
    'type' => 'guest',
    'groups' => [
        [
            'key' => 'guest-home',
            'label' => 'Beranda',
            'icon' => 'layout-dashboard',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'url' => 'guest',
                    'icon' => 'layout-dashboard',
                    'permission' => 'guest.dashboard',
                ],
            ],
        ],
        [
            'key' => 'guest-pengajuan',
            'label' => 'Pengajuan',
            'icon' => 'file-text',
            'items' => [
                [
                    'label' => 'Daftar Pengajuan',
                    'url' => 'guest/submissions',
                    'icon' => 'list',
                    'permission' => null,
                ],
                [
                    'label' => 'Buat Pengajuan',
                    'url' => 'guest/submissions/create',
                    'icon' => 'plus',
                    'permission' => null,
                ],
            ],
        ],
        [
            'key' => 'guest-bale',
            'label' => 'Bale',
            'icon' => 'building-2',
            'items' => [
                [
                    'label' => 'Pilih Bale',
                    'url' => 'select-bale',
                    'icon' => 'building-2',
                    'permission' => 'select-bale',
                ],
            ],
        ],
    ],
];
