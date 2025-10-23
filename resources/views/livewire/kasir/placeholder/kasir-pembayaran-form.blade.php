<div class="card">
    <div class="card-body p-3 ">
        <div class="card-title bg-warning h-14 py-3 px-2 rounded-lg justify-center animate-pulse">
        </div>

        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <div class="flex animate-pulse justify-between lg:text-lg h-9 rounded-lg border border-bordergray bg-gray-100  dark:bg-gray-700 dark:border-gray-700 mb-2">
                </div>
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-4 h-5 bg-gray-300 rounded-xl animate-pulse"></div>
                    <div class="col-span-12 h-9 bg-gray-300 rounded-xl animate-pulse"></div>
                </div>
            </div>

            @for ($i = 0; $i < 6; $i++)
                <div class="col-span-12 lg:col-span-6">
                    <div class="grid grid-cols-1 gap-3">
                        <div class=" w-[60%] h-5 bg-gray-300 rounded-xl animate-pulse "></div>
                        <div class=" h-10 bg-gray-300 rounded-xl animate-pulse "> </div>
                    </div>
                </div>
            @endfor
            <div class="col-span-12 mt-3 mb-2 h-10 bg-primary rounded-xl animate-pulse">
            </div>
        </div>

    </div>
</div>
