 <!-- Menu Canvas-->
 <div id="navbar-offcanvas-example-menu"
 class="lg:hidden bg-white hs-overlay  dark:bg-dark hs-overlay-open:translate-x-0  translate-x-full rtl:hs-overlay-open:translate-x-0  rtl:-translate-x-full  fixed top-0 right-0 rtl:left-0 rtl:right-auto transition-all duration-300 transform h-full max-w-xs  w-full z-[60] hidden"
 tabindex="-1" data-hs-overlay-close-on-resize="">
 <div class="items-center gap-2 lg:flex ">
     <div class="flex p-5 lg:hidden lg:p-0">
        <x-partials.logo></x-partials.logo>

     </div>
     <div class="items-center gap-2 p-5 lg:p-0 lg:flex" data-simplebar=""
         style="height: calc(100vh - 100px)">
         <button type="button" class="flex justify-between w-full py-2 hs-collapse-toggle"
             id="hs-basic-collapse" data-hs-collapse="#hs-basic-collapse-apps">
             <div class="flex gap-2 text-dark dark:text-darklink">
                 <iconify-icon icon="solar:list-bold-duotone" class="text-2xl"></iconify-icon>
                 <span class="font-medium hide-menu text-fs_15">Apps</span>
             </div>
             <i
                 class="text-xl transition-all ti ti-chevron-right hs-collapse-open:rotate-180 text-darklink"></i>
         </button>
         <div id="hs-basic-collapse-apps"
             class="hs-collapse hidden w-full overflow-hidden transition-[height] duration-300"
             aria-labelledby="hs-basic-collapse">
             <div class="grid grid-cols-12 gap-6">
                 <div class="flex items-stretch col-span-12 px-0 pb-0 lg:col-span-8 lg:px-8 lg:pr-0">
                     <div class="grid w-full grid-cols-12 lg:gap-3">
                         <div class="col-span-12 lg:col-span-6 flex items-stretch pb-2.5">
                             <ul class="flex flex-col gap-5">
                                 <li>
                                     <a href="main/app-chat.html"
                                         class="relative flex items-center gap-3 group ">
                                         <span
                                             class="flex items-center justify-center rounded-sm bg-lightgray dark:bg-darkprimary h-11 w-11">
                                             <img src="{{asset('/')}}assets/images/svgs/icon-dd-chat.svg" class="w-6 h-6">
                                         </span>
                                         <div class="">
                                             <h6 class="mb-1 text-fs_15 group-hover:text-primary ">
                                                 Chat Application
                                             </h6>
                                             <p
                                                 class="text-xs font-normal text-dark dark:text-darklink opacity-90">
                                                 New messages arrived</p>
                                         </div>

                                     </a>
                                 </li>

                             </ul>
                         </div>
                         <div class="flex items-stretch col-span-12 lg:col-span-6">
                             <ul class="flex flex-col gap-5">
                                 <li>
                                     <a href="main/eco-shop.html"
                                         class="relative flex items-center gap-3 group ">
                                         <span
                                             class="flex items-center justify-center rounded-sm bg-lightgray dark:bg-darkprimary h-11 w-11">
                                             <img src="{{asset('/')}}assets/images/svgs/icon-dd-cart.svg" class="w-6 h-6">
                                         </span>
                                         <div class="">
                                             <h6 class="mb-1 text-fs_15 group-hover:text-primary ">
                                                 eCommerce App

                                             </h6>
                                             <p class="text-xs font-normal text-link dark:text-darklink">
                                                 learn more information</p>
                                         </div>

                                     </a>
                                 </li>

                             </ul>
                         </div>
                         <div
                             class="items-stretch hidden col-span-12 py-4 pr-8 border-t md:col-span-12 border-border dark:border-darkborder lg:flex">
                             <div class="flex items-center justify-between w-full">
                                 <div class="flex items-center text-dark dark:text-white">
                                     <i class="text-lg ti ti-help text-dark dark:text-darklink"></i>
                                     <a href="main/page-faq.html"
                                         class="ml-2 font-bold text-fs_15 text-dark dark:text-darklink group-hover:text-primary">
                                         Frequently Asked Questions
                                     </a>
                                 </div>
                                 <a href="#"
                                     class="block py-3 text-white rounded-full btn border-primary px-7 hover:bg-primaryemphasis text-fs_15 hover:text-white ">
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
                         <ul class="flex flex-col gap-4 mb-4">
                             <li><a href="main/page-pricing.html"
                                     class="text-sm font-semibold text-link dark:text-darklink hover:text-primary hover:dark:text-primary ">Pricing
                                     Page</a></li>

                         </ul>
                     </div>
                 </div>
             </div>
         </div>
         <a class="py-2 mb-2 text-dark dark:text-darklink flex gap-2.5" href="main/app-chat.html"
             aria-expanded="false">
             <iconify-icon icon="solar:chat-round-unread-line-duotone" class="text-xl"></iconify-icon>
             <span class="font-medium hide-menu">Chat</span>
         </a>
         <a class="py-2 mb-2 text-dark dark:text-darklink flex gap-2.5" href="main/app-calendar.html"
             aria-expanded="false">
             <iconify-icon icon="solar:calendar-add-line-duotone" class="text-xl"></iconify-icon>
             <span class="font-medium hide-menu">Calendar</span>
         </a>
         <a class="py-2 mb-2 text-dark dark:text-darklink flex gap-2.5" href="main/app-email.html"
             aria-expanded="false">
             <iconify-icon icon="solar:mailbox-line-duotone" class="text-xl"></iconify-icon>
             <span class="font-medium hide-menu">Email</span>
         </a>
     </div>
 </div>
</div>
