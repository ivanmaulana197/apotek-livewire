<x-slot:title>Home</x-slot:title>

{{-- <div wire:poll.10s='fetchData'> --}}
<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-5 mt-5">
        @can('home.info-pendapatan-harian')
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-900 dark:text-white font-bold">Pendapatan Hari Ini</p>
                            <h3 class="text-2xl font-bold">Rp @nominal($pendapatan ?? 0)</h3>
                            @if ($persentase_pendapatan > 0)
                                <p class="text-green-500 text-sm mt-2 flex items-center ">
                                    <i class="iconify stash--chart-trend-up-duotone text-2xl font-bold "></i>
                                    {{ number_format($persentase_pendapatan, 2) }}% dari Avg. bulan ini
                                </p>
                            @elseif($persentase_pendapatan < 0)
                                <p class="text-red-500 text-sm mt-2 flex items-center ">
                                    <i class="iconify stash--chart-trend-down-duotone text-2xl font-bold "></i>
                                    {{ number_format(abs($persentase_pendapatan), 2) }}% dari Avg. bulan ini
                                </p>
                            @else
                                <p class="text-gray-500 text-sm mt-2 flex items-center ">
                                    {{-- <i class="iconify stash--chart-trend-flat-duotone text-2xl font-bold "></i> --}}
                                    {{ number_format($persentase_pendapatan, 2) }}% dari Avg. bulan ini
                                </p>
                            @endif

                        </div>
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="iconify solar--hand-money-linear text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        @can('home.info-total-transaksi')
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-900 dark:text-white font-bold">Total Transaksi</p>
                            <h3 class="text-2xl font-bold">@nominal($total_transaksi ?? 0)</h3>
                            @if ($persentase_transaksi > 0)
                                <p class="text-green-500 text-sm mt-2 flex items-center ">
                                    <i class="iconify stash--chart-trend-up-duotone text-2xl font-bold "></i>
                                    {{ number_format($persentase_transaksi, 2) }}% dari Avg. bulan ini
                                </p>
                            @elseif($persentase_transaksi < 0)
                                <p class="text-red-500 text-sm mt-2 flex items-center ">
                                    <i class="iconify stash--chart-trend-down-duotone text-2xl font-bold "></i>
                                    {{ number_format(abs($persentase_transaksi), 2) }}% dari Avg. bulan ini
                                </p>
                            @else
                                <p class="text-gray-500 text-sm mt-2 flex items-center ">
                                    {{-- <i class="iconify stash--chart-trend-flat-duotone text-2xl font-bold "></i> --}}
                                    {{ number_format($persentase_transaksi, 2) }}% dari Avg. bulan ini
                                </p>
                            @endif

                        </div>
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="iconify solar--cart-3-bold-duotone text-2xl font-bold "></i>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        @can('home.info-barang-habis')
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-900 dark:text-white font-bold">Obat Hampir Habis</p>
                            <h3 class="text-2xl font-bold">@nominal($obat_hampir_habis ?? 0)</h3>
                            @if ($obat_hampir_habis > 0)
                                <a href="{{ route('info.barang-habis') }}" wire:navigate class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                    <i class="iconify fluent--warning-32-regular font-bold "></i> Perlu restock
                                </a>
                            @else
                                <p class="text-green-500 text-sm mt-2 flex items-center gap-1">
                                    <i class="iconify solar--check-read-outline font-bold text-lg"></i> Stok cukup
                                </p>
                            @endif
                        </div>
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="iconify streamline-pixel--health-drugs-pill text-2xl font-bold "></i>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        @can('home.info-barang-expired')
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-900 dark:text-white font-bold">Barang Hampir Expired</p>
                            <h3 class="text-2xl font-bold">@nominal($barang_kadaluarsa ?? 0)</h3>

                            @if ($barang_kadaluarsa > 0)
                                <a href="{{ route('info.barang-expired') }}" wire:navigate class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                    <i class="iconify fluent--warning-32-regular font-bold "></i> Perlu penanganan
                                </a>
                            @endif
                            <p class="text-gray-500 dark:text-gray-300 text-xs mt-2 flex items-center gap-1">
                                * Range hingga 3 Bulan Kedepan
                            </p>

                        </div>
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="iconify mdi--archive-clock text-2xl font-bold "></i>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    @can('home.info-transaksi')
        <livewire:transaksi.transaksi-card-info />
    @endcan

    @canany(['home.grafik-pendapatan', 'home.grafik-transaksi'])
        <div class="grid grid-cols-1 gap-6 mb-5" x-data="dashboard">
            <div class="card border-gray-100">
                <div class="card-body">
                    <div class="card-title grid grid-cols-1 md:grid-cols-2">
                        <h3 class="font-semibold">Pendapatan Terakhir</h3>
                        <div class="flex justify-end gap-2">
                            <ul class="flex flex-col sm:flex-row">
                                @can('home.grafik-pendapatan')
                                    <li
                                        class="inline-flex items-center gap-x-2 py-2 px-4 text-sm font-medium bg-white dark:bg-darkprimary border border-light-dark text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-es-lg sm:last:rounded-es-none sm:last:rounded-se-lg  dark:text-white">
                                        <div class="relative flex items-start w-full">
                                            <div class="flex items-center h-5">
                                                <input id="pendapatan-radio" name="view-chart" type="radio" wire:model.live='tipe_chart' value="PENDAPATAN"
                                                       class="border-bordergray bg-transparent dark:border-darkborder text-primary rounded-full disabled:opacity-50  dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-gray-800"
                                                       checked>
                                            </div>
                                            <label for="pendapatan-radio" class="ms-3 block w-full text-sm text-gray-600 dark:text-gray-300">
                                                Pendapatan
                                            </label>
                                        </div>
                                    </li>
                                @endcan
                                @can('home.grafik-transaksi')
                                    <li
                                        class="inline-flex items-center gap-x-2 py-2 px-4 text-sm font-medium bg-white dark:bg-darkprimary border border-light-dark text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-es-lg sm:last:rounded-es-none sm:last:rounded-se-lg  dark:text-white">
                                        <div class="relative flex items-start w-full">
                                            <div class="flex items-center h-5">
                                                <input id="transaksi-radio" name="view-chart" type="radio" wire:model.live='tipe_chart' value="TRANSAKSI"
                                                       class="border-bordergray bg-transparent dark:border-darkborder text-primary rounded-full disabled:opacity-50  dark:checked:bg-primary dark:checked:border-primary dark:focus:ring-offset-gray-800">
                                            </div>
                                            <label for="transaksi-radio" class="ms-3 block w-full text-sm text-gray-600 dark:text-gray-300">
                                                Transaksi
                                            </label>
                                        </div>
                                    </li>
                                @endcan
                            </ul>
                            <div class="w-auto max-w-fit">
                                <x-form.input-date nama="chartDate" model="chartDate" :select_date='$chartDate' class="w-auto max-w-fit"></x-form.input-date>
                            </div>
                        </div>

                    </div>
                    <div class="h-64 sm:h-80" wire:ignore>
                        <div id="salesChart"></div>
                        <div wire:loading class="absolute inset-0 z-10 bg-gray-700/90 ">
                            <div class="absolute inset-0 space-x-2 flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span>Loading ...</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    @endcanany
</div>

@script
    <script>
        Alpine.data('dashboard', () => ({
            dataChartPendapatan: $wire.entangle('chartPendapatan'),
            init() {
                let tanggal = this.dataChartPendapatan.map(item => item.tanggal);
                let data = this.dataChartPendapatan.map(item => item.data);
                var chart = new ApexCharts(document.getElementById('salesChart'), {
                    chart: {
                        type: 'area',
                        height: '100%',
                    },
                    series: [{
                        name: "Penjualan (Rp)",
                        data: data,
                    }],
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: tanggal,
                        labels: {
                            style: {
                                fontSize: '10px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            },
                            style: {
                                fontSize: '10px'
                            }
                        },
                        min: 0
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        opacity: 0.1
                    },
                    colors: ['#3B82F6'],
                    markers: {

                        strokeColors: '#ffffff',

                        hover: {
                            size: 8
                        }
                    },
                    grid: {
                        borderColor: 'rgba(0, 0, 0, 0.05)',
                        xaxis: {
                            lines: {
                                show: false
                            }
                        }
                    }

                });
                chart.render();

                const observer = new MutationObserver(() => {
                    let isDark = document.documentElement.classList.contains('dark');
                    chart.updateOptions({
                        chart: {
                            foreColor: isDark ? '#E5E7EB' : '#374151'
                        },
                        grid: {
                            borderColor: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)'
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light'
                        }
                    });
                });

                // observe perubahan class di html
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                $wire.on('updateChart', ({
                    data,
                    tipe
                }) => {
                    tanggal = data.map(item => item['tanggal']);
                    data = data.map(item => item['data']);
                    if (tipe == 'PENDAPATAN') {
                        chart.updateOptions({
                            colors: ['#3B82F6'],
                            xaxis: {
                                categories: tanggal
                            },
                            yaxis: {
                                labels: {
                                    formatter: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    },
                                    style: {
                                        fontSize: '10px'
                                    }
                                },
                                min: 0
                            },
                            tooltip: {
                                y: {
                                    formatter: function(value) {
                                        return 'Rp ' + value.toLocaleString();
                                    }
                                }
                            },
                        });
                        chart.updateSeries([{
                            data: data,
                            name: 'Pendapatan',
                        }]);
                    } else if (tipe == 'TRANSAKSI') {
                        chart.updateOptions({
                            colors: ['#F86624'],
                            xaxis: {
                                categories: tanggal
                            },
                            yaxis: {
                                labels: {
                                    formatter: function(value) {
                                        return value + ' Transaksi';
                                    },
                                    style: {
                                        fontSize: '10px'
                                    }
                                },
                                min: 0
                            },
                            tooltip: {
                                y: {
                                    formatter: function(value) {
                                        return value.toLocaleString() + ' Transaksi';
                                    }
                                }
                            },
                        });
                        chart.updateSeries([{
                            data: data,
                            name: 'Transaksi',
                        }]);

                    }



                })
            }
        }))
    </script>
@endscript
