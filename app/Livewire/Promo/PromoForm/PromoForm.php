<?php
namespace App\Livewire\Promo\PromoForm;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Profesi;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoBarang;
use App\Models\Promo\PromoTransaksi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PromoForm extends Component
{
    public PromoFormTrigger $form;
    public $isEdit = false;
    public $promo;

    public function mount($id = null)
    {
        if ($id) {
            $this->edit($id);
        } else {
            $this->form->initializeDefaults();
        }
    }

    public function save()
    {
        try {
            // dd($this->form);
            $this->form->formattedForm(false);
            $validate = $this->validate();
            DB::BeginTransaction();
            $promo = Promo::create(Arr::only($validate, (['nama', 'deskripsi', 'tipe', 'start_date', 'end_date', 'is_aktif', 'is_member'])));
            // dd($promo);
            if ($this->form->is_member && $this->form->profesi_member) {
                $promo->profesies()->sync($this->form->profesi_member);
            }
            if ($validate['tipe'] == 'BARANG') {
                foreach ($this->form->barang_promo as $item) {
                    PromoBarang::create([
                        'id_promo'        => $promo->id,
                        'id_barang'       => $item['id_barang'],
                        'syarat_qty'      => $item['syarat_qty'],
                        'tipe_qty'        => $item['tipe_qty'],
                        'id_satuan'       => $item['id_satuan'],
                        'tipe'            => $item['tipe'],
                        'id_barang_bonus' => $item['id_barang_bonus'] ?: null,
                        'bonus_qty'       => $item['bonus_qty'],
                        'id_satuan_bonus' => $item['id_satuan_bonus'] ?: null,
                        'diskon_persen'   => $item['diskon_persen'],
                        'diskon_nominal'  => $item['diskon_nominal'],
                    ]);
                }
            } else if ($validate['tipe'] == 'TRANSAKSI') {
                PromoTransaksi::create([
                    'id_promo'        => $promo->id,
                    'minimal_belanja' => $this->form->minimal_belanja,
                    'tipe_transaksi'  => $this->form->tipe_transaksi,
                    'diskon_persen'   => $this->form->diskon_persen,
                    'diskon_nominal'  => $this->form->diskon_nominal,
                ]);
            } else {
                throw new \Exception("Terjadi kesalahan!");
            }

            DB::commit();
            session()->flash('alert', [
                'title'   => 'Berhasil!',
                'message' => 'Berhasil Menambahkan Promo',
                'icon'    => 'success', // Bisa 'success', 'error', 'warning', dll.
            ]);
            return $this->redirect(route('promo'), true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->form->formattedForm();
            // dd($e->getMessage());
            throw $e;
        }
    }

    public function edit($promo)
    {
        $this->promo = Promo::with([
            'promoBarang',
            'promoTransaksi',
            'profesies' => function ($q) {
                $q->select('id')->pluck('id')->toArray();
            }])->findOrFail($promo);
        // dd($this->promo);
        $this->isEdit = true;
        $this->form->setValues($this->promo->toArray());
    }

    public function update()
    {
        try {
            $this->form->formattedForm(false);
            $validate = $this->validate();

            DB::beginTransaction();

            // 🔹 1. Update data utama promo
            $this->promo->update(Arr::only($validate, [
                'nama', 'deskripsi', 'tipe', 'start_date', 'end_date', 'is_aktif', 'is_member',
            ]));

            // 🔹 2. Update relasi profesi (member)
            if ($this->form->is_member && $this->form->profesi_member) {
                $this->promo->profesies()->sync($this->form->profesi_member);
            } else {
                $this->promo->profesies()->detach();
            }

            // 🔹 3. Update atau Tambahkan promo BARANG
            if ($validate['tipe'] === 'BARANG') {
                foreach ($this->form->barang_promo as $item) {
                    // updateOrCreate berdasar id_barang & id_promo
                    $this->promo->promoBarang()
                        ->updateOrCreate(
                            [
                                'id_promo'  => $this->promo->id,
                                'id_barang' => $item['id_barang'],
                            ],
                            [
                                'id_satuan'       => $item['id_satuan'],
                                'id_satuan_bonus' => $item['id_satuan_bonus'] ?: null,
                                'syarat_qty'      => $item['syarat_qty'],
                                'tipe_qty'        => $item['tipe_qty'],
                                'tipe'            => $item['tipe'],
                                'id_barang_bonus' => $item['id_barang_bonus'] ?: null,
                                'bonus_qty'       => $item['bonus_qty'],
                                'diskon_persen'   => $item['diskon_persen'],
                                'diskon_nominal'  => $item['diskon_nominal'],
                            ]
                        );
                }
            }

            // 🔹 4. Update atau Tambahkan promo TRANSAKSI
            else if ($validate['tipe'] === 'TRANSAKSI') {
                $this->promo->promoTransaksi()
                    ->updateOrCreate(
                        ['id_promo' => $this->promo->id],
                        [
                            'minimal_belanja' => $this->form->minimal_belanja,
                            'tipe_transaksi'  => $this->form->tipe_transaksi,
                            'diskon_persen'   => $this->form->diskon_persen,
                            'diskon_nominal'  => $this->form->diskon_nominal,
                        ]
                    );
            } else {
                throw new \Exception("Tipe promo tidak dikenali!");
            }

            DB::commit();

            session()->flash('alert', [
                'title'   => 'Berhasil!',
                'message' => 'Promo berhasil diperbarui.',
                'icon'    => 'success',
            ]);

            return $this->redirect(route('promo'), true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->form->formattedForm();
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.promo.promo-form.promo-form');
    }

    public function searchBarang($query)
    {
        $tokens  = explode(' ', $query);
        $barangs = Barang::query()
            ->select('id', 'nama', 'stok')
            ->where(function ($query) use ($tokens) {
                foreach ($tokens as $token) {
                    $query->where('nama', 'LIKE', '%' . $token . '%');
                }
            })
            ->orderByRaw("CASE WHEN nama LIKE ? THEN 1 WHEN nama LIKE ? THEN 2 ELSE 3 END", [$query . '%', '%' . $query . '%'])
            ->limit(10) // Batasi hasil
            ->get();
        return $barangs;
    }

    public function fetchSingleItem($id)
    {
        if ($id) {
            $data['data'] = Barang::select('barang.id', 'barang.nama', 'barang.stok', 'satuan.nama as satuan')
                ->leftJoin('satuan', 'satuan.id', '=', 'barang.id_satuan_utama')->findOrFail($id)->toArray();
            $barang              = Barang::with('opsi_satuan', 'satuan_utama')->findOrFail($id);
            $data['list_satuan'] = FormatHelper::formatTomSelect($barang->all_satuan, 'id_satuan', 'satuan'); //$barang->all_satuan;
                                                                                                              // dd($data);
            return $data;
        } else {
            return null;
        }
    }

    #[Computed(persist: true, seconds: 3600, cache: true)]
    public function profesies()
    {
        return Profesi::select('id', 'nama')->get();
    }
}
