<th scope="col"
    wire:click="doSort('{{ $name }}')"
    class="text-sm cursor-pointer font-semibold text-white"
    >
    <div {{ $attributes->merge(['class'=>"inline-flex items-center ms-2"])}}>
        {{ $label }}
        <div class="ps-1">
            @if ($sortBy !== $name)
                <i class="ti ti-caret-up-down-filled"></i>
            @elseif ($sortDir == 'desc')
                <i class="ti ti-caret-down-filled"></i>
            @else
                <i class="ti ti-caret-up-filled"></i>
            @endif
        </div>
    </div>
</th>
