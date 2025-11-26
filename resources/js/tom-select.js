window.initTomSelect = function ({
    el,

    mainSelect,
    optionValue,
    optionLabel,
    direction = true,
    dropdownParent = "body",
    dropdownInput = true,
    emptyOption = true,
}) {
    let name = el.name;
    const model = el.getAttribute("x-model"); // ambil nama x-model (misal: 'form.tipe')
    // console.log(name);
    el.tomselect?.destroy();
    el.tomselect = new TomSelect(el, {
        plugins: [
            dropdownInput && "dropdown_input",
            emptyOption ? "remove_button" : "no_backspace_delete",
        ].filter(Boolean),
        sortField: direction ? { field: "text", direction: "asc" } : null,
        dropdownParent: dropdownParent,
    });

    //jika dia ""(!initialValue) dan emptyoption: false maka=>
    const initialValue = Alpine.evaluate(el, model);

    if (initialValue) {
        el.tomselect.setValue(initialValue);
    } else {
        if (!emptyOption) {
            // let data = Alpine.$data(el);
            // data[model] = el.tomselect.getValue();
            Alpine.evaluate(el, `${model} = "${el.tomselect.getValue()}"`);
        }
    }

    window.addEventListener((mainSelect ?? name) + "-created", ({ detail }) => {
        let data = detail[mainSelect ?? name];
        el.tomselect.addOption({
            value: data[optionValue],
            text: data[optionLabel],
        });
    });

    window.addEventListener("selected-" + name, ({ detail: { selected } }) => {
        el.tomselect.setValue(selected);
    });

    window.addEventListener(
        "update_" + name,
        ({ detail: { data, selected } }) => {
            if (data) {
                // let selectedValues = tom.getValue(); // simpan value terpilih
                el.tomselect.clear(true);
                el.tomselect.clearOptions(); // hapus semua option lama
                // console.log('id', id, 'data', data, 'selected', selected);
                data.forEach((item) => {
                    el.tomselect.addOption({
                        value: item[optionValue],
                        text: item[optionLabel],
                    });
                });

                el.tomselect.setValue(selected, true); // restore pilihan
            } else {
                el.tomselect.clear();
            }
            if (selected) {
                Alpine.evaluate(el, `${model} = "${selected}"`);
            } else {
                Alpine.evaluate(el, `${model} = ${selected}`);
            }
        }
    );

    window.addEventListener("clear-select-" + name, () => {
        el.tomselect.setValue("");
    });
};

window.addEventListener(
    "add-option-tom-select",
    ({ detail: { id, value, text } }) => {
        try {
            var subSelect = $(`[main-select="${id}"]`);
            subSelect?.each(function (index, el) {
                el.tomselect?.addOption({
                    value: value,
                    text: text,
                });
            });

            $(`#${id}`)[0].tomselect?.addOption({
                value: value,
                text: text,
            });
        } catch (e) {
            console.log(e);
        }
    }
);
// add value dari livewire di set id nya
window.addEventListener("set-value-tom-select", ({ detail: { id, value } }) => {
    try {
        $(`#${id}`)[0].tomselect?.setValue(value);
        $(`#${id}`)[0].dispatchEvent(new Event("input"));
    } catch (e) {
        console.log(e);
    }
});

window.addEventListener("clear-select-tom-select", ({ detail: { id } }) => {
    try {
        $(`#${id}`)[0].tomselect?.clear();
        $(`#${id}`)[0].dispatchEvent(new Event("input"));
    } catch (e) {
        console.log(e);
    }
});

window.addEventListener(
    "update-all-options-tom-select",
    ({ detail: { id, data, selected } }) => {
        try {
            var tom = $(`#${id}`)[0].tomselect;
            if (data) {
                // let selectedValues = tom.getValue(); // simpan value terpilih
                tom.clear(true);
                tom.clearOptions(); // hapus semua option lama
                // console.log('id', id, 'data', data, 'selected', selected);
                // data.forEach((item) => {
                //     tom.addOption({
                //         value: item.id,
                //         text: item.nama,
                //     });
                // });
                tom.addOptions(data);

                tom.setValue(selected, true); // restore pilihan
            } else {
                tom.clear();
            }
            $(`#${id}`)[0].dispatchEvent(new Event("input"));
        } catch (e) {
            console.log(e);
        }
    }
);
