@props(['errorName'])

<div wire:key='error-bag-{!! md5($errorName) !!}-{{ time() }}' x-data="{
    errors: @js($errors->toArray()),
    errorKey: {!! $errorName !!},
    get message() {
        return this.errors?.[`${this.errorKey}`]?.[0] ?? null;
    },
}" {{ $attributes->merge(['class' => 'col-span-12']) }}
     x-show="message">
    <span class="text-sm text-red-500" x-text="message ?? ''"></span>
</div>

{{-- <div x-show="!!{!! $errorName !!}" {{ $attributes->merge(['class' => 'col-span-12']) }}>
    <span class="text-sm text-red-500" x-text="{!! $errorName !!} ?? ''"></span>
</div> --}}
