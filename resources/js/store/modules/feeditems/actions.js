import { collect } from "collect.js";

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
     * Select first unread item in current list
     */
    selectFirstUnreadFeedItem({ getters, dispatch }, exclude) {
        if(!exclude) {
            exclude = [];
        }

        const nextFeedItem = collect(getters.feedItems).where('unread_feed_items_count', '>', 0).whereNotIn('id', exclude).first();

        if (nextFeedItem) {
            dispatch("selectFeedItems", [nextFeedItem]);
        } else {
            dispatch("selectFeedItems", []);
            dispatch("documents/selectFirstDocumentWithUnreadItems", null, { root: true });
        }
    },

    /**
     * Mark feed items as read
     */
    async markAsRead({ dispatch, commit }, data) {
        if ("feed_items" in data) {
            dispatch("selectFirstUnreadFeedItem", data.feed_items);
        } else if ("documents" in data) {
            dispatch("documents/selectFirstDocumentWithUnreadItems", data.documents, { root: true });
        }

        const response = await axios.post(
            route("feed_item.mark_as_read"),
            data
        );

        commit("folders/setFolders", response.data, { root: true });
        await dispatch("documents/index", null, { root: true });
    }
};
