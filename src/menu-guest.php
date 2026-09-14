<?php

/**
 * Menu definisi untuk package paparee/rakaca (Guest Layout).
 *
 * Beranda + Pengajuan + Bantuan + Bale — dibaca oleh RakacaGuestSidebar
 * via MenuRegistry fallback (tanpa ubah bale-core) dan difilter permission.
 */
return [
    'type'   => 'guest',
    'groups' => [
        [
            'key'   => 'guest-home',
            'label' => 'Beranda',
            'icon'  => 'layout-dashboard',
            'items' => [
                [
                    'label'      => 'Dashboard',
                    'url'        => 'guest',
                    'icon'       => 'layout-dashboard',
                    'permission' => 'guest.dashboard',
                ],
            ],
        ],
        [
            'key'   => 'guest-pengajuan',
            'label' => 'Pengajuan',
            'icon'  => 'file-text',
            'items' => [
                [
                    'label'      => 'Daftar Pengajuan',
                    'url'        => 'guest/submissions',
                    'icon'       => 'list',
                    'permission' => null,
                ],
                [
                    'label'      => 'Buat Pengajuan',
                    'url'        => 'guest/submissions/create',
                    'icon'       => 'plus',
                    'permission' => null,
                ],
            ],
        ],
        [
            'key'   => 'guest-bantuan',
            'label' => 'Bantuan',
            'icon'  => 'help-circle',
            'items' => [
                [
                    'label'      => 'Pusat Bantuan',
                    'url'        => 'bantuan',
                    'icon'       => 'help-circle',
                    'permission' => null,
                ],
            ],
        ],
        [
            'key'   => 'guest-bale',
            'label' => 'Bale',
            'icon'  => 'building-2',
            'items' => [
                [
                    'label'      => 'Pilih Bale',
                    'url'        => 'select-bale',
                    'icon'       => 'building-2',
                    'permission' => 'select-bale',
                ],
            ],
        ],
    ],
];
