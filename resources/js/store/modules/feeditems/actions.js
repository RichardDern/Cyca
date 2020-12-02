import { collect } from "collect.js";

/**
 * Feed items actions
 */
export default {
    /**
     * Load feed items for specified feeds
     */
    async index({ getters, commit }, documents) {
        const feeds = collect(documents)
            .pluck("feeds")
            .flatten(1)
            .pluck("id")
            .all();

        let nextPage = 0;
        let feedItems = [];
        const currentlySelectedItem = getters.selectedFeedItem;

        if (feeds.length > 0) {
            const data = await api.get(route("feed_item.index"), {
                feeds: feeds,
            });

            feedItems = data.data;
            nextPage = data.next_page_url !== null ? data.current_page + 1 : 0;
        }

        if (currentlySelectedItem) {
            const itemFeeds = collect(currentlySelectedItem.feeds)
                .pluck("id")
                .flatten(1);

            if (itemFeeds.intersect(feeds)) {
                let collection = collect(feedItems);

                if (!collection.where("id", currentlySelectedItem.id).first()) {
                    feedItems.push(currentlySelectedItem);
                }
            }
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
        const data = await api.get(route("feed_item.index"), {
            page: getters.nextPage,
            feeds: getters.feeds,
        });

        const newItems = [...items, ...data.data];

        commit(
            "setNextPage",
            data.next_page_url !== null ? data.current_page + 1 : 0
        );

        commit("setFeeds", getters.feeds);
        commit("setFeedItems", newItems);
    },

    /**
     * Change selected feed items
     */
    async selectFeedItems({ commit }, feedItems) {
        if (feedItems.length === 1) {
            const data = await api.get(route("feed_item.show", feedItems[0]));
            commit("update", { feedItem: feedItems[0], newProperties: data });
        }

        commit("setSelectedFeedItems", feedItems);
    },

    /**
     * Select first unread item in current list
     */
    selectFirstUnreadFeedItem({ getters, dispatch }, exclude) {
        if (!exclude) {
            exclude = [];
        }

        const nextFeedItem = collect(getters.feedItems)
            .where("feed_item_states_count", ">", 0)
            .whereNotIn("id", exclude)
            .first();

        if (nextFeedItem) {
            dispatch("selectFeedItems", [nextFeedItem]);
        } else {
            dispatch(
                "documents/selectFirstDocumentWithUnreadItems",
                { selectFirstUnread: true },
                {
                    root: true,
                }
            );
        }
    },

    selectNextUnreadFeedItem({ getters, dispatch }, currentId) {
        if (!currentId) {
            return dispatch("selectFirstUnreadFeedItem");
        }

        const nextFeedItem = collect(getters.feedItems)
            .where("feed_item_states_count", ">", 0)
            .skipUntil((item) => item.id === currentId)
            .skip(1)
            .shift();

        if (nextFeedItem) {
            dispatch("selectFeedItems", [nextFeedItem]);
        } else {
            dispatch(
                "documents/selectFirstDocumentWithUnreadItems",
                { selectFirstUnread: true },
                {
                    root: true,
                }
            );
        }
    },

    /**
     * Mark feed items as read
     */
    markAsRead({ dispatch, commit, getters }, data) {
        const nextPage = getters.nextPage;

        // Avoid unwanted reloading
        commit("setNextPage", null);

        api.post(route("feed_item.mark_as_read"), data).then(function (
            response
        ) {
            dispatch("updateUnreadFeedItemsCount", response);
            commit("setNextPage", nextPage);

            if ("feed_items" in data) {
                dispatch("selectNextUnreadFeedItem", data.feed_items[0]);
            } else if ("documents" in data) {
                dispatch(
                    "documents/selectFirstDocumentWithUnreadItems",
                    { exclude: data.documents },
                    { root: true }
                );
            }
        });
    },

    /**
     * Change number of unread feed items everywhere it's necessary
     */
    updateUnreadFeedItemsCount({ commit, dispatch, getters }, data) {
        if ("updated_feed_items" in data && data.updated_feed_items !== null) {
            const feedItems = collect(getters.feedItems).whereIn(
                "id",
                data.updated_feed_items
            );

            feedItems.each(function (feedItem) {
                commit("update", {
                    feedItem: feedItem,
                    newProperties: {
                        feed_item_states_count: 0,
                    },
                });
            });
        }

        if ("documents" in data) {
            for (var documentId in data.documents) {
                dispatch(
                    "documents/update",
                    {
                        documentId: documentId,
                        newProperties: {
                            feed_item_states_count: data.documents[documentId],
                        },
                    },
                    { root: true }
                );
            }
        }

        if ("folders" in data) {
            for (var folderId in data.folders) {
                dispatch(
                    "folders/updateProperties",
                    {
                        folderId: folderId,
                        newProperties: {
                            feed_item_states_count: data.folders[folderId],
                        },
                    },
                    { root: true }
                );
            }
        }

        if ("groups" in data) {
            for (var groupId in data.groups) {
                dispatch(
                    "groups/updateProperties",
                    {
                        groupId: groupId,
                        newProperties: {
                            feed_item_states_count: data.groups[groupId],
                        },
                    },
                    { root: true }
                );
            }
        }
    },
};
