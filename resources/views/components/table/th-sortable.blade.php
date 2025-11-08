<th scope="col" wire:click="doSort('{{ $name }}')" class="text-sm cursor-pointer font-semibold text-white">
    <div {{ $attributes->merge(['class' => 'inline-flex items-center ms-2']) }}>
        {{ $label }}
        <div class="ps-1">
            @if ($sortBy !== $name)
                <span class="iconify tabler--caret-up-down-filled "></span>
            @elseif ($sortDir == 'desc')
                <span class="iconify tabler--caret-down-filled "></span>
            @else
                <span class="iconify tabler--caret-up-filled "></span>
            @endif
        </div>
    </div>
</th>
