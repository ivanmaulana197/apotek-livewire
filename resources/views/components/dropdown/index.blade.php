@props(['text'])

<div class="hs-dropdown inline-flex" wire:ignore>
    <button  type="button" class="hs-dropdown-toggle btn btn-sm gap-x-2  shadow-2xs text-gray-800 bg-slate-200 border border-slate-800  disabled:opacity-50 disabled:pointer-events-none dark:bg-primary dark:border-neutral-700 dark:text-white dark:hover:bg-primaryemphasis dark:focus:bg-neutral-700" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
        {{ $text }}
        <svg class="hs-dropdown-open:rotate-180 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m6 9 6 6 6-6" />
        </svg>
    </button>

    <div class="hs-dropdown-menu z-10 transition-[opacity,margin] overflow-hidden duration hs-dropdown-open:opacity-100 opacity-0 hidden  bg-slate-200 border border-slate-800 shadow-md rounded-lg mt-1 divide-y divide-slate-600 dark:bg-neutral-800  dark:border-neutral-700 dark:divide-neutral-700 after:h-4 after:absolute after:-bottom-4 after:start-0 after:w-full before:h-4 before:absolute before:-top-4 before:start-0 before:w-full" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-hover-event">
        {{ $slot }}
    </div>
</div>


