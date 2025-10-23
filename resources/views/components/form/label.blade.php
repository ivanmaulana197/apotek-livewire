@props(['label','grid'=>false])
<div class="col-span-12 label {{ $grid ? 'sm:col-span-2 md:col-span-4':'' }}">
    <span {{ $attributes->merge(['class' => "label-text text-sm font-semibold flex items-center h-full text-gray-800 dark:text-neutral-200"]) }}>
        {{ $label }}
    </span>
    {{-- <span class="label-text-alt">Top Right label</span> --}}
</div>
