require("./modules/bootstrap");

import { createApp } from "vue";
import { createStore } from "vuex";
import mixins from "./mixins";
import groups from "./store/modules/groups";
import GroupsBrowser from "./components/GroupsBrowser.vue";

const debug = process.env.NODE_ENV !== "production";

const store = createStore({
    modules: {
        groups,
    },
    strict: debug,
});

createApp({
    components: { GroupsBrowser },
    el: "#app",
})
    .mixin(mixins)
    .use(store)
    .mount("#app");
