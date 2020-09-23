export default {
    /**
     * Set feed items list
     * @param {*} state
     * @param {*} feedItems
     */
    setFeedItems(state, feedItems) {
        state.feedItems = feedItems;
    },

    /**
     * Set next page #
     * @param {*} state
     * @param {*} page
     */
    setNextPage(state, page) {
        state.nextPage = page;
    },

    /**
     * Set feeds associated to current feed items
     * @param {*} state
     * @param {*} feeds
     */
    setFeeds(state, feeds) {
        state.feeds = feeds;
    },

    /**
     * Set selected feed items
     * @param {*} state
     * @param {*} feedItems
     */
    setSelectedFeedItems(state, feedItems) {
        if(!feedItems) {
            feedItems = [];
        }

        state.selectedFeedItems = feedItems;
    },

    /**
     * Update feed item's properties
     * @param {*} state
     * @param {*} param1
     */
    update(state, {feedItem, newProperties}) {
        for(var property in newProperties) {
            feedItem[property] = newProperties[property];
        }
        console.log(feedItem);
    },
};
