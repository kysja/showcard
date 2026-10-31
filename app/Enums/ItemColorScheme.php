<?php

namespace App\Enums;

enum ItemColorScheme: string
{
    case WhiteOnBlack = 'white-on-black';
    case BlackOnWhite = 'black-on-white';

    public function label(): string
    {
        return match ($this) {
            self::WhiteOnBlack => 'White on Black',
            self::BlackOnWhite => 'Black on White',
        };
    }

    public function backgroundClass(): string
    {
        return match ($this) {
            self::WhiteOnBlack => 'bg-black',
            self::BlackOnWhite => 'bg-white',
        };
    }

    public function textClass(): string
    {
        return match ($this) {
            self::WhiteOnBlack => 'text-white',
            self::BlackOnWhite => 'text-black',
        };
    }

    public function mutedTextClass(): string
    {
        return match ($this) {
            self::WhiteOnBlack => 'text-white/60',
            self::BlackOnWhite => 'text-black/60',
        };
    }
}
