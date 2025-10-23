<div class="relative px-4 pt-4 hide-menu">
    <div class="p-6 rounded-md bg-lightprimary dark:bg-darkprimary">
        <div class="">
            <div class="flex items-center">
                <img src="{{ asset('/assets/images/profile/user-1.jpg') }}"
                    class="object-cover rounded-full h-11 w-11" alt="profile" />
                <div class="ml-4 rtl:mr-4 rtl:ml-0">
                    <h5 class="text-lg font-semibold text-dark dark:text-white">Mike</h5>
                    <p class="text-sm font-normal text-link dark:text-darklink ">Admin</p>
                </div>
                <div class="ms-auto ">
                    <livewire:auth.logout-button tipe="sidebar"/>
                </div>

            </div>
        </div>
    </div>
</div>
