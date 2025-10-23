<div class="col-span-12">
    <h1 class="mb-1 text-xl font-bold">Harga Jual Utama </h1>
    <hr>
    <div class="grid grid-cols-12 gap-x-2">
        {{-- Harga jual utama --}}
        <div class="col-span-12 md:col-span-4">
            <x-form.input error-name="form.harga_beli_default" label="Referensi Harga Beli" x-model="harga_beli_default" input-group="left" label-input-group="Rp"
                          @keyup="updateHargaBeli($el)" />
            <x-badge message="Harga Referensi untuk harga beli yang tidak ada faktur" />
        </div>
        <div class="col-span-12 md:col-span-8">
            <div class="grid grid-cols-12 gap-x-2">
                <div class="col-span-12 sm:col-span-4">
                    <x-form.select-alpine label="Mode Markup Jual" :emptyOption=false x-model='mode_jual_utama' errorName="form.mode_jual_utama">
                        <x-slot:custom-option>
                            <option value="PERSEN">PERSEN</option>
                            <option value="HARGA">HARGA</option>
                        </x-slot:custom-option>
                    </x-form.select-alpine>
                </div>
                <div class="col-span-12 sm:col-span-8">
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">
                            <x-form.input label="Persen Jual" error-name="form.persen_jual_utama" input-group="right" label-input-group="%"
                                          @keyup="updatePersenJual('utama')" x-model='persen_jual_utama' ::disabled="mode_jual_utama !== 'PERSEN'" />
                        </div>
                        <div class="flex flex-col items-center justify-center col-span-1">
                            <span class="label"></span>
                            <span class="mt-4 text-lg">=</span>
                        </div>
                        <div class="col-span-6">
                            <x-form.input label="Harga Jual" error-name="form.harga_jual_utama" input-group="left" label-input-group="Rp"
                                          @keyup="updateHargaJual('utama')" x-model='harga_jual_utama' ::disabled="mode_jual_utama !== 'HARGA'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="opsi_harga.length > 0" class="grid grid-cols-1 col-span-12 gap-2">

            {{-- harga jual alternatif --}}
            <div class="mt-2 ">
                <h1 class="mb-1 text-xl font-bold">Harga Jual Alternatif</h1>
                <hr>
            </div>

            <template x-for="(item, index) in opsi_harga" :key="index">
                <div :class="(index % 2 === 0 ? 'bg-slate-200 dark:bg-slate-800' : '') + ' rounded-md px-2'">
                    <div class="flex items-center justify-around gap-1">
                        <span x-text="`#${index+1}`"></span>
                        <div class="grid grid-cols-12 mb-2 gap-x-2">
                            <div class="col-span-12 md:col-span-4 ">
                                <x-form.label label="Jumlah Minimal Jual" />
                                <div class="grid grid-cols-12 border join border-bordergray dark:border-darkborder">
                                    <div class="col-span-6">
                                        <x-form.input error-name="form.opsi_harga.${index}.jumlah_min" x-model='item.jumlah_min'
                                                      @keyup="formatForInputNumber($el)" placeholder="Masukan Jumlah Minimal Jual" class="join-item">
                                        </x-form.input>
                                    </div>
                                    <div class="col-span-6">
                                        <x-form.select-alpine @change="changeSatuanOpsiHarga(index)" error-name="form.opsi_harga.${index}.id_satuan"
                                                              ::name="'opsi_harga_' + index" :emptyOption=false x-model='item.id_satuan' class="join-item">
                                            <x-slot:custom-option></x-slot:custom-option>
                                        </x-form.select-alpine>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-12 md:col-span-8">
                                <div class="grid grid-cols-12 gap-x-2">
                                    <div class="col-span-12 sm:col-span-4 ">
                                        <x-form.select-alpine error-name="form.opsi_harga.${index}.mode" label="Markup Jual" :emptyOption=false
                                                              x-model='item.mode'>
                                            <x-slot:custom-option>
                                                <option value="PERSEN">Persen</option>
                                                <option value="HARGA">Harga</option>
                                            </x-slot:custom-option>
                                        </x-form.select-alpine>
                                    </div>
                                    <div class="col-span-12 sm:col-span-8">
                                        <div class="grid grid-cols-12">
                                            <div class="col-span-12 min-[450px]:col-span-5">
                                                <x-form.input error-name="form.opsi_harga.${index}.persen" label="Persen Jual" input-group="right"
                                                              label-input-group="%" @keyup="updatePersenJual(index)" x-model='item.persen' ::disabled="(item.mode !== 'PERSEN')" />
                                            </div>
                                            <div class="flex flex-col items-center justify-center col-span-1">
                                                <span class="label"></span>
                                                <span class="mt-4 text-lg">=</span>
                                            </div>
                                            <div class="col-span-11 min-[450px]:col-span-6">
                                                <x-form.input error-name="form.opsi_harga.${index}.harga" label="Harga Jual" input-group="both"
                                                              label-left-input-group="Rp" @keyup="updateHargaJual(index)" x-model='item.harga'
                                                              ::disabled="(item.mode !== 'HARGA')">
                                                    <x-slot:label-right-input-group>
                                                        <span x-text="item.nama_satuan"></span>
                                                    </x-slot:label-right-input-group>
                                                </x-form.input>
                                                <span x-show="item.id_satuan !== id_satuan_utama"
                                                      x-text="'≈ Rp. '+item.harga_satuan_utama +' / '+nama_satuan_utama"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="h-full text-lg btn btn-sm bg-error" @click='removeOpsiHarga(index)'>x</button>
                    </div>
                </div>
            </template>

        </div>
        <div class="col-span-12 mt-1">
            <button type="button" @click="addOpsiHarga" class="w-full mb-2 btn btn-sm disabled:!bg-primary disabled:text-white disabled:opacity-30"
                    :disabled="!harga_beli_default || !id_satuan_utama">
                Tambah Harga Jual Alternatif</button>
        </div>
    </div>
</div>
