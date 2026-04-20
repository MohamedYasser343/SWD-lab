<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Author = 'author';
    case Reader = 'reader';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Author => 'Author',
            self::Reader => 'Reader',
        };
    }

    public function canAccessAdmin(): bool
    {
        return $this === self::Admin || $this === self::Author;
    }
}
