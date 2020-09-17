import Vue from "vue";
import Vuex from "vuex";
import folders from "./modules/folders";
import documents from "./modules/documents";
import feedItems from "./modules/feeditems";

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== "production";

export default new Vuex.Store({
    modules: {
        folders,
        documents,
        feedItems
    },
    strict: debug
});
