<?php

/**
 * Menu definisi untuk package paparee/rakaca (Landlord Layout).
 *
 * Mendefinisikan dua grup:
 * - 'rakaca': manajemen layanan, formulir, submission, dan personal service
 * - 'bale-cms-mgmt': manajemen organisasi, bale list, user, dan analytics
 */
return [
    'type' => 'landlord',

    'groups' => [
        [
            'key' => 'rakaca',
            'label' => 'Rakaca',
            'icon' => 'layers',
            'items' => [
                [
                    'label' => 'Service',
                    'url' => 'rakaca/services',
                    'icon' => 'layers',
                    'permission' => 'service.read',
                ],
                [
                    'label' => 'Formulir',
                    'url' => 'rakaca/forms',
                    'icon' => 'clipboard-list',
                    'permission' => 'form.read',
                ],
                [
                    'label' => 'Submission',
                    'url' => 'rakaca/submissions',
                    'icon' => 'file-text',
                    'permission' => 'submission.read',
                ],
                [
                    'label' => 'Personal Service',
                    'url' => 'rakaca/personal-services',
                    'icon' => 'user-check',
                    'permission' => 'personal-service.read',
                ],
            ],
        ],

        [
            'key' => 'bale-cms-mgmt',
            'label' => 'Bale CMS',
            'icon' => 'building-2',
            'items' => [
                [
                    'label' => 'Organization',
                    'url' => 'rakaca/organizations',
                    'icon' => 'building-2',
                    'permission' => 'organization.read',
                ],
                [
                    'label' => 'Bale List',
                    'url' => 'rakaca/bale-lists',
                    'icon' => 'server',
                    'permission' => 'bale-list.read',
                ],
                [
                    'label' => 'Bale User',
                    'url' => 'rakaca/bale-users',
                    'icon' => 'user-cog',
                    'permission' => 'bale-user.read',
                ],
                [
                    'label' => 'Analytics',
                    'url' => 'rakaca/analytics',
                    'icon' => 'bar-chart-3',
                    'permission' => 'analytic.read',
                ],
            ],
        ],
    ],
];
