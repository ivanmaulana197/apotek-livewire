<aside id="application-sidebar-brand"
       class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full xl:rtl:-translate-x-0 rtl:translate-x-full  left-0 rtl:left-auto xl:rtl:right-5 rtl:right-0 transform hidden xl:block xl:translate-x-0 xl:end-auto xl:bottom-0 fixed top-0 with-vertical  left-sidebar transition-all duration-300 z-[998] flex-shrink-0  w-[270px] border-border dark:border-darkborder bg-white dark:bg-dark xl:top-5 xl:start-3 shadow-md xl:rounded-2xl">
    <!-- Start Vertical Layout Sidebar -->
    <div class="p-3.5 flex justify-center">
        <x-partials.logo></x-partials.logo>
    </div>
    <div class="scroll-sidebar" data-simplebar="">
        <div class="px-4 mt-5 mini-layout" data-te-sidenav-menu-ref>
            <nav class="flex flex-col w-full hs-accordion-group">
                <ul data-te-sidenav-menu-ref id="sidebarnav">

                    <!-----Dashboard------->
                    <x-partials.sidebar.caption>HOME</x-partials.sidebar.caption>

                    {{-- <x-partials.sidebar.item title="Dashboard" icon='solar:screencast-2-line-duotone' href="{{ route('dashboard') }}"
                    color='primary'></x-partials.sidebar.item> --}}
                    @can('home')
                        <x-partials.sidebar.item title="Home" icon='solar--screencast-2-line-duotone' href="{{ route('home') }}"
                                                 color='primary'></x-partials.sidebar.item>
                    @endcan
                    {{-- <x-partials.sidebar.item title="Home Full" icon='solar:chart-line-duotone' href="{{ route('home-full') }}" color='pink'></x-partials.sidebar.item> --}}

                    <!---Apps---->
                    @canany(['kasir', 'faktur', 'promo'])
                        <x-partials.sidebar.caption>Apps</x-partials.sidebar.caption>
                    @endcanany

                    @can('kasir')
                        <x-partials.sidebar.item title="Kasir" icon='hugeicons--cashier' href="{{ route('kasir-cadangan') }}"
                                                 color='purple'></x-partials.sidebar.item>
                    @endcan
                    @can('faktur')
                        <x-partials.sidebar.item name="faktur-masuk" title="Faktur Masuk" icon='solar--chart-line-duotone' href="{{ route('faktur') }}"
                                                 color='red'></x-partials.sidebar.item>
                    @endcan
                    @can('promo')
                        <x-partials.sidebar.item title="Promo" icon='duo-icons--discount' href="{{ route('promo') }}" color='violet'></x-partials.sidebar.item>
                    @endcan

                    @canany(['barang', 'stok-opname', 'transaksi'])
                        <x-partials.sidebar.caption>Data</x-partials.sidebar.caption>
                    @endcanany

                    @can('barang')
                        <x-partials.sidebar.item title="Barang" icon='basil--shopping-basket-outline' href="{{ route('data.barang') }}"
                                                 color='green'></x-partials.sidebar.item>
                    @endcan
                    @can('transaksi')
                        <x-partials.sidebar.item title="Transaksi" icon='f7--list-bullet-below-rectangle' href="{{ route('transaksi') }}"
                                                 color='orange'></x-partials.sidebar.item>
                    @endcan
                    @can('stok-opname')
                        <x-partials.sidebar.item title="Stok Opname" icon='hugeicons--task-done-01' href="{{ route('stok-opname') }}"
                                                 color='emerald'></x-partials.sidebar.item>
                    @endcan

                    @canany(['user', 'supplier', 'rak', 'kategori', 'member', 'profesi', 'satuan', 'dokter', 'role', 'metode-pembayaran'])
                        <x-partials.sidebar.item title="Data Master" icon="simple-icons--databricks" color="blue" :menuLevel="true" name="data-master">
                            @can('user')
                                <x-partials.sidebar.subitem name="data-master/data-user" href="{{ route('data-master.user') }}" color="blue">Data
                                    User</x-partials.sidebar.subitem>
                            @endcan
                            @can('supplier')
                                <x-partials.sidebar.subitem name="data-master/data-supplier" href="{{ route('data-master.supplier') }}" color="blue">Data
                                    Suppliyer</x-partials.sidebar.subitem>
                            @endcan
                            @can('rak')
                                <x-partials.sidebar.subitem name="data-master/data-rak" href="{{ route('data-master.rak') }}" color="blue">Data
                                    Rak</x-partials.sidebar.subitem>
                            @endcan
                            @can('kategori')
                                <x-partials.sidebar.subitem name="data-master/data-kategori" href="{{ route('data-master.kategori') }}" color="blue">Data
                                    Kategori</x-partials.sidebar.subitem>
                            @endcan
                            @can('member')
                                <x-partials.sidebar.subitem name="data-master/data-member" href="{{ route('data-master.member') }}" color="blue">Data
                                    Member</x-partials.sidebar.subitem>
                            @endcan
                            @can('profesi')
                                <x-partials.sidebar.subitem name="data-master/data-profesi" href="{{ route('data-master.profesi') }}" color="blue">Data
                                    Profesi</x-partials.sidebar.subitem>
                            @endcan
                            @can('satuan')
                                <x-partials.sidebar.subitem name="data-master/data-satuan" href="{{ route('data-master.satuan') }}" color="blue">Data
                                    Satuan</x-partials.sidebar.subitem>
                            @endcan
                            @can('dokter')
                                <x-partials.sidebar.subitem name="data-master/data-dokter" href="{{ route('data-master.dokter') }}" color="blue">Data
                                    Dokter</x-partials.sidebar.subitem>
                            @endcan
                            @can('metode-pembayaran')
                                <x-partials.sidebar.subitem name="data-master/data-metode-pembayaran" href="{{ route('data-master.metode-pembayaran') }}"
                                                            color="blue">Data Metode Pembayaran</x-partials.sidebar.subitem>
                            @endcan
                            @can('role')
                                <x-partials.sidebar.subitem name="data-master/data-role" href="{{ route('data-master.role') }}" color="blue">Data
                                    Role</x-partials.sidebar.subitem>
                            @endcan
                        </x-partials.sidebar.item>
                    @endcanany
                    @canany(['info-barang-expired', 'info-barang-habis'])
                        <x-partials.sidebar.item title="Informasi" icon="simple-icons--databricks" color="pink" :menuLevel="true" name="info">
                            @can('info-barang-expired')
                                <x-partials.sidebar.subitem name="info-barang-expired" href="{{ route('info.barang-expired') }}" color="pink">
                                    Info Barang Expired</x-partials.sidebar.subitem>
                            @endcan
                            @can('info-barang-habis')
                                <x-partials.sidebar.subitem name="info-barang-habis" href="{{ route('info.barang-habis') }}" color="pink">
                                    Info Barang Habis</x-partials.sidebar.subitem>
                            @endcan
                        </x-partials.sidebar.item>
                    @endcanany

                    {{-- @canany(['transaksi.cetak-laporan-all', 'transaksi.cetak-laporan-own', 'faktur.cetak-laporan'])
                        @canany(['faktur.cetak-laporan', 'transaksi.filter-transaksi']) --}}
                    @canany(['laporan-transaksi', 'laporan-faktur'])
                        <x-partials.sidebar.item title="Laporan" icon="simple-icons--databricks" color="blue" :menuLevel="true" name="laporan">
                            {{-- @canany(['transaksi.cetak-laporan-all', 'transaksi.cetak-laporan-own']) --}}
                            @can('laporan-transaksi')
                                <x-partials.sidebar.subitem name="laporan/transaksi" href="{{ route('laporan.transaksi') }}" color="blue">
                                    Laporan Transaksi
                                </x-partials.sidebar.subitem>
                            @endcan

                            @can('laporan-faktur')
                                <x-partials.sidebar.subitem name="laporan/faktur" href="{{ route('laporan.faktur') }}" color="blue">
                                    Laporan Faktur
                                </x-partials.sidebar.subitem>
                            @endcan
                        </x-partials.sidebar.item>
                    @endcanany

                    @can('setting')
                        <x-partials.sidebar.caption>Pengaturan</x-partials.sidebar.caption>
                        <x-partials.sidebar.item name="pengaturan-apotek" title="Pengaturan Apotek" icon='hugeicons--computer-settings'
                                                 href="{{ route('setting-apotek') }}" color='purple'></x-partials.sidebar.item>
                    @endcan
                    {{-- <x-partials.sidebar.item name="backup-database" title="Backup Database" icon='hugeicons:computer-settings' href="{{ route('backup') }}" color='purple'></x-partials.sidebar.item> --}}

                </ul>
            </nav>
        </div>
    </div>

    <!-- Bottom User Profile -->
    @include('partials.sidebar.sidebar-bottom')

    <!-- </aside> -->
</aside>
