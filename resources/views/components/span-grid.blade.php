@props(['label', 'data', 'textColor'=>''])

<div class="grid grid-cols-12">
     <div class="col-span-12 sm:col-span-2 md:col-span-4 ">
        <x-form.label label="{{ $label }}" />
     </div>
     <div class="col-span-12 sm:col-span-10 md:col-span-8 ">
        <div class="py-2 px-3 pe-6 block w-full shadow-sm text-sm rounded-lg focus:border-primary focus:ring-primary form-control disabled:opacity-50 disabled:pointer-events-none">
           <h3 class="{{ $textColor }} tex-g">{{ $data }}</h3>
        </div>
     </div>
</div>
