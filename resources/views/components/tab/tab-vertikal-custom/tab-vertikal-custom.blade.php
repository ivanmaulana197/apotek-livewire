<div wire:key='tab-vertikal {{ time() }}' class="relative flex flex-wrap overflow-hidden">
    <div class="flex max-w-full sm:max-h-96 overflow-auto [&::-webkit-scrollbar]:h-0 [&::-webkit-scrollbar]:w-0 pb-2 pe-2 sm:pb-0 border-e-2 border-gray-300 dark:border-neutral-500">
        <nav class="flex sm:flex-col gap-y-2" aria-label="Tabs" role="tablist" aria-orientation="vertical">
            {{ $tab }}
        </nav>
    </div>
    <div class="ms-3 flex-1">
        {{ $tabItem }}
    </div>

</div>
