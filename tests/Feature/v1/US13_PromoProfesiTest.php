<?php
namespace Tests\Feature\v1;

use App\Livewire\KasirCadangan\Promo\PromoBarangList;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Member;
use App\Models\DataMaster\Profesi;
use App\Models\DataMaster\Satuan;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoBarang;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\Feature\BaseTest;

class US13_PromoProfesiTest extends BaseTest
{
    public function test_buat_promo_profesi_aktif(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesi = Profesi::firstOrCreate(['nama' => 'UMUM']);
        $satuan  = Satuan::firstOrCreate(['nama' => 'PCS']);
        $barang  = Barang::factory()->create(['id_satuan_utama' => $satuan->id]);

        $promo = Promo::create([
            'nama'       => 'Promo Profesi UMUM',
            'deskripsi'  => 'Khusus profesi UMUM',
            'tipe'       => 'BARANG',
            'start_date' => Carbon::now()->subDay(),
            'end_date'   => Carbon::now()->addDays(7),
            'is_aktif'   => true,
            'is_member'  => true,
        ]);
        $promo->profesies()->sync([$profesi->id]);
        PromoBarang::create([
            'id_promo'       => $promo->id,
            'id_barang'      => $barang->id,
            'id_satuan'      => $satuan->id,
            'syarat_qty'     => 1,
            'tipe_qty'       => 'MINIMAL',
            'tipe'           => 'HARGA',
            'diskon_nominal' => 1000,
        ]);

        $member = Member::create(['nama' => 'Member X', 'id_profesi' => $profesi->id, 'alamat' => 'JL', 'no_hp' => '08', 'id_user' => $admin->id]);

        Livewire::test(PromoBarangList::class)
            ->call('getPromo', null, $barang->id, 1, $member->id, 'form')
            ->assertSee('Promo Profesi UMUM');
    }

    public function test_member_profesi_tidak_sesuai_promo_tidak_berlaku(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesiA = Profesi::firstOrCreate(['nama' => 'UMUM']);
        $profesiB = Profesi::firstOrCreate(['nama' => 'DOKTER']);
        $satuan   = Satuan::firstOrCreate(['nama' => 'PCS']);
        $barang   = Barang::factory()->create(['id_satuan_utama' => $satuan->id]);
        $promo    = Promo::create([
            'nama'       => 'Promo Khusus UMUM',
            'deskripsi'  => 'Hanya untuk UMUM',
            'tipe'       => 'BARANG',
            'start_date' => Carbon::now()->subDay(),
            'end_date'   => Carbon::now()->addDays(7),
            'is_aktif'   => true,
            'is_member'  => true,
        ]);
        $promo->profesies()->sync([$profesiA->id]);
        PromoBarang::create([
            'id_promo'       => $promo->id,
            'id_barang'      => $barang->id,
            'id_satuan'      => $satuan->id,
            'syarat_qty'     => 1,
            'tipe_qty'       => 'MINIMAL',
            'tipe'           => 'HARGA',
            'diskon_nominal' => 1000,
        ]);
        $memberB = Member::create(['nama' => 'Member B', 'id_profesi' => $profesiB->id, 'alamat' => 'JL', 'no_hp' => '09', 'id_user' => $admin->id]);

        Livewire::test(PromoBarangList::class)
            ->call('getPromo', null, $barang->id, 1, $memberB->id, 'form')
            ->assertDontSee('Promo Khusus UMUM');
    }

    public function test_update_profesi_promo_efek_langsung(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin);
        $profesiA = Profesi::firstOrCreate(['nama' => 'UMUM']);
        $profesiB = Profesi::firstOrCreate(['nama' => 'DOKTER']);
        $satuan   = Satuan::firstOrCreate(['nama' => 'PCS']);
        $barang   = Barang::factory()->create(['id_satuan_utama' => $satuan->id]);
        $promo    = Promo::create([
            'nama'       => 'Promo Profesi Update',
            'deskripsi'  => 'Update profesi',
            'tipe'       => 'BARANG',
            'start_date' => Carbon::now()->subDay(),
            'end_date'   => Carbon::now()->addDays(7),
            'is_aktif'   => true,
            'is_member'  => true,
        ]);
        $promo->profesies()->sync([$profesiA->id]);
        PromoBarang::create([
            'id_promo'       => $promo->id,
            'id_barang'      => $barang->id,
            'id_satuan'      => $satuan->id,
            'syarat_qty'     => 1,
            'tipe_qty'       => 'MINIMAL',
            'tipe'           => 'HARGA',
            'diskon_nominal' => 1000,
        ]);
        $memberB = Member::create(['nama' => 'Member B', 'id_profesi' => $profesiB->id, 'alamat' => 'JL', 'no_hp' => '09', 'id_user' => $admin->id]);

        $promo->profesies()->sync([$profesiB->id]);

        Livewire::test(PromoBarangList::class)
            ->call('getPromo', null, $barang->id, 1, $memberB->id, 'form')
            ->assertSee('Promo Profesi Update');
    }
}
