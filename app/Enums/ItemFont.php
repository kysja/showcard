<?php

namespace App\Enums;

enum ItemFont: string
{
    case Standard = 'System';
    case Mono = 'roboto-mono';

    public function label(): string
    {
        return $this->name;
    }

    public static function fromLabel(string $label): self
    {
        return collect(self::cases())->first(fn (self $font): bool => $font->label() === $label) ?? self::Standard;
    }
}
