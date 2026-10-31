<?php

namespace App\NativeComponents;

use App\Enums\ItemColorScheme;
use App\Enums\ItemFont;
use App\Models\Item;
use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class ItemForm extends NativeComponent
{
    public ?int $itemId = null;

    public string $content = '';

    public string $fontLabel = 'Standard';

    public string $selectedColorScheme = ItemColorScheme::WhiteOnBlack->value;

    public bool $contentIsMissing = false;

    public function mount(): void
    {
        $itemId = $this->param('id');

        if ($itemId === null) {
            return;
        }

        $item = Item::query()->findOrFail($itemId);

        $this->itemId = $item->id;
        $this->content = $item->content;
        $this->fontLabel = $item->font->label();
        $this->selectedColorScheme = $item->color_scheme->value;
    }

    public function navTitle(): string
    {
        return $this->param('id') === null ? 'New item' : 'Edit item';
    }

    public function updatedContent(): void
    {
        $this->contentIsMissing = false;
    }

    public function selectFont(int $fontIndex): void
    {
        $this->fontLabel = ItemFont::cases()[$fontIndex]->label();
    }

    public function selectColorScheme(int $colorSchemeIndex): void
    {
        $this->selectedColorScheme = ItemColorScheme::cases()[$colorSchemeIndex]->value;
    }

    public function save(): void
    {
        $content = trim($this->content);

        if ($content === '') {
            $this->contentIsMissing = true;

            return;
        }

        Item::query()->updateOrCreate(['id' => $this->itemId], [
            'content' => $content,
            'font' => ItemFont::fromLabel($this->fontLabel),
            'color_scheme' => ItemColorScheme::from($this->selectedColorScheme),
        ]);

        $this->back();
    }

    public function render(): View
    {
        return view('native.item-form', [
            'fonts' => ItemFont::cases(),
            'previewFont' => ItemFont::fromLabel($this->fontLabel)->value,
            'colorSchemes' => ItemColorScheme::cases(),
            'previewColors' => ItemColorScheme::from($this->selectedColorScheme),
        ]);
    }
}
