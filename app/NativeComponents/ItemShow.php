<?php

namespace App\NativeComponents;

use App\Enums\ItemColorScheme;
use App\Models\Item;
use Aulog\ScreenOrientation\Facades\ScreenOrientation;
use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class ItemShow extends NativeComponent
{
    public string $content = '';

    public string $font = '';

    public string $colorScheme = ItemColorScheme::WhiteOnBlack->value;

    public function mount(): void
    {
        $item = Item::query()->findOrFail($this->param('id'));

        $this->content = $item->content;
        $this->font = $item->font->value;
        $this->colorScheme = $item->color_scheme->value;

        ScreenOrientation::lockLandscape();
    }

    public function unmount(): void
    {
        ScreenOrientation::lockPortrait();

        parent::unmount();
    }

    public function closeItem(): void
    {
        $this->back();
    }

    public function render(): View
    {
        return view('native.item-show', [
            'textSizeClass' => $this->textSizeClass(),
            'colors' => ItemColorScheme::from($this->colorScheme),
        ]);
    }

    /**
     * Shorter text gets bigger type so it can be read from a distance. Width is
     * judged by the longest line and height by the number of lines, because
     * line breaks can make even short text too tall for a landscape phone.
     */
    private function textSizeClass(): string
    {
        $lines = preg_split('/\R/u', $this->content);
        $longestLineLength = max(array_map(mb_strlen(...), $lines));
        $lineCount = count($lines);

        return match (true) {
            $longestLineLength <= 11 && $lineCount <= 2 => 'text-8xl',
            $longestLineLength <= 15 && $lineCount <= 3 => 'text-7xl',
            $longestLineLength <= 19 && $lineCount <= 4 => 'text-6xl',
            $longestLineLength <= 25 && $lineCount <= 5 => 'text-5xl',
            $longestLineLength <= 32 && $lineCount <= 6 => 'text-4xl',
            $longestLineLength <= 48 && $lineCount <= 8 => 'text-3xl',
            $longestLineLength <= 120 && $lineCount <= 10 => 'text-2xl',
            $longestLineLength <= 250 && $lineCount <= 12 => 'text-xl',
            default => 'text-lg',
        };
    }
}
