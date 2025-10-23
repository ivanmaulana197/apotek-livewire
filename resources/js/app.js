// import './bootstrap';
import "preline";
import "simplebar";
import "iconify-icon";
import "@tabler/icons-webfont/dist/tabler-icons.min.css"; //impoert module icon

import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";

import focus from "@alpinejs/focus";
import Swal from "sweetalert2";
window.Swal = Swal;
import TomSelect from "tom-select";
window.TomSelect = TomSelect;

// konfigurasi daterangepicker
import moment from "moment";
window.moment = moment;
import "./library/moment/moment-locale.js";
moment.locale("id");

import "daterangepicker";
// comment jika ingin build
let jp = $.noConflict(true);

Alpine.plugin(focus);

Livewire.start();

// Livewire.hook("commit", ({ component, commit, respond, succeed, fail }) => {
//     succeed(({ snapshot, effect }) => {
//         window.HSStaticMethods.autoInit();
//     });
// });

document.addEventListener("livewire:navigated", () => {
    Livewire.hook("morphed", ({ el, component }) => {
        // Runs after all child elements in `component` are morphed
        window.HSStaticMethods.autoInit();
    });

    window.HSStaticMethods.autoInit();
    init();

    const sidebar = document.getElementById("sidebarnav");
    let activeItem = sidebar.querySelector(".dropdown-submenu a.active");

    // Kalau tidak ada subitem aktif, cari item aktif biasa
    if (!activeItem) {
        activeItem = sidebar.querySelector(".sidebar-item a.active");
    }

    // Kalau ada elemen aktif, scroll ke tengah
    if (activeItem) {
        activeItem.scrollIntoView({
            behavior: "smooth",
            block: "start",
        });
    }
});

function init() {
    // document.documentElement.setAttribute("data-layout", localStorage.getItem('data-layout')??'vertical');
    document.documentElement.setAttribute(
        "data-color-theme",
        localStorage.getItem("data-color-theme") ?? "Blue_Theme"
    );
}

import "./helpers.js";
import "./tom-select.js";
import "./daterangepicker.js";
