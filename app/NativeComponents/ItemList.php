<?php

namespace App\NativeComponents;

use App\Enums\ItemKind;
use App\Models\Item;
use Illuminate\View\View;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\NativeComponent;
use Native\Mobile\Edge\Transition;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Dialog;

class ItemList extends NativeComponent
{
    private const DELETE_DIALOG_ID = 'delete-item';

    /**
     * @var list<array{id: int, content: string, font: string, details: string}>
     */
    public array $items = [];

    public ?int $itemIdPendingDeletion = null;

    public function mount(): void
    {
        $this->loadItems();
    }

    /**
     * Returning from the form means an item may have been added or edited.
     */
    public function onResume(): void
    {
        $this->loadItems();
    }

    public function addItem(): void
    {
        $this->navigate('/items/create');
    }

    public function showItem(int $itemId): void
    {
        $this->navigate("/items/{$itemId}/show")->transition(Transition::Fade);
    }

    public function editItem(int $itemId): void
    {
        $this->navigate("/items/{$itemId}/edit");
    }

    public function confirmDeletion(int $itemId): void
    {
        $this->itemIdPendingDeletion = $itemId;

        Dialog::alert('Delete item?', 'This cannot be undone.', [
            ['label' => 'Cancel', 'style' => 'cancel'],
            ['label' => 'Delete', 'style' => 'destructive'],
        ])->id(self::DELETE_DIALOG_ID);
    }

    #[On(ButtonPressed::class)]
    public function handleDeletionDialog(string $label, ?string $id = null): void
    {
        if ($id !== self::DELETE_DIALOG_ID) {
            return;
        }

        if ($label === 'Delete') {
            Item::query()->whereKey($this->itemIdPendingDeletion)->delete();
            $this->loadItems();
        }

        $this->itemIdPendingDeletion = null;
    }

    public function render(): View
    {
        return view('native.item-list');
    }

    private function loadItems(): void
    {
        $this->items = Item::query()
            ->latest('id')
            ->get(['id', 'content', 'font'])
            ->map(function (Item $item): array {
                $kind = ItemKind::detect($item->content);

                return [
                    'id' => $item->id,
                    'content' => $item->content,
                    'font' => $item->font->value,
                    'details' => "{$kind->label()} · {$item->font->label()}",
                ];
            })
            ->all();
    }
}
