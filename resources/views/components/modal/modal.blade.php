@props(['body', 'name', 'size' => '2xl', 'clickAway' => true])

<div x-data="{ {{ $name }}Modal: false }" id="{{ $name }}-modal"
     @toggle-modal-{{ $name }}.window="{{ $name }}Modal = $event.detail.{{ $name }}Modal" x-cloak>
    <div x-show="{{ $name }}Modal" x-transition.opacity class=" fixed inset-0 z-[999] overflow-y-auto bg-black/50 p-3 sm:p-1"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-full">
            <div @if ($clickAway) @click.away="if (!$event.target.closest('.daterangepicker')) {
                    $dispatch('close-modal-{{ $name }}'); {{ $name }}Modal = false
                }" @endif
                 class="bg-white rounded-xl shadow-xl  max-w-{{ $size }} w-full m-auto dark:border dark:border-darkborder dark:bg-dark">


                {{-- <div class="flex justify-between items-center border-b px-4 py-2 flex-none">
                    <h2 class="text-lg font-semibold">Tambah</h2>
                    <button @click="{{ $name }}Modal = false" class="text-gray-500 hover:text-gray-700">
                        ✕
                    </button>
                </div> --}}


                <div class="p-4">
                    {{ $slot }}
                </div>


                {{-- <div class="flex justify-end gap-2 border-t px-4 py-2 flex-none">
                    <button type="button" @click="{{ $name }}Modal = false" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="button"  class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Submit
                    </button>
                </div> --}}
            </div>
        </div>
    </div>
</div>
