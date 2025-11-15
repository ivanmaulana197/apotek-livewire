<?php

use App\Livewire;
// use App\Models\DataMaster\Rak;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    // dd(Rak::cursor()->collect());
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

    Route::get('/transaksi', Livewire\Transaksi\TransaksiIndex::class)->name('transaksi');
    // Route::get('/cetak/transaksi-with-barang', Livewire\Cetak\Transaksi\CetakTransaksiListWithBarang::class)->name('cetak.transaksi-with-barang');
    Route::get('/cetak/transaksi/{id_barang}', Livewire\Cetak\Transaksi\CetakLaporanTransaksi::class)->name('cetak.transaksi-barang');
    Route::get('/transaksi/detail/{id_transaksi}', Livewire\Transaksi\TransaksiDetail::class)->name('transaksi.detail');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/data-barang', action: Livewire\Barang\BarangIndex::class)->name('data.barang');
        Route::get('/data-barang/tambah-barang', action: Livewire\Barang\BarangForm\BarangForm::class)->name('data.barang.tambah');
        Route::get('/data-barang/{barang:id}', action: Livewire\Barang\BarangDetail\BarangDetailIndex::class)->name('data.barang.detail');
        Route::get('/data-barang/{id}/faktur', action: Livewire\Barang\BarangDetail\BarangDetailFaktur::class)->name('data.barang.detail.faktur');
        Route::get('/data-barang/{id}/stok', action: Livewire\Barang\BarangDetail\BarangDetailStok::class)->name('data.barang.detail.stok');
        Route::get('/data-barang/{id}/expired', action: Livewire\Barang\BarangDetail\BarangDetailExpired::class)->name('data.barang.detail.expired');
        Route::get('/data-barang/{id}/transaksi', action: Livewire\Barang\BarangDetail\BarangDetailTransaksi::class)->name('data.barang.detail.transaksi');
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
            Route::get('/data-dokter/{id_dokter}', action: Livewire\DataMaster\Dokter\DokterDetail\DokterDetailIndex::class)->name('data-master.dokter.detail');
            Route::get('/data-member', action: Livewire\DataMaster\Member\MemberIndex::class)->name('data-master.member');
            Route::get('/data-member/{id_member}', action: Livewire\DataMaster\Member\MemberDetail\MemberDetailIndex::class)->name('data-master.member.detail');
            Route::get('/data-metode-pembayaran', action: Livewire\DataMaster\MetodePembayaran\MetodePembayaranIndex::class)->name('data-master.metode-pembayaran');
        });

        Route::get('/stok-opname', action: Livewire\StokOpname\StokOpnameIndex::class)->name('stok-opname');
        Route::get('/stok-opname/history', action: Livewire\StokOpname\StokOpnameHistory\StokOpnameHistoryIndex::class)->name('stok-opname.history');

        Route::get('/info-barang-expired', action: Livewire\Informasi\InfoBarangExpired\InfoBarangExpiredIndex::class)->name('info.barang-expired');
        Route::get('/info-barang-habis', action: Livewire\Informasi\InfoBarangHabis\InfoBarangHabisIndex::class)->name('info.barang-habis');

        Route::group(['prefix' => 'cetak'], function () {
            Route::get('/stok-opname/{created_at}', Livewire\Cetak\CetakStokOpname::class)->name('cetak.stok-opname');
            Route::get('/form-stok-opname', Livewire\Cetak\StokOpname\CetakFormStokOpname::class)->name('cetak.form-stok-opname');
            Route::get('/laporan-dokter', Livewire\Cetak\Dokter\CetakLaporanDokter::class)->name('cetak.laporan-dokter');
            Route::get('/laporan-dokter/{id_dokter}', Livewire\Cetak\Dokter\CetakDokterDetail::class)->name('cetak.laporan-dokter.detail');
            Route::get('/laporan-member/{id_member}', Livewire\Cetak\Member\CetakMemberDetail::class)->name('cetak.laporan-member.detail');

            Route::group(['prefix' => 'laporan-transaksi'], function () {
                Route::get('/', Livewire\Cetak\Transaksi\CetakLaporanTransaksi::class)->name('cetak.laporan-transaksi');
            });
            Route::get('/laporan-faktur', Livewire\Cetak\Faktur\CetakLaporanFaktur::class)->name('cetak.laporan-faktur');
        });

        Route::group(['prefix' => 'laporan'], function () {
            Route::get('/transaksi', Livewire\Laporan\LaporanTransaksi::class)->name('laporan.transaksi');
            Route::get('/faktur', Livewire\Laporan\LaporanFaktur::class)->name('laporan.faktur');
        });

        Route::get('/setting-apotek', Livewire\Settings\SettingApotek\SettingApotekIndex::class)->name('setting-apotek');
    });
});
