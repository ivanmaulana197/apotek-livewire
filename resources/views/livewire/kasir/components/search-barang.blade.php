<div class="grid grid-cols-12">
    <style>
        .highlight_barang {
            color: rgb(69, 191, 244);
        }
    </style>

    {{-- <x-form.label label="Pilih Barang" /> --}}

    <div class="col-span-12">
        <div x-init="window.selectSearchBarang = new TomSelect('#barang_search', {
            //  maxOptions: 10,
            maxItems: 1,
            //    loadThrottle: 200,
            sortField: {
                field: 'text',
                direction: 'asc'
            },
            valueField: 'value',
            labelField: 'text',
            plugins: [
                'remove_button'
            ],
            loadingClass: 'z-10',
            load: function(query, callback) {
                //retun callback kosong jika query kosong
                if (query.length < 1) return callback();
                // hit ke function searchBarang dan mendapatkan data
                data = $wire.searchBarang(query);
                //data membutuhkan promise
                data.then(item => {
                    //mapping data ke option
                    option = item.map(item => ({
                        value: item.id,
                        text: item.nama + ' [' + parseInt(item.stok) + ']'
                    }));
                    callback(option);
                })
            },
        });
        Livewire.on('focus-select-barang', () => {
            selectSearchBarang.clear();
            $('#barang_search-ts-control').focus();
        });" wire:ignore>
            {{-- <input id="barang_search" type="text" class="form-input mt-1 block w-full" placeholder="Cari Barang..." wire:model.live='selectedBarang'/> --}}
            <div x-data="{
                handleSelectedBarang(event) {
                    value = event.target.value;
                    if (!value) {
                        $('#barang_search-ts-control').focus();
                    } else {
                        $dispatch('focus-input-jumlah');
                    }
                    $dispatch('selectedBarang', { idBarang: value });
                }
            }">
                <select id="barang_search" class="rounded-md" placeholder="Cari Barang..." wire:model.live='selectedBarang' @change="handleSelectedBarang($event)">

                </select>
            </div>
        </div>

    </div>
</div>
