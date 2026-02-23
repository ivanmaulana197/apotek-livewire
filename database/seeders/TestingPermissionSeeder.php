<?php
namespace Database\Seeders;

use App\Models\Spatie\Permission;
use App\Models\Spatie\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsList = [
            //home
            "home",
            'home.info-pendapatan-harian', 'home.info-total-transaksi', 'home.info-barang-habis', 'home.info-barang-expired',
            'home.info-transaksi',
            "home.grafik-pendapatan", 'home.grafik-transaksi',

            //kasir
            'kasir', 'kasir-show-own', 'kasir-show-all',
            'pesanan-kasir', "pesanan-kasir.list-own", 'pesanan-kasir.list-all', "pesanan-kasir.delete-own", "pesanan-kasir.delete-all", "pesanan-kasir.create",
            'kasir.cetak.struk-pembayaran-own', 'kasir.cetak.struk-pembayaran-all',

            //faktur
            "faktur", "faktur.list-own", "faktur.list-all", "faktur.create", "faktur.edit-own", "faktur.edit-all", "faktur.detail-own", "faktur.detail-all", "faktur.delete-own", "faktur.delete-all",
            "faktur.info-jumlah-faktur", "faktur.info-total-pengeluaran", "faktur.info-total-hutang",
            "faktur.ubah-status", "faktur.cetak-laporan",

            //promo
            "promo", "promo.list", "promo.create", "promo.edit", "promo.detail", "promo.delete",

            //barang
            "barang", "barang.list", "barang.create", "barang.edit", "barang.detail", "barang.delete",
            "barang.pembulatan-all",
            "barang.detail-faktur", "barang.detail-stok", "barang.detail-transaksi", "barang.detail-barang-expired",
            "barang.stok-barang.create", "barang.stok-barang.edit", "barang.stok-barang.delete",

            //transaksi
            "transaksi", "transaksi.cetak-laporan-own", "transaksi.cetak-laporan-all",
            "transaksi.info-total-transaksi-by-metode-pembayaran", "transaksi.info-total-transaksi-umum", "transaksi.info-total-transaksi-resep", "transaksi.info-total-jumlah-transaksi",
            "transaksi.info-all-transaksi",
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
            "metode_pembayaran", "metode_pembayaran.list", "metode_pembayaran.create", "metode_pembayaran.edit", "metode_pembayaran.detail", "metode_pembayaran.delete",

            //role
            "role", "role.list", "role.create", "role.edit", "role.detail", "role.delete",

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

        $now        = now();
        $insertData = [];

        foreach ($permissionsList as $name) {
            $insertData[] = [
                'id'         => (string) Str::ulid(),
                'name'       => $name,
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Permission::insert($insertData);

        // Assign to Super Admin and Admin
        $superAdmin = Role::findByName('Super Admin');
        $admin      = Role::findByName('Admin');

        // Direct DB insert for role_has_permissions to avoid overhead
        // But spatie caches permissions, so we might need to clear cache if we bypass
        // For tests, cache is usually array, so it might be fine or we might need to reset cache.
        // Safer to use givePermissionTo, but that's slow.
        // Let's stick to givePermissionTo for now, but since we bulk inserted permissions, at least that part is fast.
        // givePermissionTo with array is optimized in Spatie v6? It fetches models.

        $superAdmin->givePermissionTo($permissionsList);
        $admin->givePermissionTo($permissionsList);

        $pegawai = Role::findByName('Pegawai');
        $pegawai->givePermissionTo([
            "barang", "barang.list", "barang.detail",
            "dokter", "dokter.create", "dokter.list", "dokter.detail",

            "home", 'home.info-pendapatan-harian', 'home.info-total-transaksi', "home.info-transaksi",

            "kasir", "kasir-show-own", "kasir.cetak.struk-pembayaran-own",
            "pesanan-kasir", "pesanan-kasir.list-own", "pesanan-kasir.delete-own", "pesanan-kasir.create",

            "transaksi", "transaksi.cetak-laporan-own", "transaksi.detail-own", "transaksi.list-own",
            "transaksi.info-total-transaksi-by-metode-pembayaran", "transaksi.info-total-transaksi-umum", "transaksi.info-total-transaksi-resep", "transaksi.info-total-jumlah-transaksi",
            // "transaksi.retur", // Removed retur from Pegawai

            "member", "member.list", "member.detail", "member.create",

            "metode_pembayaran", "metode_pembayaran.list", "metode_pembayaran.detail", "metode_pembayaran.create",
        ]);
        $gudang = Role::findByName('Gudang');
        $gudang->givePermissionTo([
            "home", 'home.info-barang-habis', 'home.info-barang-expired',

            "barang", "barang.list", "barang.detail",

            "supplier", "supplier.list", "supplier.create", "supplier.detail",

            //faktur
            "faktur", "faktur.list-own", "faktur.create", "faktur.edit-own", "faktur.detail-own", "faktur.delete-own",
            "faktur.info-jumlah-faktur", "faktur.info-total-pengeluaran", "faktur.info-total-hutang",
            // "faktur.ubah-status", "faktur.cetak-laporan",

            //info barang expired
            "info-barang-expired", "info-barang-expired.list", "info-barang-expired.trigger",
            //info barang habis
            "info-barang-habis", "info-barang-habis.list",

            "stok-opname", "stok-opname.list", "stok-opname.history", "stok-opname.cetak-laporan",
            "stok-opname.refresh-stok-opname", "stok-opname.sinkronisasi-stok-keseluruhan", "stok-opname.cetak-form",
        ]);
    }
}
