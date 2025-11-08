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
            //promo
            "promo.index", "promo.list", "promo.create", "promo.edit", "promo.detail", "promo.delete",

            //profesi
            "profesi.index", "profesi.list", "profesi.create", "profesi.edit", "profesi.detail", "profesi.delete",

            //supplier
            "supplier.index", "supplier.list", "supplier.create", "supplier.edit", "supplier.detail", "supplier.delete",

            //rak
            "rak.index", "rak.list", "rak.create", "rak.edit", "rak.detail", "rak.delete",

            //kategori barang
            "kategori.index", "kategori.list", "kategori.create", "kategori.edit", "kategori.detail", "kategori.delete",

            //member
            "member.index", "member.list", "member.create", "member.edit", "member.detail", "member.delete",

            //satuan
            "satuan.index", "satuan.list", "satuan.create", "satuan.edit", "satuan.detail", "satuan.delete",

            //dokter
            "dokter.index", "dokter.list", "dokter.create", "dokter.edit", "dokter.detail", "dokter.delete",

            //metode pembayaran
            "metode-pembayaran.index", "metode-pembayaran.list", "metode-pembayaran.create", "metode-pembayaran.edit", "metode-pembayaran.detail", "metode-pembayaran.delete",

            //transaksi
            "transaksi.index", "transaksi.list", "transaksi.create", "transaksi.edit", "transaksi.detail", "transaksi.delete",
            "transaksi.advance-search",

            //faktur
            "faktur.index", "faktur.list", "faktur.create", "faktur.edit", "faktur.detail", "faktur.delete",

            //barang
            "barang.index", "barang.list", "barang.create", "barang.edit", "barang.detail", "barang.delete",

            //setting
            "setting.index", "setting.edit",
        ];
        foreach ($permission as $item) {
            Permission::create(["name" => $item]);
        }
        $admin->givePermissionTo($permission);
    }
}