<?php
namespace App\Livewire\DataMaster\Member;

use App\Models\DataMaster\Member;
use App\Models\DataMaster\Profesi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class MemberForm extends Component
{
    public $isEdit = false;
    public Member $member;
    public $nama, $id_profesi, $no_hp, $alamat;
    public $isModal = false;

    protected function rules()
    {
        return [
            'nama'       => 'required',
            'id_profesi' => 'required|exists:profesi,id',
            'no_hp'      => 'nullable',
            'alamat'     => 'nullable',
        ];
    }

    public function save()
    {
        if (Auth::user()->can('member.create')) {
            $this->nama          = strtoupper($this->nama);
            $validate            = $this->validate();
            $validate['id_user'] = Auth::user()->id;
            $validate            = collect($validate)->transform(function ($val) {
                return $val === '' ? null : $val;
            }) // buang semua yang null
                ->toArray();
            $member = Member::create($validate);
            if ($this->isModal) {
                $this->dispatch('toggle-modal-member', memberModal: false);
            }
            $this->dispatch('alert', status: 'success', message: 'Berhasil Menambahkan Member');
            $this->resetForm();
            $this->dispatch('member-created', member: $member);
        } else {
            $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
    }

    public function update()
    {
        if (Auth::user()->can('member.edit')) {
            $this->nama          = strtoupper($this->nama);
            $validate            = $this->validate();
            $validate['id_user'] = Auth::user()->id;
            $member              = tap($this->member)->update($validate);

            $this->dispatch('alert', status: 'info', message: 'Berhasil Mengupdate Member');
            $this->resetForm();
            $this->dispatch('member-created', member: $member->id);
        } else {
            $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
    }

    #[On('edit-member')]
    public function editFormMember(Member $member)
    {
        if (Auth::user()->can('member.edit')) {
            $this->isEdit     = true;
            $this->member     = $member;
            $this->nama       = $member->nama;
            $this->alamat     = $member->alamat;
            $this->no_hp      = $member->no_hp;
            $this->id_profesi = $member->id_profesi;
            $this->dispatch('selected-profesi', selected: $this->id_profesi);
        } else {
            $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
    }

    #[On('close-modal-member')]
    public function resetForm()
    {
        $this->isEdit = false;
        $this->dispatch('clear-select-profesi');
        $this->member     = new Member();
        $this->nama       = null;
        $this->alamat     = null;
        $this->no_hp      = null;
        $this->id_profesi = null;
    }

    public function render()
    {
        if (Auth::user()->canAny(['member.create', 'member.edit'])) {

            return view('livewire.data-master.member.member-form');
        } else {
            $this->dispatch('alert', status: 'error', message: 'Kamu Tidak Memiliki Akses');
        }
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function profesies()
    {
        return Profesi::get(['id', 'nama']);
    }
}
