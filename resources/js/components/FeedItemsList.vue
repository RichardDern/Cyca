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

export default {
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            feedItems: "feedItems/feedItems",
            canLoadMore: "feedItems/canLoadMore"
        }),
        /**
         * Return list of feed items sorted by published date
         */
        sortedList: function () {
            const self = this;

            return collect(self.feedItems).sortByDesc("published_at").all();
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
        onSelectedFeedItemsChanged: function(feedItems) {
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
