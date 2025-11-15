@script
    <script>
        Alpine.data('stokBarangForm', () => ({
            jumlah: $wire.entangle('jumlah'),
            all_satuan: $wire.entangle('all_satuan'),
            formattedStok(stok) {
                let hasil = [];
                const EPS = 1e-9; // toleransi error kecil
                const fmod = (a, b) => a - Math.floor(a / b) * b;
                this.all_satuan.forEach((item) => {
                    const rasio = parseFloat(item.rasio);
                    if (rasio <= 0) return;

                    const jumlah = Math.floor((stok / rasio) + EPS);
                    if (jumlah > 0) {
                        hasil.push(`${jumlah} ${item.satuan}`);
                    }

                    stok = fmod(stok, rasio);
                })

                return hasil.join(',  ');
            },
        }))
    </script>
@endscript

<div class="" x-data="stokBarangForm">
    <div class="flex justify-between">
        <h1 class="text-lg font-bold">Form Tambah Stok Batch Barang</h1>
        <x-modal.button-close @click=" $dispatch('toggle-modal-edit_stok_barang',{ edit_stok_barangModal: false });" />
    </div>
    <form wire:submit.prevent='save'>
        <div class="grid grid-cols-1">
            <x-form.input label="Jumlah Stok" x-model="jumlah" onkeyup="formatForInputNumber(this)" input-group="right" errorName="jumlah"
                          label-input-group="{{ $nama_satuan_utama }}" />
            <span class="ms-2 font-semibold text-sm text-gray-700 dark:text-gray-300 " x-text="' = '+formattedStok(formatStringToNumber(jumlah))"></span>
            <div class="">
                <x-form.label label="Tanggal Expired" />
                <x-form.input-date nama="tgl_expired" model="tgl_expired" placeholder="Masukan Tanggal Expired" :singleDatePicker=true errorName="tgl_expired"
                                   :ranges=false minDate="{{ now()->toDateString() }}" />
            </div>
            <x-form.input label="Harga Beli" wire:model="harga_beli" onkeyup="formatForInputNumber(this)" input-group="both" label-left-input-group="Rp."
                          label-right-input-group="/{{ $nama_satuan_utama }}" />

            <div class="flex flex-wrap my-2 gap-2 justify-end">
                <button class="btn btn-sm" type="submit">Simpan</button>
                <button class="btn btn-sm bg-gray-200 hover:bg-gray-400 text-black hover:text-black" type="button">Batal</button>
            </div>
        </div>
    </form>
</div>
