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
     * @param {*} param0
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
     * @param {*} param0
     * @param {*} feedItems
     */
    selectFeedItems({ commit }, feedItems) {
        commit("setSelectedFeedItems", feedItems);
    },

    /**
     * Mark feed items as read
     */
    async markAsRead({ dispatch, getters }, data) {
        await axios.post(route("feed_item.mark_as_read"), data);

        dispatch("folders/index", null, { root: true });
        dispatch("folders/show", null, { root: true });
    }
};
