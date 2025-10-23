<x-slot:title>Login</x-slot:title>
<div>
    @if (session()->has('error'))
        <div class="p-3 mb-4 text-red-700 bg-red-100 border border-red-200 rounded">
            {{ session('error') }}
        </div>
    @endif
    <form wire:submit='submitLogin'>
        <div class="flex flex-col gap-5 mt-7">
            <div>
                <label class="block mb-2 font-semibold text-dark dark:text-darklink ">Email</label>
                <input type="email" class="rounded-full ps-5 form-control dark:text-white" wire:model='email' placeholder="Email" />
                @error('email')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block mb-2 font-semibold text-dark dark:text-darklink ">Password</label>
                <input type="password" class="rounded-full ps-5 form-control dark:text-white" wire:model='password'
                    placeholder="Password" />
                @error('password')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <div class="flex justify-between my-2">
                    <div>
                        <label class="flex items-center cursor-pointer label ">
                            <input type="checkbox" wire:model='remember'
                                class="w-4 h-4 bg-transparent rounded border-bordergray text-primary dark:border-darkborder dark:checked:bg-primary dark:checked:border-primary focus:ring-0 focus:ring-offset-0"
                                id="hs-checked-checkbox" checked>
                            <span class="label-text ms-2">Remeber
                                this
                                Device</span>
                        </label>
                    </div>
                    {{-- <a href="../main/authentication-forgot-password.html" class="font-semibold text-primary">Forgot
                        Password ?</a> --}}
                </div>
            </div>
            <button type="submit" class="py-3 btn btn-md" wire:loading.remove>
                Sign In
            </button>


            <button class="py-3 btn btn-md flex items-center justify-center" wire:loading wire:loading.attr='disabled'>
                <iconify-icon icon="eos-icons:loading"></iconify-icon>
            </button>
        </div>
    </form>
</div>
