<?php

namespace App\Enums;

enum ItemKind
{
    case Email;
    case Phone;
    case Link;
    case Text;

    public static function detect(string $content): self
    {
        $content = trim($content);

        return match (true) {
            filter_var($content, FILTER_VALIDATE_EMAIL) !== false => self::Email,
            preg_match('/^\+?[\d\s().-]+$/', $content) === 1 && preg_match_all('/\d/', $content) >= 7 => self::Phone,
            preg_match('#^(https?://|www\.)\S+$#i', $content) === 1 => self::Link,
            default => self::Text,
        };
    }

    public function label(): string
    {
        return $this->name;
    }
}
