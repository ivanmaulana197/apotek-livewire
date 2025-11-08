<nav {{ $attributes->merge(['class' => 'relative z-0 flex border border-gray-200 rounded-xl overflow-hidden dark:border-neutral-700']) }} aria-label="Tabs"
     role="tablist" aria-orientation="horizontal">
    {{ $tab }}
</nav>

<div class="mt-2">
    {{ $tabItem }}
</div>
