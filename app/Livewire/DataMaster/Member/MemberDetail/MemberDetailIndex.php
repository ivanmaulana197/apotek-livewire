<?php
namespace App\Livewire\DataMaster\Member\MemberDetail;

use App\Models\DataMaster\Member;
use App\Models\DataMaster\Profesi;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MemberDetailIndex extends Component
{
    public $isEdit;
    public $member;
    public $infoTransaksi;
    public $temp, $filter_date;

    public function mount($id_member)
    {
        $this->isEdit      = false;
        $this->member      = Member::with('profesi:id,nama')->find($id_member)->toArray();
        $this->filter_date = [
            'start' => now()->startOfMonth()->format('Y-m-d'),
            'end'   => now()->endOfMonth()->format('Y-m-d'),
        ];

        // $this->dispatch('selected-profesi', selected: $this->member['id_profesi']);
        $this->temp = $this->member;

    }

    public function updatedFilterDate($value)
    {
        if (! $value) {
            $this->filter_date = [
                'start' => now()->format('Y-m-d'),
                'end'   => now()->format('Y-m-d'),
            ];
        }
        $this->dispatch('update_value_filter_date', value: $this->filter_date);

    }

    public function edit()
    {
        $this->isEdit = true;
    }

    public function cancel()
    {
        $this->isEdit = false;
        $this->member = $this->temp;
    }

    public function updateMember()
    {
        // dd($this->member);
        $data = $this->validate([
            'member.nama'       => 'required',
            'member.id_profesi' => 'required|exists:profesi,id',
            'member.no_hp'      => 'nullable',
            'member.alamat'     => 'nullable',
        ], ['required' => 'Kolom Wajib Diisi']);
        $data         = array_map(fn($value) => $value === '' ? null : $value, $data['member']);
        $data['nama'] = strtoupper($data['nama']);

        Member::find($this->member['id'])->update($data);

        $this->isEdit = false;
        $this->member = Member::with('profesi:id,nama')->find($this->member['id'])->toArray();
        $this->temp   = $this->member;
    }

    public function render()
    {
        return view('livewire.data-master.member.member-detail.member-detail-index');
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function profesies()
    {
        return Profesi::get(['id', 'nama']);
    }
}
