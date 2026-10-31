@use('App\Icons\Ios')
@use('App\Icons\Android')

<column fill class="safe-area bg-theme-background">
    <row
        ref="brand-header"
        class="mx-4 mt-2 mb-2 items-center gap-3 rounded-2xl bg-gradient-to-r from-indigo-700 to-violet-600 px-4 py-3 shadow-md"
    >
        <image ref="brand-logo" src="images/logo.png" class="h-[28] w-[40] object-contain" />
        <text font="accent" :max-lines="1" class="flex-1 text-xl text-white">{{ config('app.name') }}</text>
        <row class="items-center rounded-full bg-white/20 px-3 py-1">
            <text ref="item-count" :max-lines="1" class="text-sm text-white">
                {{ trans_choice('{0} No items|{1} :count item|[2,*] :count items', count($items)) }}
            </text>
        </row>
    </row>

    @if (empty($items))
        <column ref="empty-state" class="w-full flex-1 items-center justify-center gap-3 p-8">
            <column class="mb-3 h-[88] w-[88] items-center justify-center rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-500 shadow-lg">
                <icon :ios="Ios::Textformat" :android="Android::TextFields" :size="40" class="text-white" />
            </column>
            <text class="text-center text-2xl text-theme-on-background">Nothing to show yet</text>
            <text class="mb-4 text-center leading-relaxed text-theme-on-surface-variant">
                Add a name, email or phone number and show it across the room.
            </text>
            <button label="Add your first item" variant="primary" size="lg" :ios="Ios::Plus" :android="Android::Add" @press="addItem" />
        </column>
    @else
        <scroll-view class="w-full flex-1">
            <column class="w-full gap-2 px-4 pt-1 pb-[104]">
                @foreach ($items as $item)
                    <column
                        ref="item-{{ $item['id'] }}"
                        class="w-full gap-1 rounded-2xl border border-theme-outline bg-theme-surface px-3 py-2 shadow-sm"
                    >
                        <pressable
                            ref="show-{{ $item['id'] }}"
                            a11y-label="Show {{ $item['content'] }} full screen"
                            :press-opacity="0.6"
                            class="w-full"
                            @press="showItem({{ $item['id'] }})"
                        >
                            <text
                                ref="item-{{ $item['id'] }}-content"
                                font="{{ $item['font'] }}"
                                :max-lines="1"
                                class="text-lg text-theme-on-surface"
                            >{{ $item['content'] }}</text>
                        </pressable>

                        <row class="w-full items-center gap-1">
                            <text
                                ref="item-{{ $item['id'] }}-details"
                                :max-lines="1"
                                class="flex-1 text-xs text-theme-on-surface-variant"
                            >{{ $item['details'] }}</text>

                            <pressable
                                ref="edit-{{ $item['id'] }}"
                                a11y-label="Edit item"
                                :press-opacity="0.6"
                                class="h-[40] items-center justify-center rounded-xl px-2"
                                @press="editItem({{ $item['id'] }})"
                            >
                                <row class="items-center gap-1">
                                    <icon :ios="Ios::Pencil" :android="Android::Edit" :size="14" class="text-theme-on-surface-variant" />
                                    <text class="text-sm text-theme-on-surface-variant">Edit</text>
                                </row>
                            </pressable>

                            <pressable
                                ref="delete-{{ $item['id'] }}"
                                a11y-label="Delete item"
                                :press-opacity="0.6"
                                class="h-[40] items-center justify-center rounded-xl px-2"
                                @press="confirmDeletion({{ $item['id'] }})"
                            >
                                <row class="items-center gap-1">
                                    <icon :ios="Ios::Trash" :android="Android::Delete" :size="14" class="text-theme-destructive" />
                                    <text class="text-sm text-theme-destructive">Delete</text>
                                </row>
                            </pressable>
                        </row>
                    </column>
                @endforeach
            </column>
        </scroll-view>

        <pressable
            ref="add-item"
            a11y-label="Add item"
            :press-scale="0.92"
            class="absolute bottom-[24] right-[20] h-[60] w-[60] items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-violet-500 shadow-lg"
            @press="addItem"
        >
            <icon :ios="Ios::Plus" :android="Android::Add" :size="28" class="text-white" />
        </pressable>
    @endif
</column>
