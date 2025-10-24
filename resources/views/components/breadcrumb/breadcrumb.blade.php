@props(['name', 'before' => '', 'subtitle', 'button' => null])

<div class="card mb-3 shadow-none">
    <div class="card-body py-3 px-8">
        <div class="sm:flex items-center justify-between ">
            @isset($subtitle)
                <div class="">
                    <h4 class="font-bold text-xl text-dark dark:text-white">{{ $button }} {{ $name }}</h4>
                    <p class="text-sm font-semibold text-gray-500">{{ $subtitle }}</p>
                </div>
            @else
                <div class="flex items-center gap-3">
                    {{ $button }}
                    <h4 class="font-semibold text-xl text-dark dark:text-white"> {{ $name }}</h4>
                </div>
            @endisset
            <ol class="flex items-center " aria-label="Breadcrumb">
                {{ $before }}
                <li class="flex items-center text-sm  font-bold " aria-current="page">
                    {{ $name }}
                </li>
            </ol>
        </div>
    </div>
</div>
