<div wire:key='tab-vertikal {{ time() }}' class="flex flex-wrap">
    <div class="border-e border-gray-200 dark:border-neutral-700">
        <nav class="flex flex-col  gap-y-2" aria-label="Tabs" role="tablist" aria-orientation="vertical">
            {{ $tab }}
        </nav>
    </div>
    <div class="ms-3 flex-1">
        {{ $tabItem }}
    </div>

</div>
