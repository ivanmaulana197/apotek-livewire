@props(['label-dir' => 'left', 'error-name' => null])
<div class="">
    <label class="flex items-center cursor-pointer label ">
        @if ($labelDir == 'left')
            <span class="label-text ms-2">{{ $slot }}</span>
        @endif
        <input type="checkbox" {{ $attributes->merge([]) }}
               class="w-4 h-4 bg-transparent rounded border-bordergray text-primary dark:border-darkborder dark:checked:bg-primary dark:checked:border-primary focus:ring-0 focus:ring-offset-0"
               id="hs-checked-checkbox" checked>
        @if ($labelDir == 'right')
            <span class="label-text ms-2">{{ $slot }}</span>
        @endif
    </label>
    @if ($errorName)
        <x-error-bag errorName="`{!! $errorName !!}`" />
    @endif
</div>
