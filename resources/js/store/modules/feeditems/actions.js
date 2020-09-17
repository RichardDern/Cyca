/**
 * Feed items actions
 */
export default {
    /**
     * Load feed items for specified feeds
     */
    async index({commit}, feeds) {
        const response = await axios
            .get(route("feed_item.index"), {
                params: {
                    feeds: feeds
                }
            });

        commit("setNextPage", response.data.next_page_url !== null ? response.data.current_page + 1 : 0);
        commit("setFeeds", feeds);
        commit("setFeedItems", response.data.data);
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
        const response = await axios
            .get(route("feed_item.index"), {
                params: {
                    page: getters.nextPage,
                    feeds: getters.feeds
                }
            });

        const newItems = [...items, ...response.data.data];

        commit("setNextPage", response.data.next_page_url !== null ? response.data.current_page + 1 : 0);
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
    async markAsRead({commit}, data) {
        await axios.post(route("feed_item.mark_as_read"), data);
    }
};
