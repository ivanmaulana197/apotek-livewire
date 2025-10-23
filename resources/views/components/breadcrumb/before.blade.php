@props(['name','href'])

<li class="flex items-center">
    <a class="text-sm font-medium text-primary hover:text-primaryemphasis" href="{{ $href }}" wire:navigate>
        {{ $name }}
    </a>
</li>
<li>
    <div class="h-1 w-1 rounded-full bg-bodytext mx-2.5 flex items-center mt-1"></div>
</li>