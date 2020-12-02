require("./modules/bootstrap");

import { createApp } from "vue";
import mixins from "./mixins";
import Importer from "./components/Importer.vue";

createApp({
    components: { Importer },
    el: "#app",
})
    .mixin(mixins)
    .mount("#app");
