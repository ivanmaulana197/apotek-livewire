<div x-data x-on:backup-sql.window="window.location.href = $event.detail.url">
    <button type="button" wire:click="downloadSQL" class="btn btn-sm btn-primary">Download SQL</button>
</div>
