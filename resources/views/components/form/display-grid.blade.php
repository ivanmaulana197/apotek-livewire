@props(['label', 'value'])

<div
     class="flex justify-between py-1 px-3 rounded-lg border border-bordergray bg-gray-100 text-gray-500 dark:text-gray-400 dark:bg-gray-700 dark:border-gray-700">
    <span>{{ $label }} :</span>
    <span>{{ $value }}</span>
</div>
