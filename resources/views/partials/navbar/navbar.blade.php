<header
        class="sticky top-0 inset-x-0 z-[60] flex flex-wrap md:justify-start md:flex-nowrap text-sm py-3 lg:py-0 mb-3 bg-white dark:bg-dark rounded-2xl shadow-md">
    <div class="w-full with-vertical">
        <div class="w-full px-4 mx-auto lg:px-6" aria-label="Global">
            <div class="relative md:flex md:items-center md:justify-between">
                <div class="hs-collapse grow md:block">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center justify-between w-full gap-2 lg:w-auto">

                            <div class="relative">
                                @if (!request()->is('kasir*'))
                                    <a class="items-center justify-center hidden w-10 h-10 text-xl rounded-full cursor-pointer xl:flex icon-hover text-link dark:text-darklink sidebartoggler hover:text-primary hover:bg-lightprimary dark:hover:bg-darkprimary"
                                       id="headerCollapse" href="javascript:void(0)">
                                        <span class="iconify solar--list-bold-duotone text-2xl relative z-[1]"></span>
                                    </a>
                                @endif
                                <!--Mobile Sidebar Toggle -->
                                <div class="sticky inset-x-0 top-0 xl:hidden">
                                    <div class="flex items-center">
                                        <!-- Navigation Toggle -->
                                        {{-- @if (!request()->is('kasir*')) --}}
                                        <a class="flex items-center justify-center w-10 h-10 text-xl rounded-full cursor-pointer icon-hover text-link dark:text-darklink sidebartoggler hover:text-primary hover:bg-lightprimary dark:hover:bg-darkprimary"
                                           data-hs-overlay="#application-sidebar-brand" aria-controls="application-sidebar-brand"
                                           aria-label="Toggle navigation">
                                            <span class="iconify solar--list-bold-duotone text-2xl relative z-[1]"></span>
                                        </a>
                                        {{-- @else
                                            <button type="button"
                                                class="inline-flex items-center justify-center w-10 h-10 p-2 rounded-full text-link dark:text-darklink hover:text-primary hover:bg-lightprimary dark:hover:bg-darkprimary"
                                                data-hs-overlay="#navbar-offcanvas-example-menu"
                                                aria-controls="navbar-offcanvas-example-menu"
                                                aria-label="Toggle navigation">
                                                <i class="text-2xl iconify tabler--align-justified "></i>
                                            </button> --}}
                                        {{-- @endif --}}
                                        <!-- End Navigation Toggle -->
                                    </div>
                                </div>
                                <!-- End Sidebar Toggle -->
                            </div>

                            <div class="flex overflow-hidden lg:hidden md:w-fit">
                                <x-partials.logo></x-partials.logo>

                            </div>

                            <div class="lg:hidden flex flex-row">
                                <x-partials.toggle-theme />
                                {{-- <button type="button"
                                    class="inline-flex items-center justify-center w-10 h-10 p-2 rounded-full hs-collapse-toggle text-link dark:text-darklink hover:text-primary hover:bg-lightprimary dark:hover:bg-darkprimary"
                                    data-hs-collapse="#hs-basic-collapse-heading" aria-label="Toggle navigation">
                                    <i class="text-xl iconify tabler--dots"></i>
                                </button> --}}
                                @include('partials.navbar.items.profile')
                            </div>

                            {{-- left navbar --}}
                            <!-- Menu-->
                            <div id="navbar-offcanvas-example lg:block hidden" {{-- sebelumnya class dikasi w-[270px] --}}
                                 class="hs-overlay hs-overlay-open:translate-x-0 z-[2] -translate-x-full fixed top-0 start-0 transition-all duration-300 transform h-full max-w-xs bg-white dark:bg-dark  basis-full grow sm:order-1 lg:static lg:block lg:h-auto sm:max-w-none lg:border-r-transparent lg:transition-none lg:translate-x-0  lg:basis-auto hidden "
                                 tabindex="-1" data-hs-overlay-close-on-resize="">
                                <div class="items-center gap-2 lg:flex ">
                                    <div class="flex p-5 lg:hidden lg:p-0">
                                        <x-partials.logo></x-partials.logo>

                                    </div>
                                    <div class="items-center gap-2 p-5 lg:py-4 lg:px-0 lg:flex">
                                        {{-- <div class="items-center lg:flex">
                                            <div
                                                 class="hs-dropdown h-[70px] flex items-center  [--strategy:static] lg:[--strategy:absolute] [--adaptive:none] sm:[--trigger:hover]  relative group/menu">
                                                <button type="button"
                                                        class="flex items-center w-full gap-2 text-base lg:w-auto text-link dark:text-darklink hover:text-primary dark:hover:text-primary group-hover/menu:text-primary">
                                                    <i class="text-xl iconify tabler--api-app lg:hidden lg:text-sm"></i>Apps
                                                    <i class="text-lg iconify tabler--chevron-down ms-auto lg:text-sm"></i>
                                                </button>

                                                <div
                                                     class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] sm:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 left-0 hidden z-10 sm:mt-3 top-full start-0 min-w-[15rem] lg:w-[900px] before:absolute lg:bg-white bg-transparent dark:bg-dark lg:shadow-md shadow-none">
                                                    <div class="grid grid-cols-12">
                                                        <div class="flex items-stretch col-span-12 px-0 py-8 pb-0 lg:col-span-8 lg:px-8 lg:pr-0">
                                                            <div class="grid w-full grid-cols-12 lg:gap-3">
                                                                <div class="col-span-12 lg:col-span-6 flex items-stretch pb-2.5">
                                                                    <ul class="flex flex-col gap-5">
                                                                        <li>
                                                                            <a href="main/app-chat.html" class="relative flex items-center gap-3 group ">
                                                                                <span
                                                                                      class="flex items-center justify-center rounded-sm bg-lightgray dark:bg-darkprimary h-11 w-11">
                                                                                    <img src="{{ asset('/assets/images/svgs/icon-dd-chat.svg') }}"
                                                                                         class="w-6 h-6">
                                                                                </span>
                                                                                <div class="">
                                                                                    <h6 class="mb-1 text-fs_15 group-hover:text-primary ">
                                                                                        Chat Application
                                                                                    </h6>
                                                                                    <p class="text-xs font-normal text-dark dark:text-darklink opacity-90">
                                                                                        New messages arrived
                                                                                    </p>
                                                                                </div>

                                                                            </a>
                                                                        </li>

                                                                    </ul>
                                                                </div>
                                                                <div class="flex items-stretch col-span-12 lg:col-span-6">
                                                                    <ul class="flex flex-col gap-5">
                                                                        <li>
                                                                            <a href="main/eco-shop.html" class="relative flex items-center gap-3 group ">
                                                                                <span
                                                                                      class="flex items-center justify-center rounded-sm bg-lightgray dark:bg-darkprimary h-11 w-11">
                                                                                    <img src="{{ asset('/') }}assets/images/svgs/icon-dd-cart.svg"
                                                                                         class="w-6 h-6">
                                                                                </span>
                                                                                <div class="">
                                                                                    <h6 class="mb-1 text-fs_15 group-hover:text-primary ">
                                                                                        eCommerce App
                                                                                    </h6>
                                                                                    <p class="text-xs font-normal text-link dark:text-darklink">
                                                                                        learn more information
                                                                                    </p>
                                                                                </div>

                                                                            </a>
                                                                        </li>

                                                                    </ul>
                                                                </div>
                                                                <div
                                                                     class="items-stretch hidden col-span-12 py-4 pr-8 border-t md:col-span-12 border-border dark:border-darkborder lg:flex">
                                                                    <div class="flex items-center justify-between w-full">
                                                                        <div class="flex items-center text-dark dark:text-white group ">
                                                                            <i
                                                                               class="text-lg iconify tabler--help text-dark dark:text-darklink group-hover:text-primary"></i>
                                                                            <a href="main/page-faq.html"
                                                                               class="ml-2 font-bold text-fs_15 text-dark dark:text-darklink group-hover:text-primary">
                                                                                Frequently Asked Questions
                                                                            </a>
                                                                        </div>
                                                                        <a href="#" class="px-8 btn">
                                                                            Check
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex col-span-12 lg:col-span-4 items-strech">
                                                            <div class="lg:p-3 lg:border-s border-border dark:border-darkborder">
                                                                <h5 class="mb-4 text-lg font-semibold text-dark dark:text-white">
                                                                    Quick Links</h5>
                                                                <ul class="flex flex-col gap-4">
                                                                    <li>
                                                                        <a href="main/page-pricing.html"
                                                                           class="text-sm font-semibold text-link dark:text-darklink hover:text-primary hover:dark:text-primary ">Pricing
                                                                            Page</a>
                                                                    </li>

                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="lg:flex">
                                            <a href="{{ route('home') }}" wire:navigate
                                               class="flex items-center w-full gap-3 px-2 py-2 rounded-md lg:w-auto lg:py-1 lg:px-2 lg:text-base text-md lg:gap-0 header-link text-link dark:text-darklink hover:text-primary dark:hover:text-primary">
                                                <span class="iconify solar--screencast-2-line-duotone text-xl me-1"></span>
                                                Home
                                            </a>
                                        </div>

                                        <div>
                                            <a href="{{ route('transaksi') }}" wire:navigate
                                               class="flex items-center w-full gap-3 px-2 py-2 rounded-md lg:w-auto lg:py-1 lg:px-2 lg:text-base text-md lg:gap-0 header-link text-link dark:text-darklink hover:text-primary dark:hover:text-primary">
                                                <span class="iconify f7--list-bullet-below-rectangle text-xl me-1"></span>
                                                Transaksi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- right navbar --}}
                        <div class="items-center hidden gap-2 icon-nav lg:flex">
                            <x-partials.toggle-theme />

                            <!-- Profile DD -->
                            @include('partials.navbar.items.profile')
                        </div>
                    </div>
                </div>
            </div>
            {{-- @include('layouts.partials.navbar.navbar-mobile') --}}

        </div>
    </div>
    {{-- @include('layouts.partials.navbar.navbar-horizontal') --}}
</header>
