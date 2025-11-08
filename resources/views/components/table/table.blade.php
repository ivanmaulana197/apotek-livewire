@props(['nama','height' => null])

@php
    if(!$height){
        $height = 'max-h-[400px]';
    }
@endphp

<div class="card shadow-none">
    <div class="flex flex-col {{ $height }} ">
        <div class="flex-grow overflow-auto rounded-xl border border-gray-400 dark:border-darkborder">
            <table class="min-w-full h-full text-sm  divide-y divide-gray-400 dark:divide-darkborder" id="table-{{ $nama }}">
                {{ $slot }}
            </table>
        </div>

    </div>
</div>
