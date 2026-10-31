<column
    ref="show-screen"
    fill
    center
    a11y-label="{{ $content }}"
    a11y-hint="Double-tap to close"
    class="safe-area p-6 {{ $colors->backgroundClass() }}"
    @doubleTap="closeItem"
>
    <text
        ref="show-text"
        font="{{ $font }}"
        class="w-full whitespace-pre-line text-center {{ $colors->textClass() }} {{ $textSizeClass }}"
    >{{ $content }}</text>
</column>
