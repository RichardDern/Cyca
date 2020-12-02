import { createStore } from "vuex";
import folders from "./modules/folders";
import documents from "./modules/documents";
import feedItems from "./modules/feeditems";
import groups from "./modules/groups";

const debug = process.env.NODE_ENV !== "production";

export default createStore({
    modules: {
        folders,
        documents,
        feedItems,
        groups,
    },
    strict: debug,
});
