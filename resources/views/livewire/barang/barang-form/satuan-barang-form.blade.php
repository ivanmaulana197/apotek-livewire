<div class="col-span-12">
    <div class="grid grid-cols-1 gap-y-2">
        {{-- satuan utama --}}
        <x-form.select-alpine @change="satuanUtamaChange($el)" label="Satuan Utama" name="satuan" :data-option="$satuans" modal-name="satuan" :add-value=true
                              option-value="id" option-label="nama" x-model='id_satuan_utama' error-name="form.id_satuan_utama" />


        <template x-for="(item, index) in opsi_satuan" :key="index">
            <div :class="index % 2 === 0 ? 'bg-slate-200 dark:bg-slate-800 rounded-md' : ''">
                <div class="flex items-center justify-around gap-1">
                    <span x-text="`#${index+1}`"></span>
                    <div class="grid grid-cols-12 gap-2 my-2">
                        {{-- perbandingan satuan --}}
                        <div class="col-span-12 md:col-span-5">
                            <div class="grid grid-cols-2 border border-gray-200 join dark:border-darkborder">
                                {{-- input perbandingan satuan --}}
                                <x-form.input error-name="form.opsi_satuan.${index}.rasio_satuan_opsi" placeholder="Masukan nilai" class="join-item" coba="`${index+1}`"
                                              x-model="item.rasio_satuan_opsi" @keyup="changeRasioSatuanOpsi($el)">
                                </x-form.input>
                                {{-- select perbandingan satuan --}}
                                <x-form.select-alpine error-name="form.opsi_satuan.${index}.id_satuan"  ::name="'satuan_opsi_' + index" main-select="satuan" @change="updatedSatuanOpsi" x-model="item.id_satuan"
                                                      ::placeholder="'Opsi Satuan ke-' + (index + 1)" class="whitespace-nowrap min-w-24 md:min-w-32 join-item">
                                    <x-slot:custom-option></x-slot:custom-option>
                                </x-form.select-alpine>
                            </div>
                        </div>
                        <div class="flex items-center justify-center col-span-1">
                            <span class="text-xl">=</span>
                        </div>
                        {{-- perbandingan dengan satuan utama --}}
                        <div class="col-span-11 md:col-span-6 ">
                            <x-form.input error-name="form.opsi_satuan.${index}.rasio_satuan_utama" placeholder="Masukan nilai" error-name="form.opsi_satuan.*.rasio_satuan_utama" input-group="right"
                                          @keyup="changeRasioSatuanUtama($el)" x-model='item.rasio_satuan_utama'>
                                <x-slot:labelInputGroup>
                                    <span x-text="nama_satuan_utama ?? '-'"></span>
                                </x-slot:labelInputGroup>
                            </x-form.input>
                        </div>
                    </div>
                    <button type="button" class="h-full text-lg btn btn-sm bg-error" @click='removeOpsiSatuan(index)'>x</button>
                </div>
            </div>
        </template>

        <div class="mt-1">
            <button type="button" @click="addOpsiSatuan"
                    class="w-full mb-2 btn btn-sm bg-primary hover:bg-primaryemphasis disabled:bg-primary disabled:text-white disabled:opacity-30">
                Tambah Opsi Satuan
            </button>
        </div>
    </div>
</div>
