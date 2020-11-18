require("./modules/bootstrap");
require("./modules/components")("groups");

import Vuex from "vuex";
import groups from "./store/modules/groups";

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== "production";

const store = new Vuex.Store({
    modules: {
        groups
    },
    strict: debug
});

const app = new Vue({    
    el: "#app",
    store
});