<x-layouts.base>
    <x-slot:title>{{ $title ?? 'Auth' }}</x-slot:title>
    <main>
        <!--start the project-->
        <div id="main-wrapper" class="flex ">
            <div class="flex justify-center w-full h-full min-h-screen overflow-hidden">
                <div class="container relative flex items-center justify-center rounded-none auth-login container-xl px-7">
                    <div class="relative z-[1] container">
                        <div class="card auth-login-wrapper ">
                            <div class="card-body">
                                <div class="flex items-center justify-center">
                                    <x-partials.logo></x-partials.logo>
                                </div>
                                <div class="items-center justify-around py-8 md:flex">
                                    <div class="justify-center hidden lg:w-5/12 lg:flex">
                                        <img src="{{ asset('/assets/images/backgrounds/login-security.png') }}" alt="security" class="w-auto h-fit">
                                    </div>
                                    <div class="w-full lg:w-5/12 ">
                                        <h2 class="mb-3 text-3xl font-bold">Welcome to Apotek Rajawali</h2>
                                        <p class="text-base font-medium text-dark dark:text-darklink mb-7">Your Dashboard</p>
                                        {{ $slot }}
                                    </div>
                                </div>
                            </div>
                            <!----Bottom Shape---->
                            <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-error -z-[1]"></div>
                            <!----Top Shape---->
                            <img src="{{ asset('/assets/images/shaps/shap-login.png') }}" alt="shape" class="absolute h-80 w-80 -right-20 -z-[1] -top-24 ">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end of project-->

    </main>
</x-layouts.base>
