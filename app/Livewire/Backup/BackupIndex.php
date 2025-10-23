<?php

namespace App\Livewire\Backup;

use Livewire\Component;

class BackupIndex extends Component
{
    public function downloadSQL(){
        $this->dispatch('backup-sql',url: route('backup.export'));
    }

    public function render()
    {
        return view('livewire.backup.backup-index');
    }
}
