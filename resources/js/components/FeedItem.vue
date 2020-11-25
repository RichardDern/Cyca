<template>
    <a
        class="feed-item"
        v-bind:class="{
            selected: is_selected,
            read: feedItem.feed_item_states_count === 0,
        }"
        v-bind:href="feedItem.url"
        rel="noopener noreferrer"
        v-on:click.left.exact.stop.prevent="onClicked"
    >
        <div class="feed-item-label" v-html="highlight(feedItem.title)"></div>
        <div class="feed-item-meta">
            <date-time
                class="flex-none mr-4 w-2/12"
                v-bind:datetime="feedItem.published_at"
                v-bind:calendar="true"
            ></date-time>
            <img v-bind:src="feedItem.feeds[0].favicon" class="favicon" />
            <div class="truncate">{{ feedItem.feeds[0].title }}</div>
        </div>
    </a>
</template>

<script>
import TEXTColor from "textcolor";
import { mapGetters, mapActions } from "vuex";
import DateTime from "./DateTime";

export default {
    components: { DateTime },
    data: function () {
        return {
            enableEnsureVisible: true,
        };
    },
    props: ["feedItem"],
    computed: {
        ...mapGetters({
            selectedFeedItems: "feedItems/selectedFeedItems",
        }),

        /**
         * Return a boolean value indicating if this document belongs to
         * currently selected documents
         */
        is_selected: function () {
            const self = this;

            let selected = false;

            try {
                selected =
                    self.selectedFeedItems &&
                    self.selectedFeedItems.find(
                        (fi) => fi.id === self.feedItem.id
                    );
            } finally {
                return selected;
            }
        },
    },
    watch: {
        is_selected: function (selected) {
            const self = this;

            if (selected) {
                self.ensureVisible();
            }
        },
    },
    methods: {
        ...mapActions({
            selectFeedItem: "feedItems/selectFeedItem",
            addFeedItemToSelection: "documents/addFeedItemToSelection",
        }),

        /**
         * Document has been clicked
         */
        onClicked: function () {
            const self = this;

            self.$emit("selected-feeditems-changed", [self.feedItem]);
        },

        /**
         * Add this document to selection
         */
        onAddToSelection: function () {
            const self = this;

            let selectedFeedItems = [...self.selectedFeedItems];

            const index = selectedFeedItems.findIndex(
                (fi) => fi.id === self.feedItem.id
            );

            if (index === -1) {
                selectedFeedItems.push(self.feedItem);
            } else {
                selectedFeedItems.splice(index, 1);
            }

            self.$emit("selected-feeditems-changed", selectedFeedItems);
        },

        highlight: function (title) {
            highlights.forEach(function (highlight) {
                var regex = new RegExp(
                    "(" + highlight.expression + ")(?![^<]*>|[^<>]*</)",
                    "i"
                );
                let textColor = TEXTColor.findTextColor(highlight.color);
                title = title.replace(
                    regex,
                    '<span class="highlight" style="color: ' +
                        textColor +
                        "; background-color: " +
                        highlight.color +
                        '">$1</span>'
                );
            });

            return title;
        },

        ensureVisible: function () {
            const self = this;

            if (self.enableEnsureVisible) {
                self.$el.scrollIntoView({
                    block: "center",
                });
            }
        },
    },
};
</script>
