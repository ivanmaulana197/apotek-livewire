@props(['number', 'active' => false])

@php
    $active = $active ? '' : 'hidden';
@endphp
<div {{ $attributes->merge(['class' => "$active"]) }} id="vertical-tab-with-border-{{ $number }}" role="tabpanel"
     aria-labelledby="vertical-tab-with-border-item-{{ $number }}">
    {{ $slot }}
</div>
