@use('App\Icons\Ios')
@use('App\Icons\Android')

<scroll-view class="w-full h-full bg-theme-background">
    <column class="w-full gap-5 p-4">
        <column
            ref="preview"
            class="w-full min-h-[132] items-center justify-center gap-3 rounded-3xl border border-theme-outline p-5 shadow-md {{ $previewColors->backgroundClass() }}"
        >
            <text class="text-xs uppercase tracking-widest {{ $previewColors->mutedTextClass() }}">Preview</text>
            <text
                ref="preview-text"
                font="{{ $previewFont }}"
                :max-lines="4"
                class="w-full whitespace-pre-line text-center text-2xl {{ $previewColors->textClass() }}"
            >{{ $content === '' ? 'Your text here' : $content }}</text>
        </column>

        <outlined-text-input
            label="Text"
            placeholder="Name, email, phone..."
            native:model.debounce.150ms="content"
            multiline
            :min-lines="2"
            :max-lines="6"
            :max-length="500"
            :is-error="$contentIsMissing"
            supporting="{{ $contentIsMissing ? 'Enter some text' : '' }}"
        />

        <column class="w-full gap-2">
            <text class="text-xs uppercase tracking-widest text-theme-on-surface-variant">Font</text>

            @foreach (array_chunk($fonts, 2, preserve_keys: true) as $fontRow)
                <row class="w-full gap-2">
                    @foreach ($fontRow as $fontIndex => $font)
                        @php($isSelected = $font->label() === $fontLabel)
                        <pressable
                            ref="font-{{ $font->label() }}"
                            a11y-label="{{ $font->label() }} font{{ $isSelected ? ', selected' : '' }}"
                            :press-opacity="0.7"
                            class="h-[56] flex-1 items-center justify-center rounded-2xl border-2 bg-theme-surface {{ $isSelected ? 'border-theme-primary' : 'border-theme-outline' }}"
                            @press="selectFont({{ $fontIndex }})"
                        >
                            <row class="items-center gap-2">
                                <text font="{{ $font->value }}" class="text-lg text-theme-on-surface">{{ $font->label() }}</text>
                                @if ($isSelected)
                                    <icon :ios="Ios::Checkmark" :android="Android::Check" :size="18" class="text-theme-primary" />
                                @endif
                            </row>
                        </pressable>
                    @endforeach
                </row>
            @endforeach
        </column>

        <column class="w-full gap-2">
            <text class="text-xs uppercase tracking-widest text-theme-on-surface-variant">Color</text>

            <row class="w-full gap-2">
                @foreach ($colorSchemes as $colorSchemeIndex => $colorScheme)
                    @php($isSelected = $colorScheme->value === $selectedColorScheme)
                    <pressable
                        ref="color-{{ $colorScheme->value }}"
                        a11y-label="{{ $colorScheme->label() }}{{ $isSelected ? ', selected' : '' }}"
                        :press-opacity="0.7"
                        class="h-[56] flex-1 items-center justify-center rounded-2xl border-2 {{ $colorScheme->backgroundClass() }} {{ $isSelected ? 'border-theme-primary' : 'border-theme-outline' }}"
                        @press="selectColorScheme({{ $colorSchemeIndex }})"
                    >
                        <row class="items-center gap-2">
                            <text :max-lines="1" class="text-base {{ $colorScheme->textClass() }}">{{ $colorScheme->label() }}</text>
                            @if ($isSelected)
                                <icon :ios="Ios::Checkmark" :android="Android::Check" :size="18" class="{{ $colorScheme->textClass() }}" />
                            @endif
                        </row>
                    </pressable>
                @endforeach
            </row>
        </column>

        <button label="Save" variant="primary" size="lg" class="w-full" @press="save" />
    </column>
</scroll-view>
