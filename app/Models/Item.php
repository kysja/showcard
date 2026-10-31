<?php

namespace App\Models;

use App\Enums\ItemColorScheme;
use App\Enums\ItemFont;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'content',
        'font',
        'color_scheme',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'font' => ItemFont::class,
            'color_scheme' => ItemColorScheme::class,
        ];
    }
}
