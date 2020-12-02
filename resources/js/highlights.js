require("./modules/bootstrap");

import { createApp } from "vue";
import mixins from "./mixins";
import Highlights from "./components/Highlights.vue";

createApp({
    components: { Highlights },
    el: "#app",
})
    .mixin(mixins)
    .mount("#app");
