@props([
    'nama',
    'model',
    'placeholder',
    'singleDatePicker' => false,
    'errorName' => null,
    'select_date' => null,
    'bigRange' => false,
    'ranges' => true,
    'minDate' => false,
])

<div wire:key='{{ $nama }}' x-data="{
    bigRange: {{ json_encode($bigRange) }},
    select_date: {{ json_encode($select_date) }},
    ranges: {{ json_encode($ranges ?? false) }},
    minDateValue: {{ json_encode($minDate) }},
    singleDatePicker: {{ json_encode($singleDatePicker) }},
}" x-init="let minDate = minDateValue ? moment(minDateValue) : false;

if (ranges) {
    if (!bigRange) {
        ranges = {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'This Week': [moment().startOf('week'), moment().endOf('week')],
            'Last Week': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        };
    } else {
        ranges = {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
        };
    }

}

let settingDateRange = {
    showDropdowns: true,
    singleDatePicker: singleDatePicker,
    autoUpdateInput: false,
    alwaysShowCalendars: true,
    ranges: ranges,
    minDate: minDate,
    drops: 'auto',
    locale: {
        cancelLabel: 'Clear'
    },
    'cancelClass': 'bg-red-500',
};
if (select_date) {
    if (singleDatePicker) {
        var date = moment(select_date);
        settingDateRange['startDate'] = date;
        setSingleDate(date);
    } else {
        var start = moment(select_date['start']);
        var end = moment(select_date['end']);
        settingDateRange['startDate'] = start;
        settingDateRange['endDate'] = end;
        setDateRange(start, end);
    }
}

function setDateRange(start, end) {
    $('#{{ $nama }}').val(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
}

function setSingleDate(date) {
    $('#{{ $nama }}').val(date.format('D MMMM YYYY'));
}

function initDatePicker() {
    $('#{{ $nama }}').daterangepicker(settingDateRange).on('apply.daterangepicker', function(ev, picker) {
        if (singleDatePicker) {
            $(this).val(picker.startDate.format('D MMMM YYYY'));


            @this.set('{{ $model }}', picker.startDate.format('YYYY-MM-DD'));
        } else {
            $(this).val(picker.startDate.format('D MMMM YYYY') + ' - ' + picker.endDate.format('D MMMM YYYY'));
            @this.set('{{ $model }}', { start: picker.startDate.format('YYYY-MM-DD'), end: picker.endDate.format('YYYY-MM-DD') });
        }
    }).on('hide.daterangepicker', function(ev, picker) {
        if (singleDatePicker) {
            $(this).val(picker.startDate.format('D MMMM YYYY'));

            @this.set('{{ $model }}', picker.startDate.format('YYYY-MM-DD'));
        } else {
            $(this).val(picker.startDate.format('D MMMM YYYY') + ' - ' + picker.endDate.format('D MMMM YYYY'));
            @this.set('{{ $model }}', { start: picker.startDate.format('YYYY-MM-DD'), end: picker.endDate.format('YYYY-MM-DD') });
        }
    }).on('cancel.daterangepicker', function(ev, picker) {
        picker.setStartDate(moment());
        picker.setEndDate(moment());
        $(this).val('');
        @this.set('{{ $model }}', null);
    });
}

Livewire.on('clear-{{ $nama }}', () => {
    $('#{{ $nama }}').val('');
    @this.set('{{ $model }}', null);
    settingDateRange['startDate'] = moment();
    initDatePicker();
});

Livewire.on('set-{{ $nama }}', ({ select_date }) => {
    if (singleDatePicker) {
        var date = moment(select_date);
        settingDateRange['startDate'] = date;
        setSingleDate(date);
    } else {
        var start = moment(select_date['start']);
        var end = moment(select_date['end']);
        settingDateRange['startDate'] = start;
        settingDateRange['endDate'] = end;
        setDateRange(start, end);
    }
    initDatePicker();
});


initDatePicker();">
    <div class="relative w-full" x-data>
        <!-- Icon di awal (Date) -->
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <i class="iconify solar--calendar-bold-duotone text-lg"></i>
        </span>

        <input autocomplete="off" type="text" id="{{ $nama }}" placeholder="{{ $placeholder ?? 'Pilih Tanggal' }}"
               {{ $attributes->merge(['class' => 'pl-10 pr-10 py-2 border rounded-md focus:ring focus:ring-blue-300 focus:border-blue-500 form-control']) }}>

        <!-- Icon di akhir (Arrow Down) -->
        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">
            <i class="iconify tabler--chevron-down"></i>
        </span>
    </div>
    @if ($errorName)
        @error($errorName)
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    @endif

</div>
