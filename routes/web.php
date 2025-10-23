<?php

use App\Livewire;
use App\Models\Barang\Barang;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    dd(Barang::find('01k6a1fta6xff22vapt6kfm0xg')->toArray());
})->name('welcome');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', Livewire\Auth\Login::class)->name('login');
    // Route::get('/register', Register::class)->name('register');

});

Route::middleware(['role:admin|pegawai'])->group(function () {
    // Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::get('/', Livewire\Home\HomeIndex::class)->name('home');
    Route::get('/kasir', action: Livewire\Kasir\KasirIndex::class)->name('kasir');
    Route::get('/kasir/{id_pesanan_kasir}', action: Livewire\Kasir\KasirIndex::class)->name('kasir.show');

    Route::get("/cetak/struk-pembayaran/{id_transaksi}", Livewire\Cetak\StrukPembayaran::class)->name('cetak.struk-pembayaran');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/data-barang', action: Livewire\Barang\BarangIndex::class)->name('data.barang');
        Route::get('/data-barang/tambah-barang', action: Livewire\Barang\BarangForm\BarangForm::class)->name('data.barang.tambah');
        Route::get('/data-barang/edit-barang/{id}', action: Livewire\Barang\BarangForm\BarangForm::class)->name('data.barang.edit');

        Route::get('/faktur-masuk', action: Livewire\Faktur\FakturIndex::class)->name('faktur');
        Route::get('/faktur-masuk/tambah-faktur', action: Livewire\Faktur\FakturForm\FakturForm::class)->name('faktur.tambah');
        Route::get('/faktur-masuk/edit-faktur/{id}', action: Livewire\Faktur\FakturForm\FakturForm::class)->name('faktur.edit');
        Route::get('/faktur-masuk/detail-faktur/{id}', action: Livewire\Faktur\FakturDetail::class)->name('faktur.detail');

        Route::get('/promo', Livewire\Promo\PromoIndex::class)->name('promo');
        Route::get('/promo/tambah-promo', Livewire\Promo\PromoForm\PromoForm::class)->name('promo.tambah');
        Route::get('/promo/edit-promo/{id}', Livewire\Promo\PromoForm\PromoForm::class)->name('promo.edit');

        Route::group(['prefix' => 'data-master'], function () {
            Route::get('/data-rak', action: Livewire\DataMaster\Rak\RakIndex::class)->name('data-master.rak');
            Route::get('/data-kategori', action: Livewire\DataMaster\Kategori\KategoriIndex::class)->name('data-master.kategori');
            Route::get('/data-supplier', action: Livewire\DataMaster\Supplier\SupplierIndex::class)->name('data-master.supplier')->middleware('permission:supplier.index');

            Route::get('/data-profesi', action: Livewire\DataMaster\Profesi\ProfesiIndex::class)->name('data-master.profesi');
            Route::get('/data-satuan', action: Livewire\DataMaster\Satuan\SatuanIndex::class)->name('data-master.satuan');
            Route::get('/data-dokter', action: Livewire\DataMaster\Dokter\DokterIndex::class)->name('data-master.dokter');
            Route::get('/data-member', action: Livewire\DataMaster\Member\MemberIndex::class)->name('data-master.member');
            Route::get('/data-metode-pembayaran', action: Livewire\DataMaster\MetodePembayaran\MetodePembayaranIndex::class)->name('data-master.metode-pembayaran');
        });

        Route::get('/setting-apotek', Livewire\Settings\SettingApotek\SettingApotekIndex::class)->name('setting-apotek');
    });
});
