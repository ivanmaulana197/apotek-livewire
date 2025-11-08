window.formatCurrency = function (value) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(value);
};

window.parseFormattedNumber = function (formattedStr) {
    return parseFloat(formattedStr.replace(/\./g, "").replace(",", ".")); // Konversi kembali ke angka
};

window.formatedNumber = function (value) {
    value = value.replace(/[^0-9,]/g, "");
    // if (!value || isNaN(value)) {
    // return null;
    // }
    return Number(value).toLocaleString("id-ID");
};

window.formatStringToNumber = function (value) {
    if (!value) {
        return 0;
    }
    value = value.toString();
    value = value.replace(/\./g, "").replace(",", ".");
    if (value.includes(".")) {
        return parseFloat(value);
    }
    return parseInt(value, 10);
};

window.formatToStringNumber = function (value) {
    if (!value) {
        return null;
    }
    value = value.toString();
    let a = value.replace(/[^0-9,]/g, "");

    const parts = a.split(",");
    // Format bagian integer dengan pemisah ribuan
    // let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    let integerPart = Number(parts[0]).toLocaleString("id-ID");
    // Gabungkan kembali bagian integer dan desimal jika ada
    if (parts.length > 1) {
        // Batasi hanya satu koma
        return `${integerPart},${parts[1]}`;
    } else {
        return integerPart;
    }
};

window.formatNumberToString = function (number) {
    // if (!value) {
    //     return null;
    // }
    // let [integer, decimal] = value.toString().split(".");

    // // Format integer dengan tanda titik sebagai pemisah ribuan
    // integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    // // Jika ada bagian desimal, tambahkan koma sebagai pemisah desimal
    // if (decimal) {
    //     return `${integer},${decimal}`;
    // }
    // // Jika tidak ada bagian desimal, kembalikan hanya bagian integer
    // return integer;
    if (number === null || number === undefined || number === "") {
        return null;
    }

    // Ubah ke string agar tidak kehilangan presisi desimal
    let numberStr = String(number).replace(",", "."); // jaga-jaga kalau input pakai koma

    // Pisahkan integer dan desimal
    let parts = numberStr.split(".");

    // Format bagian integer pakai titik untuk ribuan
    let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    // Jika ada desimal
    if (parts[1] && parts[1] !== "") {
        // Hilangkan nol di akhir desimal
        let decimalPart = parts[1].replace(/0+$/, "");

        // Jika setelah di-trim masih ada angka desimal
        if (decimalPart !== "") {
            return `${integerPart},${decimalPart}`;
        }
    }

    // Jika tidak ada desimal atau desimalnya cuma nol semua
    return integerPart;
};

window.formatForInputNumber = function (input) {
    input.value = formatToStringNumber(input.value);
};

window.formatToUpperCase = function (input) {
    input.value = input.value.toUpperCase();
};

window.formatDate = function (
    date,
    showTime = false,
    withSecond = false,
    mode = "full"
) {
    if (!date) return null;

    const bulan = [
        "", // supaya index 1 = Januari
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];

    const m = moment(date);
    if (!m.isValid()) return null;

    const tanggal = m.date();
    const bulanIndo = bulan[m.month() + 1]; // moment bulan dari 0-11
    const tahun = m.year();

    let formattedDate = "";

    switch (mode) {
        case "bulan":
            formattedDate = bulanIndo;
            break;
        case "tahun":
            formattedDate = tahun;
            break;
        default: // 'full'
            formattedDate = `${tanggal} ${bulanIndo} ${tahun}`;
    }

    if (showTime && mode === "full") {
        const jam = m.format(withSecond ? "HH:mm:ss" : "HH:mm");
        formattedDate = `${jam}, ${formattedDate}`;
    }

    return formattedDate;
};

window.formatDateStringToDate = function (tanggal, format = "YYYY-MM-DD") {
    if (!tanggal) return null;

    const parsed = moment(
        tanggal,
        ["D MMMM YYYY", "DD MMMM YYYY", "D MMM YYYY", "YYYY-MM-DD"],
        true
    );
    return parsed.isValid() ? parsed.format(format) : null;
};

window.formatTomSelect = function (data, value = "id", text = "nama") {
    return data.map((item) => {
        return {
            id: item[value],
            text: item[text],
        };
    });
};
