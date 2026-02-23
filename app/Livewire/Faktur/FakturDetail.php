<?php
namespace App\Livewire\Faktur;

use App\Models\Faktur\Faktur;
use App\Traits\CustomDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class FakturDetail extends Component
{
    use CustomDataTable;

    public $faktur;

    public function mount($id = null)
    {
        $this->sortBy  = 'history_barang.updated_at';
        $this->sortDir = 'desc';
        $this->faktur  = Faktur::with(['detail_faktur.detail_barang' => function ($q) {
            $q->with(['barang:id,kode,nama,id_satuan_utama', 'barang.satuan_utama:id,nama', 'satuan:id,nama']);
        },
            'supplier:id,nama'])
            ->findOrFail($id);
        if (! Auth::user()->can('faktur.detail-all') && ! (Auth::user()->can('faktur.detail-own') && $this->faktur->id_user == Auth::user()->id)) {
            return redirect()->back()->with([
                'alert' => [
                    'title'   => 'Tidak Diizinkan',
                    'message' => 'Anda tidak memiliki akses yang diperlukan.',
                    'icon'    => 'error', // Ikon SweetAlert ('success', 'error', 'warning', 'info')
                ],
            ]);
        }
        // dd($this->faktur->toArray());
    }

    #[On('update-status-faktur')]
    public function updateStatusFaktur($id)
    {
        $this->faktur->status = $this->faktur->status == 'LUNAS' ? 'BELUM LUNAS' : 'LUNAS';
        $this->faktur->save();
    }

    public function render()
    {
        return view('livewire.faktur.faktur-detail');
    }
}
