<div class="hs-dropdown  xl:[--strategy:absolute] [--adaptive:none] md:[--trigger:hover] sm:relative ms-3">
    <a id="hs-dropdown-hover-event-profile" class="relative align-middle rounded-full cursor-pointer hs-dropdown-toggle">
        <div class="flex gap-4">
            <div class="relative">
                <img class="object-cover rounded-full w-11 h-11" src="{{ asset('/') }}assets/images/profile/user-1.jpg" alt="" aria-hidden="true">
                <span class="h-3.5 w-3.5 rounded-full bg-success block absolute top-0 -end-1 border-2 border-white dark:border-dark"></span>

            </div>
            <div class="hidden sm:block">
                <h6 class="mb-1 text-base font-bold text-dark dark:text-white profile-name">
                    {{ auth()->user()->name }}
                </h6>
                <p class="mb-0 text-sm leading-tight text-link dark:text-darklink">
                    {{ auth()->user()->formatRoleList() ?? '-' }}
                </p>
            </div>
        </div>
    </a>
    <div class="card hs-dropdown-menu absolute z-10 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 mt-2 min-w-max top-auto right-0 rtl:right-auto rtl:left-0 w-full sm:w-[385px] hidden z- border-none "
         aria-labelledby="hs-dropdown-hover-event-profile">
        <div class="card-body p-7">
            <div class="flex items-center justify-between pb-5">
                <h3 class="mb-0 text-lg font-semibold text-dark dark:text-white">
                    User
                    Profile</h3>
            </div>
            <div class="message-body max-h-[450px]" data-simplebar="">
                <div class="">
                    <div class="flex items-center gap-6 pb-5 border-b dark:border-darkborder">
                        <img src="{{ asset('/') }}assets/images/profile/user-1.jpg" class="h-[90px] w-[90px] rounded-full object-cover" alt="profile">
                        <div class="">
                            <h5 class="card-title">
                                {{ auth()->user()->name }}
                            </h5>
                            <span class="card-subtitle">{{ auth()->user()->formatRoleList() ?? '-' }}</span>
                            <p class="flex items-center mt-1 mb-0 ">
                                <span class="iconify solar--mailbox-line-duotone text-base me-1"></span>
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                    </div>

                    <ul class="mt-3 flex flex-col gap-3.5">
                        <li>
                            <a href="main/page-user-profile.html" class="relative flex items-center gap-5 p-2 rounded-sm bg-hover group">
                                <span
                                      class="flex items-center justify-center p-2 rounded-sm bg-lightinfo dark:bg-darkinfo hover:bg-info group text-info hover:text-white">
                                    <span class="iconify solar--wallet-2-line-duotone text-2xl "></span>
                                </span>

                                <div>
                                    <h6 class="mb-1 text-base font-medium leading-tight group-hover:text-primary">
                                        My Profile
                                    </h6>
                                    <p class="text-sm font-normal leading-tight">
                                        Account settings</p>
                                </div>
                            </a>
                        </li>
                        {{-- <li>
                            <a href="main/app-email.html" class="relative flex items-center gap-5 p-2 rounded-sm bg-hover group">
                                <span
                                      class="flex items-center justify-center p-2 rounded-sm bg-lightsuccess dark:bg-darksuccess hover:bg-success group text-success hover:text-white">
                                    <span class="iconify solar--shield-minimalistic-line-duotone text-2xl"></span>
                                </span>

                                <div>
                                    <h6 class="mb-1 text-base font-medium leading-tight group-hover:text-primary">
                                        My Inbox
                                    </h6>
                                    <p class="text-sm font-normal leading-tight">
                                        Messages & Emails</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="main/app-kanban.html" class="relative flex items-center gap-5 p-2 rounded-sm bg-hover group">
                                <span
                                      class="flex items-center justify-center p-2 rounded-sm bg-lighterror dark:bg-darkerror hover:bg-error group text-error hover:text-white">
                                    <span class="iconify solar--card-2-line-duotone text-2xl"></span>
                                </span>

                                <div>
                                    <h6 class="mb-1 text-base font-medium leading-tight group-hover:text-primary">
                                        My Task
                                    </h6>
                                    <p class="text-sm font-normal leading-tight">
                                        To-do and Daily Tasks</p>
                                </div>
                            </a>
                        </li> --}}
                    </ul>
                </div>
            </div>
            <div class="mt-5">
                <livewire:auth.logout-button tipe="navbar" />
            </div>
        </div>
    </div>
</div>
