<?php
namespace Database\Seeders;

use App\Models\Spatie\Permission;
use App\Models\Spatie\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin  = Role::findByName('admin');
        $barang = ['list barang', 'create barang', 'edit barang', 'read barang', 'delete barang'];
        foreach ($barang as $item) {
            Permission::create(["name" => $item]);
        }
        $admin->givePermissionTo($barang);

        $permission = [
            //home
            "home",
            'home.info-pendapatan-harian', 'home.info-total-transaksi', 'home.info-barang-habis', 'home.info-barang-expired',
            "home.grafik-pendapatan", 'home.grafik-transaksi',

            //kasir
            'kasir', 'kasir-show-own', 'kasir-show-all',
            // 'kasir:simpan transaksi',
            'pesanan-kasir', "pesanan-kasir.list-own", 'pesanan-kasir.list-all', "pesanan-kasir.delete-own", "pesanan-kasir.delete-all", "pesanan-kasir.create",
            'cetak.struk-pembayaran-own', 'cetak.struk-pembayaran-all',

            //faktur
            "faktur", "faktur.list-own", "faktur.list-all", "faktur.create", "faktur.edit-own", "faktur.edit-all", "faktur.detail-own", "faktur.detail-all", "faktur.delete-own", "faktur.delete-all",
            // "faktur", "faktur.list", "faktur.list-all", "faktur.create", "faktur.edit", "faktur.edit-all", "faktur.detail", "faktur.detail-all", "faktur.delete", "faktur.delete-all",
            "faktur.info-jumlah-faktur", "faktur.info-total-pengeluaran", "faktur.info-total-hutang",
            "faktur.ubah-status", "faktur.cetak-laporan",

            //promo
            "promo", "promo.list", "promo.create", "promo.edit", "promo.detail", "promo.delete",

            //barang
            "barang", "barang.list", "barang.create", "barang.edit", "barang.detail", "barang.delete",
            "barang.pembulatan-all",
            // "barang.list:detail barang", 'barang.list:edit barang', "barang.list:delete barang",
            "barang.detail-faktur", "barang.detail-stok", "barang.detail-transaksi", "barang.detail-barang-expired",
            "barang.stok-barang.create", "barang.stok-barang.edit", "barang.stok-barang.delete",

            //transaksi
            "transaksi", "transaksi.cetak-laporan-own", "transaksi.cetak-laporan-all",
            "transaksi.info-total-transaksi-by-metode-pembayaran", "transaksi.info-total-transaksi-umum", "transaksi.info-total-transaksi-resep", "transaksi.info-total-jumlah-transaksi",
            "transaksi.detail-all", "transaksi.detail-own",
            "transaksi.list-all", "transaksi.list-own",
            "transaksi.filter-transaksi",
            "transaksi.change-shift-transaksi",
            "transaksi.retur",

            //stokopname
            "stok-opname", "stok-opname.list", "stok-opname.history", "stok-opname.cetak-laporan",
            "stok-opname.refresh-stok-opname", "stok-opname.sinkronisasi-stok-keseluruhan", "stok-opname.cetak-form",
            "stok-opname.info-total-kerugian",

            //data master
            "user", "user.list", "user.create", "user.edit", "user.detail", "user.delete",
            //supplier
            "supplier", "supplier.list", "supplier.create", "supplier.edit", "supplier.detail", "supplier.delete",
            //rak
            "rak", "rak.list", "rak.create", "rak.edit", "rak.detail", "rak.delete",
            //kategori barang
            "kategori", "kategori.list", "kategori.create", "kategori.edit", "kategori.detail", "kategori.delete",
            //member
            "member", "member.list", "member.create", "member.edit", "member.detail", "member.delete",
            "member.detail.info-total-transaksi", "member.detail.info-total-belanja", "member.detail.info-total-benefit",
            "member.cetak-laporan-by-member",
            "member.cetak-laporan",
            "member.detail.list-barang-terlaris", "member.detail.list-data-transaksi",
            //profesi
            "profesi", "profesi.list", "profesi.create", "profesi.edit", "profesi.detail", "profesi.delete",
            //satuan
            "satuan", "satuan.list", "satuan.create", "satuan.edit", "satuan.detail", "satuan.delete",

            //dokter
            "dokter", "dokter.list", "dokter.create", "dokter.edit", "dokter.detail", "dokter.delete",
            "dokter.cetak-laporan",
            "dokter.cetak-laporan-by-dokter",
            'dokter.detail.list-data-barang-terjual', 'dokter.detail.list-data-transaksi',
            "dokter.detail.info-total-tebus-obat", "dokter.detail.info-total-transaksi",
            //metode pembayaran
            "metode-pembayaran", "metode-pembayaran.list", "metode-pembayaran.create", "metode-pembayaran.edit", "metode-pembayaran.detail", "metode-pembayaran.delete",

            //transaksi
            // "transaksi", "transaksi.list", "transaksi.create", "transaksi.edit", "transaksi.detail", "transaksi.delete",
            // "transaksi.advance-search",

            //info barang expired
            "info-barang-expired", "info-barang-expired.list", "info-barang-expired.trigger",
            //info barang habis
            "info-barang-habis", "info-barang-habis.list",

            //laporan transaksi
            "laporan-transaksi", "laporan-transaksi.cetak-laporan",
            //laporan faktur
            "laporan-faktur", "laporan-faktur.cetak-laporan",

            //setting
            "setting", "setting.edit",
        ];
        foreach ($permission as $item) {
            Permission::create(["name" => $item]);
        }
        $admin->givePermissionTo($permission);
        $pegawai = Role::findByName('pegawai');
        $pegawai->givePermissionTo([
            "home", 'home.info-pendapatan-harian', 'home.info-total-transaksi',
            "kasir-show-own",
        ]);

    }
}
