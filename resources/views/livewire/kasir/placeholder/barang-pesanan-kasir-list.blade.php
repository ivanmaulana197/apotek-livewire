<div class="card">
    <div class="card-body">
        <div>
            <div class="h-8 rounded-lg w-24 bg-error hover:bg-erroremphasis animate-pulse"></div>
        </div>
        <x-table nama="barang-pesanan-kasir" height="h-96 max-h-96">
            <x-table.thead>
                @for ($i = 0; $i < 8; $i++)
                    <x-table.th>
                        <x-slot:label>
                            <div class="h-5 bg-gray-300 animate-pulse"></div>
                        </x-slot:label>
                    </x-table.th>
                @endfor

            </x-table.thead>
            <x-table.tbody>
                <tr>
                    @for ($i = 0; $i < 8; $i++)
                    <td class="p-2 text-sm font-semibold text-center text-dark dark:text-white">
                        <div class="h-10 bg-gray-300 animate-pulse"></div>
                    </td>
                    @endfor
                </tr>
            </x-table.tbody>
        </x-table>

    </div>
</div>
