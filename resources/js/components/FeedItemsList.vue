<template>
    <div id="feeditems-list" v-on:scroll.passive="onScroll">
        <feed-item
            v-for="feedItem in sortedList"
            v-bind:key="feedItem.id"
            v-bind:feedItem="feedItem"
            v-on:selected-feeditems-changed="onSelectedFeedItemsChanged"
        ></feed-item>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import FeedItem from "./FeedItem";

export default {
    components: { FeedItem },
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            feedItems: "feedItems/feedItems",
            canLoadMore: "feedItems/canLoadMore",
            selectedFolder: "folders/selectedFolder",
        }),
        /**
         * Return list of feed items sorted by published date
         */
        sortedList: function () {
            const self = this;
            let collection = collect(self.feedItems).sortByDesc("published_at");

            if (
                self.selectedFolder &&
                self.selectedFolder.type === "unread_items"
            ) {
                collection = collection.where("feed_item_states_count", ">", 0);
            }

            return collection.all();
        },
    },
    watch: {
        sortedList: function () {
            const self = this;

            self.$nextTick(function () {
                const scrollHeight = self.$el.scrollHeight;
                const innerHeight = self.$el.clientHeight;

                if (scrollHeight === innerHeight && self.canLoadMore) {
                    self.loadMoreFeedItems();
                }
            });
        },
    },
    methods: {
        ...mapActions({
            loadMoreFeedItems: "feedItems/loadMoreFeedItems",
        }),

        /**
         * Selected feed items has changed
         */
        onSelectedFeedItemsChanged: function (feedItems) {
            const self = this;

            self.$emit("selected-feeditems-changed", feedItems);
        },

        onScroll: function ($event) {
            const scrollTop = $event.target.scrollTop;
            const innerHeight = $event.target.clientHeight;
            const scrollHeight = $event.target.scrollHeight;

            if (scrollTop + innerHeight >= scrollHeight && this.canLoadMore) {
                this.loadMoreFeedItems();
            }
        },
    },
};
</script>
