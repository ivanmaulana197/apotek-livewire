window.initDateRangePicker = function ({
    el,
    ranges = true,
    singleDatePicker = false,
    minDate = null,
    parentEl = "body",
}) {
    const model = el.getAttribute("x-model"); // ambil nama x-model (misal: 'form.tipe')
    let nama = el.nama;
    var select_date = Alpine.evaluate(el, model);
    if (ranges) {
        ranges = {
            Today: [moment(), moment()],
            Yesterday: [
                moment().subtract(1, "days"),
                moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "This Week": [moment().startOf("week"), moment().endOf("week")],
            "Last Week": [
                moment().subtract(1, "week").startOf("week"),
                moment().subtract(1, "week").endOf("week"),
            ],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month"),
            ],
        };
    }

    let settings = {
        showDropdowns: true,
        parentEl: parentEl,
        singleDatePicker: singleDatePicker,
        autoUpdateInput: false,
        alwaysShowCalendars: true,
        ranges: ranges,
        minDate: minDate,
        drops: "auto",
        locale: {
            cancelLabel: "Clear",
        },
        cancelClass: "bg-red-500",
    };

    if (select_date) {
        if (singleDatePicker) {
            select_date = formatDateStringToDate(select_date);
            var date = moment(select_date);
            settings["startDate"] = date;
            setSingleDate(date);
        } else {
            var start = moment(select_date["start"]);
            var end = moment(select_date["end"]);
            settings["startDate"] = start;
            settings["endDate"] = end;
            setDateRange(start, end);
        }
    }

    function initial() {
        $(el)
            .daterangepicker(settings)
            .on("apply.daterangepicker", function (ev, picker) {
                singleDatePicker
                    ? setSingleDate(picker.startDate)
                    : setDateRange(picker.startDate, picker.endDate);
            })
            .on("hide.daterangepicker", function (ev, picker) {
                singleDatePicker
                    ? setSingleDate(picker.startDate)
                    : setDateRange(picker.startDate, picker.endDate);
            })
            .on("cancel.daterangepicker", function (ev, picker) {
                picker.setStartDate(moment());
                picker.setEndDate(moment());
                $(this).val("");
                this.dispatchEvent(new Event("input"));
            });
    }

    function setSingleDate(date) {
        const formatted = date.format("D MMMM YYYY");
        $(el).val(formatted);
        el.dispatchEvent(new Event("input"));

        // Update x-model
        let data = Alpine.$data(el);
        data[model] = formatted; //update data berdasarkan x-model
    }

    function setDateRange(startDate, endDate) {
        const formatted =
            startDate.format("D MMMM YYYY") +
            " - " +
            endDate.format("D MMMM YYYY");
        $(el).val(formatted);
        // console.log(formatted);
        el.dispatchEvent(new Event("input"));
        let data = Alpine.$data(el);
        data[model] = formatted; //update data berdasarkan x-model
    }

    initial();

    window.addEventListener("clear-" + nama, () => {
        $(el).val("");
        settings["startDate"] = moment();
        initial();
    });
    window.addEventListener("set-" + nama, ({ detail: { value } }) => {
        // console.log(value);
        if (singleDatePicker) {
            let date = moment(value);
            settings["startDate"] = date;
            setSingleDate(date);
        } else {
            let start = moment(value["start"]);
            let end = moment(value["end"]);
            settings["startDate"] = start;
            settings["endDate"] = end;
            setDateRange(start, end);
        }
        initial();
    });

    // console.log(el.id); // el.id;
};
