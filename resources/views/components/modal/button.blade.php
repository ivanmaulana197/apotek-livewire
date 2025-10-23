@props(['name','label'])

{{-- <div x-data="{ {{ $name }}Modal: false }">
    <button type="button" @click="{{$name}}Modal = !{{ $name }}Modal"
        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700">
        Open modal
    </button>
</div> --}}
<div x-data class="w-full h-full">
    <button type="button" @click="$dispatch('toggle-modal-{{ $name }}', { {{ $name }}Modal: true })"
    class="btn btn-sm btn-primary w-full h-full text-lg">
        {{ $label }}
    </button>
</div>