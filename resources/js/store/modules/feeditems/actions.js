import { collect } from "collect.js";
import feeditems from ".";

/**
 * Feed items actions
 */
export default {
    /**
     * Load feed items for specified feeds
     */
    async index({ commit }, documents) {
        const feeds = collect(documents)
            .pluck("feeds")
            .flatten(1)
            .pluck("id")
            .all();

        let nextPage = 0;
        let feedItems = [];

        if (feeds.length > 0) {
            const response = await axios.get(route("feed_item.index"), {
                params: {
                    feeds: feeds
                }
            });

            feedItems = response.data.data;
            nextPage =
                response.data.next_page_url !== null
                    ? response.data.current_page + 1
                    : 0;
        }

        commit("setNextPage", nextPage);
        commit("setFeeds", feeds);
        commit("setFeedItems", feedItems);
    },

    /**
     * Infinite loading
     */
    async loadMoreFeedItems({ getters, commit }) {
        if (!getters.nextPage || !getters.feeds) {
            return;
        }

        const items = getters.feedItems;
        const response = await axios.get(route("feed_item.index"), {
            params: {
                page: getters.nextPage,
                feeds: getters.feeds
            }
        });

        const newItems = [...items, ...response.data.data];

        commit(
            "setNextPage",
            response.data.next_page_url !== null
                ? response.data.current_page + 1
                : 0
        );
        commit("setFeeds", getters.feeds);
        commit("setFeedItems", newItems);
    },

    /**
     * Change selected feed items
     */
    selectFeedItems({ commit }, feedItems) {
        commit("setSelectedFeedItems", feedItems);
    },

    /**
     * Mark feed items as read
     */
    //TODO: While this code seems to works, it probably could be simplified
    async markAsRead({ dispatch, commit, getters, rootGetters }, data) {
        const response = await axios.post(
            route("feed_item.mark_as_read"),
            data
        );

        commit("folders/setFolders", response.data, { root: true });
        dispatch("documents/index", null, { root: true });

        if ("documents" in data) {
            const nextDocument = collect(rootGetters["documents/documents"])
                .whereNotIn("id", data.documents)
                .first();

            if (nextDocument) {
                dispatch("documents/selectDocuments", [nextDocument], {
                    root: true
                }).then(function() {
                    const nextFeedItem = collect(getters.feedItems).first();

                    if (nextFeedItem) {
                        dispatch("selectFeedItems", [nextFeedItem]);
                    } else {
                        dispatch("selectFeedItems", []);
                    }
                });
            } else {
                dispatch("documents/selectDocuments", [], { root: true });
            }
        } else if ("feed_items" in data) {
            const nextFeedItem = collect(getters.feedItems)
                .whereNotIn("id", data.feed_items)
                .first();

            if (nextFeedItem) {
                dispatch("selectFeedItems", [nextFeedItem]);
            } else {
                const nextDocument = collect(
                    rootGetters["documents/documents"]
                ).first();

                if (nextDocument) {
                    dispatch("documents/selectDocuments", [nextDocument], {
                        root: true
                    }).then(function() {
                        const nextFeedItem = collect(getters.feedItems).first();

                        if (nextFeedItem) {
                            dispatch("selectFeedItems", [nextFeedItem]);
                        } else {
                            dispatch("selectFeedItems", []);
                        }
                    });
                } else {
                    dispatch("documents/selectDocuments", [], { root: true });
                }
            }
        }
    }
};
