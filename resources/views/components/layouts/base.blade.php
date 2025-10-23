<!DOCTYPE html>
<html lang="en"
    dir="ltr"
    class="light"
    data-boxed-layout="full"
    data-card="shadow"
    data-layout="vertical">

<head>
    @include('partials.head', ['title' => $title])
    @stack('script-head')
    @livewireStyles
</head>
<body class="DEFAULT_THEME bg-lightprimary dark:bg-darkbody">

    <x-sweet-alert />
    {{ $slot }}




    @if (request()->is('kasir*'))
        <style>
            @media (min-width: 1300px) {
                [data-layout="vertical"] body[data-sidebartype="mini-sidebar"] .page-wrapper {
                    margin-left: 0px;
                }
            }

            @media (min-width: 1300px) {
                [data-layout="vertical"] body .page-wrapper {
                    margin-left: 0px;
                }
            }

            @media (min-width: 1024px) {
                #application-sidebar-brand {
                    display: none;
                }
            }
        </style>
    @endif

    {{-- @if (session('alert'))
        <script>
            document.addEventListener('livewire:navigated', () => {
                Swal.fire({
                    title: '{{ session('alert.title') }}',
                    text: '{{ session('alert.message') }}',
                    icon: '{{ session('alert.icon') }}',
                    showConfirmButton: false,
                    timer: 1500,
                });

            },{once:true});
        </script>
    @endif --}}

    @include('partials.navbar.navbar-mobile-menu-canvas')


    @include('partials.customize')
    <script>
        function handleColorTheme(e) {
            localStorage.setItem('data-color-theme', e)
            document.documentElement.setAttribute("data-color-theme", e);
        }
    </script>
    @livewireScriptConfig
    @stack('script-body')
</body>
</html>
