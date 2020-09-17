export default {
    /**
     * Feed items present in current selection (folder, document, feed)
     */
    feedItems: state => {
        return state.feedItems;
    },
    /**
     * Return first feed item in current list
     */
    feedItem: state => {
        return collect(state.feedItems).first();
    },
    /**
     * Return currently selected feed items
     */
    selectedFeedItems: state => {
        if(!state.selectedFeedItems) {
            return [];
        }

        return state.selectedFeedItems;
    },
    /**
     * Return the first selected feed item
     */
    selectedFeedItem: state => {
        return collect(state.selectedFeedItems).first();
    },
    /**
     * Return next page #
     */
    nextPage: state => {
        return state.nextPage;
    },
    /**
     * Return feeds associated with current feed items
     */
    feeds: state => {
        return state.feeds;
    },
    /**
     * Return a boolean value indicating if we can load more feed items
     */
    canLoadMore: state => {
        return state.nextPage > 1;
    }
};
