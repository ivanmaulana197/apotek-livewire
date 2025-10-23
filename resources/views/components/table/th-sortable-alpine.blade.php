@props(['name','label'])
<th scope="col" @click="sort('{{ $name }}')" class="text-sm cursor-pointer font-semibold text-white">
    <div {{ $attributes->merge(['class' => 'inline-flex items-center ms-2']) }}>
        {{ $label }}
        <div class="ps-1">
            <template x-if="sortBy === '{{ $name }}'">
                <span x-text="sortDir === 'asc' ? '▲' : '▼'"></span>
            </template>
            <template x-if="sortBy !== '{{ $name }}'">
                <i class="ti ti-caret-up-down-filled"></i>
            </template>
        </div>
    </div>
</th>
