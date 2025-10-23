<div class="card mb-2">
    <div class="card-body">
        @isset($title)
            <h5 class="card-title text-md">{{ $title }}</h5>
        @endisset
        <div class="flex items-center justify-center gap-2">
            <x-table.loading-spin />
        </div>
    </div>
    <!-- Loading spinner... -->
</div>
