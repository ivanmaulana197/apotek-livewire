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

Route::middleware(['auth'])->group(function () {
    // Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::get('/', Livewire\Home\HomeIndex::class)->middleware('permission:home')->name('home');
    // Route::get('/kasir', action: Livewire\Kasir\KasirIndex::class)->name('kasir');
    // Route::get('/kasir/{id_pesanan_kasir}', action: Livewire\Kasir\KasirIndex::class)->name('kasir.show');
    Route::get('/kasir', action: Livewire\KasirCadangan\KasirIndex::class)->middleware('permission:kasir')->name('kasir-cadangan');
    Route::get('/kasir/{id_pesanan_kasir}', action: Livewire\KasirCadangan\KasirIndex::class)->middleware('permission:kasir-show-own|kasir-show-all')->name('kasir-cadangan.show');

    Route::get("/cetak/struk-pembayaran/{id_transaksi}", Livewire\Cetak\StrukPembayaran::class)->middleware('permission:cetak.struk-pembayaran-own|cetak.struk-pembayaran-all')->name('cetak.struk-pembayaran');

    Route::get('/transaksi', Livewire\Transaksi\TransaksiIndex::class)->middleware('permission:transaksi')->name('transaksi');
    // Route::get('/cetak/transaksi-with-barang', Livewire\Cetak\Transaksi\CetakTransaksiListWithBarang::class)->name('cetak.transaksi-with-barang');
    Route::get('/cetak/transaksi/{id_barang}', Livewire\Cetak\Transaksi\CetakLaporanTransaksi::class)->middleware('permission:cetak.transaksi-barang')->name('cetak.transaksi-barang');
    Route::get('/transaksi/detail/{id_transaksi}', Livewire\Transaksi\TransaksiDetail::class)->middleware('permission:transaksi.detail-all|transaksi.detail-own')->name('transaksi.detail');

    Route::get('/faktur-masuk', action: Livewire\Faktur\FakturIndex::class)->middleware('permission:faktur')->name('faktur');
    Route::get('/faktur-masuk/tambah-faktur', action: Livewire\Faktur\FakturForm\FakturForm::class)->middleware('permission:faktur.create')->name('faktur.tambah');
    Route::get('/faktur-masuk/edit-faktur/{id}', action: Livewire\Faktur\FakturForm\FakturForm::class)->middleware('permission:faktur.edit-all|faktur.edit-own')->name('faktur.edit');
    Route::get('/faktur-masuk/detail-faktur/{id}', action: Livewire\Faktur\FakturDetail::class)->middleware('permission:faktur.detail-all|faktur.detail-own')->name('faktur.detail');

    Route::get('/data-barang', action: Livewire\Barang\BarangIndex::class)->middleware('permission:barang')->name('data.barang');
    Route::get('/data-barang/tambah-barang', action: Livewire\Barang\BarangForm\BarangForm::class)->middleware('permission:barang.create')->name('data.barang.tambah');
    Route::get('/data-barang/{barang:id}', action: Livewire\Barang\BarangDetail\BarangDetailIndex::class)->middleware('permission:barang.detail')->name('data.barang.detail');
    Route::get('/data-barang/{id}/faktur', action: Livewire\Barang\BarangDetail\BarangDetailFaktur::class)->middleware('permission:barang.detail-faktur')->name('data.barang.detail.faktur');
    Route::get('/data-barang/{id}/stok', action: Livewire\Barang\BarangDetail\BarangDetailStok::class)->middleware('permission:barang.detail-stok')->name('data.barang.detail.stok');
    Route::get('/data-barang/{id}/expired', action: Livewire\Barang\BarangDetail\BarangDetailExpired::class)->middleware('permission:barang.detail-barang-expired')->name('data.barang.detail.expired');
    Route::get('/data-barang/{id}/transaksi', action: Livewire\Barang\BarangDetail\BarangDetailTransaksi::class)->middleware('permission:barang.detail-transaksi')->name('data.barang.detail.transaksi');
    Route::get('/data-barang/edit-barang/{id}', action: Livewire\Barang\BarangForm\BarangForm::class)->middleware('permission:barang.edit')->name('data.barang.edit');

    Route::get('/promo', Livewire\Promo\PromoIndex::class)->middleware('permission:promo')->name('promo');
    Route::get('/promo/tambah-promo', Livewire\Promo\PromoForm\PromoForm::class)->middleware('permission:promo.create')->name('promo.tambah');
    Route::get('/promo/edit-promo/{id}', Livewire\Promo\PromoForm\PromoForm::class)->middleware('permission:promo.edit')->name('promo.edit');

    Route::group(['prefix' => 'data-master'], function () {
        Route::get('/data-user', action: Livewire\DataMaster\User\UserIndex::class)->middleware('permission:user')->name('data-master.user');
        Route::get('/data-rak', action: Livewire\DataMaster\Rak\RakIndex::class)->middleware('permission:rak')->name('data-master.rak');
        Route::get('/data-kategori', action: Livewire\DataMaster\Kategori\KategoriIndex::class)->middleware('permission:kategori')->name('data-master.kategori');
        Route::get('/data-supplier', action: Livewire\DataMaster\Supplier\SupplierIndex::class)->middleware('permission:supplier')->name('data-master.supplier');
        Route::get('/data-profesi', action: Livewire\DataMaster\Profesi\ProfesiIndex::class)->middleware('permission:profesi')->name('data-master.profesi');
        Route::get('/data-satuan', action: Livewire\DataMaster\Satuan\SatuanIndex::class)->middleware('permission:satuan')->name('data-master.satuan');
        Route::get('/data-dokter', action: Livewire\DataMaster\Dokter\DokterIndex::class)->middleware('permission:dokter')->name('data-master.dokter');
        Route::get('/data-dokter/{id_dokter}', action: Livewire\DataMaster\Dokter\DokterDetail\DokterDetailIndex::class)->middleware('permission:dokter.detail')->name('data-master.dokter.detail');
        Route::get('/data-member', action: Livewire\DataMaster\Member\MemberIndex::class)->middleware('permission:member')->name('data-master.member');
        Route::get('/data-member/{id_member}', action: Livewire\DataMaster\Member\MemberDetail\MemberDetailIndex::class)->middleware('permission:member.detail')->name('data-master.member.detail');
        Route::get('/data-metode-pembayaran', action: Livewire\DataMaster\MetodePembayaran\MetodePembayaranIndex::class)->middleware('permission:metode-pembayaran')->name('data-master.metode-pembayaran');
    });

    Route::get('/stok-opname', action: Livewire\StokOpname\StokOpnameIndex::class)->middleware('permission:stok-opname')->name('stok-opname');
    Route::get('/stok-opname/history', action: Livewire\StokOpname\StokOpnameHistory\StokOpnameHistoryIndex::class)->middleware('permission:stok-opname.history')->name('stok-opname.history');

    Route::get('/info-barang-expired', action: Livewire\Informasi\InfoBarangExpired\InfoBarangExpiredIndex::class)->middleware('permission:info-barang-expired')->name('info.barang-expired');
    Route::get('/info-barang-habis', action: Livewire\Informasi\InfoBarangHabis\InfoBarangHabisIndex::class)->middleware('permission:info-barang-habis')->name('info.barang-habis');

    Route::group(['prefix' => 'cetak'], function () {
        Route::get('/stok-opname/{created_at}', Livewire\Cetak\CetakStokOpname::class)->middleware('permission:stok-opname.cetak-laporan')->name('cetak.stok-opname');
        Route::get('/form-stok-opname', Livewire\Cetak\StokOpname\CetakFormStokOpname::class)->middleware('permission:stok-opname.cetak-form')->name('cetak.form-stok-opname');
        Route::get('/laporan-dokter', Livewire\Cetak\Dokter\CetakLaporanDokter::class)->middleware('permission:dokter.cetak-laporan')->name('cetak.laporan-dokter');
        Route::get('/laporan-dokter/{id_dokter}', Livewire\Cetak\Dokter\CetakDokterDetail::class)->middleware('permission:dokter.cetak-laporan-by-dokter')->name('cetak.laporan-dokter.detail');
        Route::get('/laporan-member/{id_member}', Livewire\Cetak\Member\CetakMemberDetail::class)->middleware('permission:member.cetak-laporan-by-member')->name('cetak.laporan-member.detail');

        Route::get('/laporan-transaksi', Livewire\Cetak\Transaksi\CetakLaporanTransaksi::class)->middleware('permission:transaksi.cetak-laporan-all|transaksi.cetak-laporan-own')->name('cetak.laporan-transaksi');

        Route::get('/laporan-faktur', Livewire\Cetak\Faktur\CetakLaporanFaktur::class)->middleware('permission:faktur.cetak-laporan')->name('cetak.laporan-faktur');
    });

    Route::group(['prefix' => 'laporan'], function () {
        Route::get('/transaksi', Livewire\Laporan\LaporanTransaksi::class)->middleware('permission:transaksi.cetak-laporan-all|transaksi.cetak-laporan-own')->name('laporan.transaksi');
        Route::get('/faktur', Livewire\Laporan\LaporanFaktur::class)->middleware('permission:faktur.cetak-laporan')->name('laporan.faktur');
    });

    Route::get('/setting-apotek', Livewire\Settings\SettingApotek\SettingApotekIndex::class)->middleware('permission:setting')->name('setting-apotek');

});
