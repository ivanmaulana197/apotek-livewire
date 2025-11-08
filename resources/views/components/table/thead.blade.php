@props(['color' => 'bg-primary dark:bg-darkprimary', 'textColor' => 'text-white'])


<thead
{{ $attributes->merge(['class'=>"sticky top-0 rounded-xl font-semibold $color z-[1] $textColor"]) }}>
    <x-table.tr>
        {{ $slot }}
    </x-table.tr>
</thead>
