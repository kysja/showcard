<?php

/**
 * Native UI — Theme Tokens
 *
 * Published via `php artisan vendor:publish --tag=native-ui-config`.
 * Edit to customize your app's visual identity in one place.
 *
 * For dynamic per-tenant theming, use Native\Mobile\UI\Theme::merge([...])
 * from a service provider. Runtime merges deep-merge on top of these values.
 *
 * Decision log: /docs/NATIVE-UI-REWRITE-PLAN.md (D — theme layer)
 */

return [

    /*
    |---------------------------------------------------------------------------
    | Theme
    |---------------------------------------------------------------------------
    |
    | 17 color tokens, 4 radii, 4 font sizes, font family.
    |
    | "on-X" means "color of content placed ON a surface of color X"
    |   — i.e., text/icons on that background.
    |
    | Color tokens accept:
    |   - CSS hex: '#B91C1C', '#F00', or with alpha '#8B5CF680' (#RRGGBBAA)
    |   - Tailwind palette names: 'red-300', 'orange-800'
    |   - Opacity modifiers on either: 'red-300/20', '#8B5CF6/50'
    |
    | Dark mode is auto-derived from `light` when `dark` is not set. To opt
    | into explicit dark tokens, fill out the `dark` block.
    |
    | The default pairs meet WCAG AA (4.5:1) — if you customize, keep each
    | `on-*` color at 4.5:1 contrast against its background token.
    |
    */

    'theme' => [

        'light' => [
            // Primary brand color — used for filled buttons, active states, key accents.
            'primary' => '#4338CA',
            'on-primary' => '#FFFFFF',

            // Secondary / muted action color.
            'secondary' => '#475569',
            'on-secondary' => '#FFFFFF',

            // Surface = cards, sheets, dialogs. Background = page root.
            'surface' => '#FFFFFF',
            'on-surface' => '#111827',
            'background' => '#F3F4F8',
            'on-background' => '#111827',

            // Surface variant = filled text fields, muted tonal surfaces.
            // on-surface-variant = muted label/hint text on those surfaces.
            'surface-variant' => '#E9EBF2',
            'on-surface-variant' => '#565D6B',

            // Outline = neutral borders (text fields, dividers, cards).
            'outline' => '#DDE0E8',

            // Destructive actions — maps to `variant="destructive"` on components.
            'destructive' => '#B91C1C',
            'on-destructive' => '#FFFFFF',

            // Tertiary accent — for highlights, badges, emphasis not covered by primary.
            'accent' => '#C2410C',
            'on-accent' => '#FFFFFF',
        ],

        'dark' => [
            // Leave empty or partial to auto-derive from `light` (luminance inversion).
            // Specify any token here to override the derived value.
            'primary' => '#A5B4FC',
            'on-primary' => '#111827',

            'secondary' => '#94A3B8',
            'on-secondary' => '#0F172A',

            'surface' => '#1B1E26',
            'on-surface' => '#F3F4F6',
            'background' => '#0F1117',
            'on-background' => '#F3F4F6',

            'surface-variant' => '#262A33',
            'on-surface-variant' => '#AEB4C0',

            'outline' => '#343946',

            'destructive' => '#F87171',
            'on-destructive' => '#0F172A',

            'accent' => '#FDBA74',
            'on-accent' => '#0F172A',
        ],

        // Corner radii (points / dp).
        'radius-sm' => 4,
        'radius-md' => 8,
        'radius-lg' => 16,
        'radius-full' => 9999,

        // Font size scale (points / sp).
        'font-sm' => 14,
        'font-md' => 16,
        'font-lg' => 20,
        'font-xl' => 24,
    ],

    'fonts' => [
        'default' => 'Inter-Regular',
        'accent' => 'Archivo+Black-Regular',
        'roboto-mono' => 'RobotoMono-Regular',
    ],

];
