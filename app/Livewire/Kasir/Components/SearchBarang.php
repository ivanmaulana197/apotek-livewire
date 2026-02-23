<?php
namespace App\Livewire\Kasir\Components;

use App\Models\Barang\Barang;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchBarang extends Component
{
    public $search  = '';
    public $barangs = [];
    public $selectedBarang;

    #[On('refresh_selected_barang')]
    public function refreshSelctedBarang()
    {
        $this->selectedBarang = null;
    }

    public function searchBarang($query)
    {
        $this->search = $query;
        // $tokens = explode(' ', $this->search);

        // $this->barangs = Barang::query()
        // ->select('id', 'nama', 'stok')
        // ->where(function ($query) use ($tokens) {
        //     foreach ($tokens as $token) {
        //         $query->where('nama', 'LIKE', '%' . $token . '%');
        //     }
        // })
        // ->orderByRaw("CASE
        //     WHEN nama LIKE ? THEN 1
        //     WHEN nama LIKE ? THEN 2
        //     ELSE 3
        // END", [$this->search . '%', '%' . $this->search . '%'])
        // ->limit(10) // Batasi hasil
        // ->get();

        // $this->barangs = Barang::select('id', 'nama', 'stok')
        //     ->where('nama', 'like', '%' . $this->search . '%')
        //     ->take(20) // Batasi hasil yang dikembalikan
        //     ->get()
        //     ->toArray();
        // $this->dispatch('barangUpdated', item:$this->barangs);
        return Barang::search($this->search)
            ->select('id', 'nama', 'stok')
            ->orderBy('nama')
            ->limit(50)
            ->get();
        // return $this->barangs;
    }

    public function render()
    {
        return view('livewire.kasir.components.search-barang');
    }

    public function placeholder()
    {
        return view('livewire.kasir.placeholder.search-barang');
    }

    public function highlight($text)
    {
        $tokens  = explode(' ', $this->search);
        $pattern = '/' . implode('|', array_map('preg_quote', $tokens)) . '/i';

        return preg_replace($pattern, '<span class="highlight">$0</span>', $text);
    }
}
