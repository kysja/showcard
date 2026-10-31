<?php

use App\Enums\ItemColorScheme;
use App\Enums\ItemFont;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Testing\Native;

uses(RefreshDatabase::class);

it('shows the empty state and opens the form when there are no items', function () {
    Native::visit('/')
        ->assertSee('Nothing to show yet')
        ->assertMissingElement('pressable', fn (array $node): bool => ($node['ref'] ?? null) === 'add-item')
        ->tap('Add your first item')
        ->assertNavigatedTo('/items/create');
});

it('shows the brand header with the logo and item count', function () {
    expect(public_path('images/logo.png'))->toBeFile()
        ->and(public_path('icon.png'))->toBeFile();

    $screen = Native::visit('/')
        ->assertMissingElement('top_bar')
        ->assertSee(config('app.name'))
        ->assertElement('image', fn (array $node): bool => ($node['ref'] ?? null) === 'brand-logo'
            && ($node['props']['src'] ?? null) === 'images/logo.png');

    $screen->assertSee('No items');

    Item::factory()->create();
    $screen->call('onResume')->assertSee('1 item');

    Item::factory()->create();
    $screen->call('onResume')->assertSee('2 items');
});

it('keeps the name and item count on one header line', function () {
    Native::visit('/')
        ->assertElement('row', fn (array $node): bool => ($node['ref'] ?? null) === 'brand-header'
            && count(array_filter(
                $node['children'] ?? [],
                fn (array $child): bool => in_array($child['type'] ?? null, ['text', 'row'], true),
            )) === 2);
});

it('describes each item by its detected kind and font', function (string $content, string $expectedDetails) {
    Item::factory()->create(['content' => $content, 'font' => ItemFont::Mono]);

    Native::visit('/')->assertSee($expectedDetails);
})->with([
    'email' => ['alex@example.com', 'Email · Mono'],
    'phone' => ['+1 (555) 123-4567', 'Phone · Mono'],
    'link' => ['https://example.com/card', 'Link · Mono'],
    'name' => ['Alex Example', 'Text · Mono'],
    'short number' => ['12345', 'Text · Mono'],
]);

it('renders gradients on the header and add button', function () {
    Item::factory()->create();
    $hasGradient = fn (array $node): bool => ! empty($node['props']['gradient_stops']);

    Native::visit('/')
        ->assertElement('row', fn (array $node): bool => ($node['ref'] ?? null) === 'brand-header' && $hasGradient($node))
        ->assertElement('pressable', fn (array $node): bool => ($node['ref'] ?? null) === 'add-item' && $hasGradient($node));
});

it('opens the show screen when the card text is tapped', function () {
    $item = Item::factory()->create(['content' => 'Press me']);

    Native::visit('/')
        ->assertMissingElement('text', fn (array $node): bool => trim($node['props']['text'] ?? '') === 'Show')
        ->tap('Press me')
        ->assertNavigatedTo("/items/{$item->id}/show");
});

it('keeps the back button on the form header', function () {
    Native::visit('/items/create')
        ->assertElement('top_bar', fn (array $node): bool => ($node['props']['title'] ?? null) === 'New item');
});

it('uses a fully opaque square app icon', function () {
    [$width, $height] = getimagesize(public_path('icon.png'));
    $icon = imagecreatefrompng(public_path('icon.png'));

    expect($width)->toBe(1024)
        ->and($height)->toBe(1024)
        ->and(imagecolorat($icon, 0, 0) >> 24)->toBe(0);
});

it('lists items newest first in their own font', function () {
    $older = Item::factory()->create(['content' => 'older@example.com']);
    $newer = Item::factory()->create(['content' => '555 1234', 'font' => ItemFont::Mono]);

    $screen = Native::visit('/')
        ->assertSee('older@example.com')
        ->assertSee('555 1234')
        ->assertElement('text', fn (array $node): bool => ($node['ref'] ?? null) === "item-{$newer->id}-content"
            && ($node['props']['font_name'] ?? null) === 'roboto-mono');

    expect($screen->instance()->items[0]['id'])->toBe($newer->id)
        ->and($screen->instance()->items[1]['id'])->toBe($older->id);
});

it('opens the form from the add button', function () {
    Item::factory()->create();

    Native::visit('/')
        ->tap('add-item')
        ->assertNavigatedTo('/items/create');
});

it('opens the show and edit screens from a row', function (string $buttonPrefix, string $expectedPath) {
    $item = Item::factory()->create();

    Native::visit('/')
        ->tap("{$buttonPrefix}-{$item->id}")
        ->assertNavigatedTo("/items/{$item->id}/{$expectedPath}");
})->with([
    'show' => ['show', 'show'],
    'edit' => ['edit', 'edit'],
]);

it('picks up changes when returning to the list', function () {
    $screen = Native::visit('/')->assertSee('Nothing to show yet');

    Item::factory()->create(['content' => 'added elsewhere']);

    $screen->call('onResume')->assertSee('added elsewhere');
});

it('asks before deleting and only deletes on confirmation', function () {
    $bridge = Native::fakeBridge()->respondTo('Dialog.Alert', ['success' => true]);
    $item = Item::factory()->create(['content' => 'keep or delete']);

    $screen = Native::visit('/')->tap("delete-{$item->id}");

    $bridge->assertCalled('Dialog.Alert', fn (array $params): bool => $params['id'] === 'delete-item');
    expect(Item::query()->count())->toBe(1);

    $screen->emitNative(ButtonPressed::class, ['index' => 0, 'label' => 'Cancel', 'id' => 'delete-item']);
    expect(Item::query()->count())->toBe(1);

    $screen->tap("delete-{$item->id}")
        ->emitNative(ButtonPressed::class, ['index' => 1, 'label' => 'Delete', 'id' => 'delete-item'])
        ->assertDontSee('keep or delete')
        ->assertSee('Nothing to show yet');

    expect(Item::query()->count())->toBe(0);
});

it('ignores dialog presses from other dialogs', function () {
    Native::fakeBridge()->respondTo('Dialog.Alert', ['success' => true]);
    $item = Item::factory()->create();

    Native::visit('/')
        ->tap("delete-{$item->id}")
        ->emitNative(ButtonPressed::class, ['index' => 1, 'label' => 'Delete', 'id' => 'other-dialog']);

    expect(Item::query()->count())->toBe(1);
});

it('uses the standard font and white on black for new items by default', function () {
    Native::visit('/items/create')
        ->set('content', 'hello@example.com')
        ->tap('Save')
        ->assertWentBack();

    $item = Item::query()->sole();

    expect($item->font)->toBe(ItemFont::Standard)
        ->and($item->color_scheme)->toBe(ItemColorScheme::WhiteOnBlack);
});

it('creates an item with the chosen font and colors', function () {
    Native::visit('/items/create')
        ->set('content', '  hello@example.com  ')
        ->tap('font-Mono')
        ->assertSet('fontLabel', 'Mono')
        ->assertElement('text', fn (array $node): bool => ($node['ref'] ?? null) === 'preview-text'
            && ($node['props']['font_name'] ?? null) === 'roboto-mono')
        ->tap('color-black-on-white')
        ->assertSet('selectedColorScheme', 'black-on-white')
        ->tap('Save')
        ->assertWentBack();

    $item = Item::query()->sole();

    expect($item->content)->toBe('hello@example.com')
        ->and($item->font)->toBe(ItemFont::Mono)
        ->and($item->color_scheme)->toBe(ItemColorScheme::BlackOnWhite);
});

it('offers only the standard and mono fonts', function () {
    expect(ItemFont::cases())->toBe([ItemFont::Standard, ItemFont::Mono]);

    $screen = Native::visit('/items/create');

    foreach (ItemFont::cases() as $font) {
        $screen->assertElement('pressable', fn (array $node): bool => ($node['ref'] ?? null) === "font-{$font->label()}");
    }
});

it('offers every color scheme on the form', function () {
    $screen = Native::visit('/items/create')
        ->assertSee('White on Black')
        ->assertSee('Black on White');

    foreach (ItemColorScheme::cases() as $colorScheme) {
        $screen->assertElement('pressable', fn (array $node): bool => ($node['ref'] ?? null) === "color-{$colorScheme->value}");
    }
});

it('moves items with a removed font to standard and defaults their colors', function () {
    $migration = require database_path('migrations/2026_10_31_142224_add_colors_to_items_table.php');
    $migration->down();

    DB::table('items')->insert([
        ['content' => 'removed font', 'font' => 'lobster'],
        ['content' => 'kept font', 'font' => 'roboto-mono'],
    ]);

    $migration->up();

    expect(Item::query()->where('content', 'removed font')->sole()->font)->toBe(ItemFont::Standard)
        ->and(Item::query()->where('content', 'kept font')->sole()->font)->toBe(ItemFont::Mono)
        ->and(Item::query()->where('content', 'kept font')->sole()->color_scheme)->toBe(ItemColorScheme::WhiteOnBlack);
});

it('rejects blank text and clears the error on typing', function () {
    Native::visit('/items/create')
        ->set('content', '   ')
        ->tap('Save')
        ->assertSet('contentIsMissing', true)
        ->assertSee('Enter some text')
        ->set('content', 'x')
        ->assertSet('contentIsMissing', false);

    expect(Item::query()->count())->toBe(0);
});

it('edits an existing item without creating a new one', function () {
    $item = Item::factory()->create([
        'content' => 'old text',
        'font' => ItemFont::Standard,
        'color_scheme' => ItemColorScheme::BlackOnWhite,
    ]);

    Native::visit("/items/{$item->id}/edit")
        ->assertSet('content', 'old text')
        ->assertSet('fontLabel', 'Standard')
        ->assertSet('selectedColorScheme', 'black-on-white')
        ->set('content', 'new text')
        ->tap('font-Mono')
        ->tap('color-white-on-black')
        ->tap('Save')
        ->assertWentBack();

    $item = Item::query()->sole();

    expect($item->content)->toBe('new text')
        ->and($item->font)->toBe(ItemFont::Mono)
        ->and($item->color_scheme)->toBe(ItemColorScheme::WhiteOnBlack);
});

it('shows an item in its chosen colors', function (ItemColorScheme $colorScheme, string $expectedTextColor) {
    $item = Item::factory()->create(['color_scheme' => $colorScheme]);

    Native::visit("/items/{$item->id}/show")
        ->assertElement('text', fn (array $node): bool => ($node['ref'] ?? null) === 'show-text'
            && strtoupper((string) ($node['props']['color'] ?? '')) === $expectedTextColor);
})->with([
    'white on black' => [ItemColorScheme::WhiteOnBlack, '#FFFFFF'],
    'black on white' => [ItemColorScheme::BlackOnWhite, '#000000'],
]);

it('shows an item with no nav bar and only closes on double-tap', function () {
    $item = Item::factory()->create(['content' => 'Big text', 'font' => ItemFont::Mono]);

    Native::visit("/items/{$item->id}/show")
        ->assertSee('Big text')
        ->assertMissingElement('text', fn (array $node): bool => ($node['ref'] ?? null) !== 'show-text')
        ->assertMissingElement('top_bar')
        ->assertElement('text', fn (array $node): bool => ($node['ref'] ?? null) === 'show-text'
            && ($node['props']['font_name'] ?? null) === 'roboto-mono')
        ->assertElement('column', fn (array $node): bool => ($node['ref'] ?? null) === 'show-screen'
            && ($node['props']['on_double_tap'] ?? 0) > 0
            && ! isset($node['props']['on_press']))
        ->call('closeItem')
        ->assertWentBack();
});

it('keeps line breaks when showing an item', function (string $savedContent) {
    $item = Item::factory()->create(['content' => $savedContent]);

    Native::visit("/items/{$item->id}/show")
        ->assertElement('text', fn (array $node): bool => ($node['ref'] ?? null) === 'show-text'
            && ($node['props']['text'] ?? null) === "First line\nSecond line");
})->with([
    'unix line break' => ["First line\nSecond line"],
    'windows line break' => ["First line\r\nSecond line"],
]);

it('rotates to landscape while showing and back to portrait on leaving', function () {
    $bridge = Native::fakeBridge()
        ->respondTo('ScreenOrientation.LockLandscape', ['success' => true])
        ->respondTo('ScreenOrientation.LockPortrait', ['success' => true]);
    $item = Item::factory()->create();

    $screen = Native::visit("/items/{$item->id}/show");

    $bridge->assertCalledTimes('ScreenOrientation.LockLandscape', 1)
        ->assertCalledTimes('ScreenOrientation.LockPortrait', 0);

    $screen->instance()->unmount();

    $bridge->assertCalledTimes('ScreenOrientation.LockPortrait', 1);
});

it('scales the show text to its length', function (int $length, float $expectedFontSize) {
    $item = Item::factory()->create(['content' => str_repeat('a', $length)]);

    Native::visit("/items/{$item->id}/show")
        ->assertElement('text', fn (array $node): bool => ($node['ref'] ?? null) === 'show-text'
            && ($node['props']['font_size'] ?? null) === $expectedFontSize);
})->with([
    'name' => [11, 96.0],
    'phone' => [15, 72.0],
    'long phone' => [19, 60.0],
    'email' => [25, 48.0],
    'long email' => [32, 36.0],
    'short sentence' => [48, 30.0],
    'sentence' => [120, 24.0],
    'paragraph' => [250, 20.0],
    'maximum' => [500, 18.0],
]);

it('scales multi-line show text to its longest line and line count', function (string $content, float $expectedFontSize) {
    $item = Item::factory()->create(['content' => $content]);

    Native::visit("/items/{$item->id}/show")
        ->assertElement('text', fn (array $node): bool => ($node['ref'] ?? null) === 'show-text'
            && ($node['props']['font_size'] ?? null) === $expectedFontSize);
})->with([
    'two short lines' => ["Alex\nExample", 96.0],
    'three short lines' => ["a\nb\nc", 72.0],
    'longest line decides width' => ["Alex\nalex@example.com", 60.0],
    'many short lines' => [implode("\n", array_fill(0, 12, 'a')), 20.0],
    'too many lines' => [implode("\n", array_fill(0, 13, 'a')), 18.0],
]);

it('keeps line breaks in the form preview', function () {
    $item = Item::factory()->create(['content' => "First line\nSecond line"]);

    Native::visit("/items/{$item->id}/edit")
        ->assertElement('text', fn (array $node): bool => ($node['ref'] ?? null) === 'preview-text'
            && ($node['props']['text'] ?? null) === "First line\nSecond line");
});

it('maps every custom font option to a bundled font file', function () {
    foreach (ItemFont::cases() as $font) {
        if ($font === ItemFont::Standard) {
            continue;
        }

        expect(resource_path('fonts/'.config("native-ui.fonts.{$font->value}").'.ttf'))->toBeFile();
    }
});

it('keeps the android signing credentials out of the app bundle', function (string $key) {
    expect(Str::is(config('nativephp.cleanup_env_keys'), $key))->toBeTrue();
})->with([
    'ANDROID_KEYSTORE_FILE',
    'ANDROID_KEYSTORE_PASSWORD',
    'ANDROID_KEY_ALIAS',
    'ANDROID_KEY_PASSWORD',
]);

it('has accessible screens', function () {
    $item = Item::factory()->create();

    Native::visit('/')->assertAccessible();
    Native::visit('/items/create')->assertAccessible();
    Native::visit("/items/{$item->id}/edit")->assertAccessible();
    Native::visit("/items/{$item->id}/show")->assertAccessible();
});
