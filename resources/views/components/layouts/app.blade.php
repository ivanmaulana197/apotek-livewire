<x-layouts.base>
    <x-slot:title>{{ $title ?? 'Sistem' }}</x-slot:title>
    <main class="min-h-screen">
        <!--start the project-->
        <div id="main-wrapper" class="flex {{ request()->is('kasir*') ? 'py-2 px-3' : 'p-5' }}">
            {{-- @if (!request()->is('kasir*')) --}}
            @include('partials.sidebar.sidebar')
            {{-- @endif --}}
            <div class="w-full page-wrapper" role="main">

                <!-- Main Content -->
                <main class="h-full ">
                    <div class="container py-1 pt-0 full-container xl:ps-2 pe-0 remove-ps max-w-full">

                        <!--  Header Start -->
                        @include('partials.navbar.navbar')
                        <!--  Header End -->

                        <!-- Horizontal Sidebar Menu -->
                        {{-- @include('layouts.partials.sidebar.sidebar-horizontal') --}}
                        <!-- Horizontal Sidebar Menu End -->

                        {{ $slot }}
                        <!-------------->

                    </div>
                </main>
                <!-- Main Content End -->

            </div>
        </div>
        <!--end of project-->
    </main>
</x-layouts.base>
