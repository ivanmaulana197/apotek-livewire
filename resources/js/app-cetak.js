import {
    Livewire,
    // Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";
import moment from "moment";
import focus from "@alpinejs/focus";
window.moment = moment;
import "./library/moment/moment-locale.js";
moment.locale("id");

// Alpine.plugin(focus);

Livewire.start();

import "./helpers.js";
