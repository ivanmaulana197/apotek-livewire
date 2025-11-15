<a {{ $attributes->merge([
    'class' => 'flex items-center gap-x-2.5 py-2 px-3 pe-8 text-sm hover:bg-gray-400 dark:hover:bg-neutral-700',
]) }}>
    {{ $slot }}
</a>
