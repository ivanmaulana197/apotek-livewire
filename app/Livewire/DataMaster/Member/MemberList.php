<?php

namespace App\Livewire\DataMaster\Member;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\CustomDataTable;
use App\Models\DataMaster\Member;
use App\Models\DataMaster\Profesi;
use App\Models\DataMaster\Supplier;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class MemberList extends Component
{
    use WithPagination, CustomDataTable;

    public $filters = [
        'id_profesi' => '',
    ];

    public function confirmationDelete($id)
    {
        $this->dispatch('alert-confirm', id: $id,method: 'delete-member');
    }

    #[On('delete-member')]
    public function delete(Member $id): void
    {
        if (Auth::user()->hasRole('admin')) { //nanti update yang mempunyai permission
            $id->delete();
            $this->dispatch('alert', message: 'Berhasil Menghapus Supplier');
        }else{
            $this->dispatch('alert', message: 'Kamu Tidak Memiliki Akses', status: 'error');
        }
    }

    #[On('member-created')]
    public function render()
    {
        $data = Member::with(['profesi'])
            ->select('member.*')
            ->join('profesi', 'member.id_profesi', '=', 'profesi.id')
            ->search($this->search, ['member.nama', 'member.alamat', 'member.no_hp'], ['profesi' => ['profesi.nama']])
            ->filter($this->filters)
            ->orderBy($this->sortBy, $this->sortDir)
            ->groupBy('member.id')
            ->paginate($this->perPage);

        return view('livewire.data-master.member.member-list',['members'=>$data]);
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function profesies()
    {
        return Profesi::select('id', 'nama')->get();
    }
}
