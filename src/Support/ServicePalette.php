<?php

namespace Paparee\Rakaca\Support;

/**
 * Palet warna layanan Rakaca.
 *
 * Nilai disimpan sebagai token (`rakaca_services.color`), bukan hex, dan class
 * Tailwind ditulis lengkap di sini supaya ikut ter-compile — class yang dirakit
 * dinamis (`from-{$tone}-500`) tidak akan terbaca scanner Tailwind.
 */
final class ServicePalette
{
    public const DEFAULT = 'indigo';

    public const TONES = [
        'indigo' => [
            'label' => 'Indigo',
            'icon' => 'from-indigo-500 to-indigo-600 shadow-indigo-500/30',
            'button' => 'bg-indigo-600 hover:bg-indigo-700',
            'title' => 'group-hover:text-indigo-600 dark:group-hover:text-indigo-300',
            'swatch' => 'bg-indigo-500',
        ],
        'sky' => [
            'label' => 'Sky',
            'icon' => 'from-sky-500 to-sky-600 shadow-sky-500/30',
            'button' => 'bg-sky-700 hover:bg-sky-800',
            'title' => 'group-hover:text-sky-600 dark:group-hover:text-sky-300',
            'swatch' => 'bg-sky-500',
        ],
        'cyan' => [
            'label' => 'Cyan',
            'icon' => 'from-cyan-500 to-cyan-600 shadow-cyan-500/30',
            'button' => 'bg-cyan-700 hover:bg-cyan-800',
            'title' => 'group-hover:text-cyan-600 dark:group-hover:text-cyan-300',
            'swatch' => 'bg-cyan-500',
        ],
        'teal' => [
            'label' => 'Teal',
            'icon' => 'from-teal-500 to-teal-600 shadow-teal-500/30',
            'button' => 'bg-teal-700 hover:bg-teal-800',
            'title' => 'group-hover:text-teal-600 dark:group-hover:text-teal-300',
            'swatch' => 'bg-teal-500',
        ],
        'emerald' => [
            'label' => 'Emerald',
            'icon' => 'from-emerald-500 to-emerald-600 shadow-emerald-500/30',
            'button' => 'bg-emerald-700 hover:bg-emerald-800',
            'title' => 'group-hover:text-emerald-600 dark:group-hover:text-emerald-300',
            'swatch' => 'bg-emerald-500',
        ],
        'lime' => [
            'label' => 'Lime',
            'icon' => 'from-lime-500 to-lime-600 shadow-lime-500/30',
            'button' => 'bg-lime-700 hover:bg-lime-800',
            'title' => 'group-hover:text-lime-700 dark:group-hover:text-lime-300',
            'swatch' => 'bg-lime-500',
        ],
        'amber' => [
            'label' => 'Amber',
            'icon' => 'from-amber-500 to-amber-600 shadow-amber-500/30',
            'button' => 'bg-amber-700 hover:bg-amber-800',
            'title' => 'group-hover:text-amber-600 dark:group-hover:text-amber-300',
            'swatch' => 'bg-amber-500',
        ],
        'orange' => [
            'label' => 'Orange',
            'icon' => 'from-orange-500 to-orange-600 shadow-orange-500/30',
            'button' => 'bg-orange-700 hover:bg-orange-800',
            'title' => 'group-hover:text-orange-600 dark:group-hover:text-orange-300',
            'swatch' => 'bg-orange-500',
        ],
        'rose' => [
            'label' => 'Rose',
            'icon' => 'from-rose-500 to-rose-600 shadow-rose-500/30',
            'button' => 'bg-rose-700 hover:bg-rose-800',
            'title' => 'group-hover:text-rose-600 dark:group-hover:text-rose-300',
            'swatch' => 'bg-rose-500',
        ],
        'fuchsia' => [
            'label' => 'Fuchsia',
            'icon' => 'from-fuchsia-500 to-fuchsia-600 shadow-fuchsia-500/30',
            'button' => 'bg-fuchsia-700 hover:bg-fuchsia-800',
            'title' => 'group-hover:text-fuchsia-600 dark:group-hover:text-fuchsia-300',
            'swatch' => 'bg-fuchsia-500',
        ],
        'purple' => [
            'label' => 'Purple',
            'icon' => 'from-purple-500 to-purple-600 shadow-purple-500/30',
            'button' => 'bg-purple-700 hover:bg-purple-800',
            'title' => 'group-hover:text-purple-600 dark:group-hover:text-purple-300',
            'swatch' => 'bg-purple-500',
        ],
        'slate' => [
            'label' => 'Slate',
            'icon' => 'from-slate-500 to-slate-600 shadow-slate-500/30',
            'button' => 'bg-slate-700 hover:bg-slate-800',
            'title' => 'group-hover:text-slate-600 dark:group-hover:text-slate-300',
            'swatch' => 'bg-slate-500',
        ],
    ];

    /**
     * Warna layanan, fallback ke warna brand bila kosong/tidak dikenal.
     *
     * @return array{label: string, icon: string, button: string, title: string, swatch: string}
     */
    public static function get(?string $color): array
    {
        return self::TONES[$color] ?? self::TONES[self::DEFAULT];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::TONES);
    }
}
