@props(['perPage' => '5', 'nama_table' => '', 'search'=>true])
<div class="">
    <div class="flex flex-row flex-wrap items-center gap-3">
        <div wire:ignore class="{{ !trim($slot) ? 'flex flex-row flex-wrap items-center' : '' }}">
            @if (!trim($slot))
                <label class="me-2">Show :</label>
            @endif
            <x-datatable.per-page :nama_table="$nama_table" :label="trim($slot) ? true : false" :perPage="$perPage" />
        </div>
        {{ $slot }}
        @if (!trim($slot))
            <div class="flex-auto">
                @if ($search)
                    <x-datatable.search placeholder_search="Search ..." class="font-medium" />
                @endif
            </div>
        @endif
    </div>
    @if (trim($slot))
        <div class="my-3">
            @if ($search)
                <x-datatable.search placeholder_search="Search ...">
                    <span class="text-md ms-1"> Search</span>
                </x-datatable.search>
            @endif
        </div>
    @endif
</div>
