<!------- Customizer button--------->
{{-- <button type="button"
        class="fixed z-10 flex items-center justify-center w-10 h-10 overflow-hidden rounded-full btn sm:h-14 sm:w-14 sm:bottom-8 bottom-5 right-8 rtl:left-8 rtl:right-auto"
        data-hs-overlay="#hs-overlay-right">
    <span class="text-lg text-white iconify tabler--settings sm:text-2xl"></span>
</button> --}}
<button type="button"
        class="fixed z-10 flex flex-col items-center justify-center overflow-hidden rounded-l-2xl rtl:rounded-r-2xl btn h-fit px-1 py-3 top-1/2 right-0 rtl:left-0 rtl:right-auto "
        data-hs-overlay="#hs-overlay-right">
    <span class="text-lg text-white iconify tabler--settings sm:text-2xl"></span>
    <div class="relative w-[30px] h-[60px] flex items-center justify-center">
        <span class="absolute block transform rotate-90 origin-center text-center leading-[40px]">
            CUSTOM
        </span>
    </div>

</button>

<!------- Customizer Options--------->
<div id="hs-overlay-right"
     class="customizer  rounded-none hs-overlay bg-white dark:bg-dark hs-overlay-open:translate-x-0  translate-x-full rtl:hs-overlay-open:translate-x-0  rtl:-translate-x-full  fixed top-0 right-0 rtl:left-0 rtl:right-auto transition-all duration-300 transform h-full max-w-xs  w-full z-[60] hidden "
     tabindex="-1">
    <div class="flex items-center justify-between px-6 py-3 border-b border-border dark:border-darkborder ">
        <h3 class="text-lg font-semibold text-dark dark:text-white">
            Settings
        </h3>
        <button type="button"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-sm text-gray-500 rounded-md hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 focus:ring-offset-white "
                data-hs-overlay="#hs-overlay-right">
            <span class="sr-only">Close modal</span>
            <i class="text-xl iconify tabler--x text-dark dark:text-white"></i>
        </button>
    </div>

    <!-------Light/Dark Theme--------->
    <div class="px-6 py-6" data-simplebar="" style="height: calc(100vh - 80px)">
        <h6 class="mb-2 font-semibold text-dark dark:text-white">Theme</h6>
        <div class="flex flex-row gap-3 mb-8 customizer-box" role="group">
            <input type="radio" class=" btn-check" name="theme-layout" autocomplete="off" />
            <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-primary hs-dark-mode-active:text-darklink"
                   for="light-layout" data-hs-theme-click-value="light"><i class="text-2xl icon iconify tabler--sun me-2 "></i> Light</label>
            <input type="radio" class="btn-check" name="theme-layout" autocomplete="off" />
            <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link hs-dark-mode-active:text-primary"
                   for="dark-layout" data-hs-theme-click-value="dark"><i class="text-2xl icon iconify tabler--moon me-2 "></i>
                Dark</label>
        </div>

        <!-------Theme Direction--------->
        <h6 class="mb-2 font-semibold text-dark dark:text-white">Theme Direction</h6>
        <div class="flex flex-row gap-3 mb-8 customizer-box" role="group">
            <input type="radio" class="btn-check" name="direction-l" id="ltr-layout" autocomplete="off" />
            <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                   for="ltr-layout"><i class="text-2xl icon iconify tabler--text-direction-ltr me-2"></i>LTR</label>
            <input type="radio" class="btn-check" name="direction-l" id="rtl-layout" autocomplete="off" />
            <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                   for="rtl-layout">
                <i class="text-2xl icon iconify tabler--text-direction-rtl me-2"></i>RTL</label>
        </div>

        <!-------Theme Colors--------->
        <h6 class="mb-2 font-semibold text-dark dark:text-white">Theme Colors</h6>
        <div class="flex flex-row flex-wrap gap-3 mb-8 customizer-box color-pallete" role="group">
            <input type="radio" class="btn-check" name="color-theme-layout" id="Blue_Theme" autocomplete="off" />
            <label class="flex items-center justify-center px-5 py-4 border rounded-md cursor-pointer hs-tooltip btn border-border dark:border-darkborder hs-tooltip-toggle"
                   onclick="handleColorTheme('Blue_Theme')" for="Blue_Theme" data-bs-title="BLUE_THEME">
                <div class="flex items-center justify-center rounded-full color-box skin-1">
                    <i class="flex text-white iconify tabler--check icon fs-5"></i>
                </div>
                <span class="tooltip hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible" role="tooltip">
                    Blue_Theme
                </span>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="Aqua_Theme" autocomplete="off" />
            <label class="flex items-center justify-center px-5 py-4 border rounded-md cursor-pointer hs-tooltip hs-tooltip-toggle btn border-border dark:border-darkborder"
                   onclick="handleColorTheme('Aqua_Theme')" for="Aqua_Theme" data-bs-title="AQUA_THEME">
                <div class="flex items-center justify-center rounded-full color-box skin-2">
                    <i class="flex text-white iconify tabler--check icon fs-5"></i>
                </div>
                <span class="tooltip hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible" role="tooltip">
                    Aqua_Theme
                </span>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="Purple_Theme" autocomplete="off" />
            <label class="flex items-center justify-center px-5 py-4 border rounded-md cursor-pointer hs-tooltip hs-tooltip-toggle btn border-border dark:border-darkborder"
                   onclick="handleColorTheme('Purple_Theme')" for="Purple_Theme" data-bs-title="PURPLE_THEME">
                <div class="flex items-center justify-center rounded-full color-box skin-3">
                    <i class="flex text-white iconify tabler--check icon fs-5"></i>
                </div>
                <span class="tooltip hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible" role="tooltip">
                    Purple_Theme
                </span>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="Green_Theme" autocomplete="off" />
            <label class="flex items-center justify-center px-5 py-4 border rounded-md cursor-pointer hs-tooltip hs-tooltip-toggle btn border-border dark:border-darkborder"
                   onclick="handleColorTheme('Green_Theme')" for="Green_Theme" data-bs-title="GREEN_THEME">
                <div class="flex items-center justify-center rounded-full color-box skin-4">
                    <i class="flex text-white iconify tabler--check icon fs-5"></i>
                </div>
                <span class="tooltip hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible" role="tooltip">
                    Green_Theme
                </span>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="Cyan_Theme" autocomplete="off" />
            <label class="flex items-center justify-center px-5 py-4 border rounded-md cursor-pointer hs-tooltip hs-tooltip-toggle btn border-border dark:border-darkborder"
                   onclick="handleColorTheme('Cyan_Theme')" for="Cyan_Theme" data-bs-title="CYAN_THEME">
                <div class="flex items-center justify-center rounded-full color-box skin-5">
                    <i class="flex text-white iconify tabler--check icon fs-5"></i>
                </div>
                <span class="tooltip hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible" role="tooltip">
                    Cyan_Theme
                </span>
            </label>

            <input type="radio" class="btn-check" name="color-theme-layout" id="Orange_Theme" autocomplete="off" />
            <label class="flex items-center justify-center px-5 py-4 border rounded-md cursor-pointer hs-tooltip hs-tooltip-toggle btn border-border dark:border-darkborder"
                   onclick="handleColorTheme('Orange_Theme')" for="Orange_Theme" data-bs-title="ORANGE_THEME">
                <div class="flex items-center justify-center rounded-full color-box skin-6">
                    <i class="flex text-white iconify tabler--check icon fs-5"></i>
                </div>
                <span class="tooltip hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible" role="tooltip">
                    Orange_Theme
                </span>
            </label>
        </div>

        <!-- Layput Options  -->
        {{-- <h6 class="mb-2 font-semibold text-dark dark:text-white">Layout Type</h6>
    <div class="flex flex-row gap-3 mb-8 customizer-box" role="group">
        <div>
            <input type="radio" class="btn-check" name="page-layout" id="vertical-layout"
                autocomplete="off" />
            <label
                class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                for="vertical-layout">
                <i class="text-2xl icon iconify tabler--layout-sidebar-right me-2"></i> Vertical</label>
        </div>
        <div>
            <input type="radio" class="btn-check" name="page-layout" id="horizontal-layout"
                autocomplete="off" />
            <label
                class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                for="horizontal-layout">
                <i class="text-2xl icon iconify tabler--layout-navbar me-2"></i> Horizontal</label>
        </div>
    </div> --}}

        <!-- Container Options  -->
        <h6 class="mb-2 font-semibold text-dark dark:text-white">Container Option</h6>
        <div class="flex flex-row gap-3 mb-8 customizer-box" role="group">
            <input type="radio" class="btn-check" name="layout" id="boxed-layout" autocomplete="off" />
            <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                   for="boxed-layout">
                <i class="text-2xl icon iconify tabler--layout-distribute-vertical me-2"></i>
                Boxed</label>

            <input type="radio" class="btn-check" name="layout" id="full-layout" autocomplete="off" />
            <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                   for="full-layout">
                <i class="text-2xl icon iconify tabler--layout-distribute-horizontal me-2"></i> Full</label>
        </div>

        <!-- Sidebar Type Options  -->
        <h6 class="mb-2 font-semibold text-dark dark:text-white">Sidebar Type</h6>
        <div class="flex flex-row gap-3 mb-8 customizer-box" role="group">
            <a href="javascript:void(0)" class="fullsidebar">
                <input type="radio" class="btn-check" name="sidebar-type" id="full-sidebar" autocomplete="off" />
                <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                       for="full-sidebar"><i class="text-2xl icon iconify tabler--layout-sidebar-right me-2"></i>
                    Full</label>
            </a>
            <div>
                <input type="radio" class="btn-check " name="sidebar-type" id="mini-sidebar" autocomplete="off" />
                <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                       for="mini-sidebar">
                    <i class="iconify solar--siderbar-outline icon fs-7 me-2"></i>Collapse</label>
            </div>
        </div>

        <!-- Border-sahdow Card Options  -->
        <h6 class="mb-2 font-semibold text-dark dark:text-white">Card With</h6>
        <div class="flex flex-row gap-3 mb-8 customizer-box" role="group">
            <input type="radio" class="btn-check" name="card-layout" id="card-with-border" autocomplete="off" />
            <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                   for="card-with-border"><i class="text-2xl icon iconify tabler--border-outer me-2"></i>Border</label>

            <input type="radio" class="btn-check" name="card-layout" id="card-without-border" autocomplete="off" />
            <label class="h-full px-5 py-3 border rounded-md cursor-pointer btn border-border dark:border-darkborder text-link dark:text-darklink"
                   for="card-without-border">
                <i class="text-2xl icon iconify tabler--border-none me-2"></i>Shadow</label>
        </div>

    </div>
</div>
