<x-layouts.cetak class=" text-gray-900">
    <x-slot:title>{{ $title ?? 'Sistem' }}</x-slot:title>
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-2xl p-8">
        <div class="border-b-2 border-gray-800 pb-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    {{-- nanti dipindah di livewire component agar mendapatkan datanya --}}
                    <h1 class="text-3xl font-bold text-gray-800">Apotek Rajawali</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Jl. Rajawali, Bledanah, Karang Dalem, Kec. Sampang, Kabupaten Sampang
                    </p>
                    {{-- <p class="text-sm text-gray-600">
                        Telp: (021) 1234-5678 | Email: info@tokosejahtera.com
                    </p> --}}
                </div>
                <div class="mt-6 text-center no-print">
                    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition">
                        Cetak Laporan
                    </button>
                </div>
            </div>
        </div>

        {{ $slot }}

        <div class="mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-600">
            <p>Dicetak pada: <span id="waktuFooter"></span></p>
            <p class="mt-1">Toko Sejahtera - Laporan ini dibuat secara otomatis</p>
        </div>

        <!-- Print Button -->

    </div>

    <script>
        // Set tanggal dan waktu
        const now = new Date();
        const options = {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "numeric",
            minute: "numeric",
            second: "numeric",
        };
        const tanggal = now.toLocaleDateString("id-ID", options);
        document.getElementById("waktuFooter").textContent = tanggal;
    </script>
</x-layouts.cetak>
