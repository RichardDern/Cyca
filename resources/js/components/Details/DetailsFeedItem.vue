<template>
    <article>
        <h1>
            <div class="title">
                <span v-html="feedItem.title"></span>
            </div>
            <div class="flex items-center">
                <button
                    v-if="details && details.unread_feed_items_count > 0"
                    class="button info"
                    v-on:click="onMarkAsReadClicked"
                >
                    <svg fill="currentColor" width="16" height="16" class="mr-1">
                        <use xlink:href="/images/icons.svg#unread_items" />
                    </svg>
                    {{ __("Mark as read") }}
                </button>
                <a class="button info ml-2" v-bind:href="feedItem.url" rel="noopener noreferrer">
                    <svg fill="currentColor" width="16" height="16" class="mr-1">
                        <use xlink:href="/images/icons.svg#open" />
                    </svg>
                    {{ __("Open") }}
                </a>
                <button class="button info ml-2" v-on:click="onShareClicked">
                    <svg fill="currentColor" width="16" height="16" class="mr-1">
                        <use xlink:href="/images/icons.svg#share" />
                    </svg>
                    {{ __("Share") }}
                </button>
            </div>
        </h1>

        <div class="body">
            <div v-if="details" v-html="details.content ? details.content : details.description"></div>

            <dl class="details">
                <dt>{{ __("URL") }}</dt>
                <dd>
                    <a
                        class="url"
                        v-bind:href="feedItem.url"
                        rel="noopener noreferrer"
                    >{{ feedItem.url }}</a>
                </dd>
                <dt>{{__("Date of item's creation")}}</dt>
                <dd>
                    <date-time v-bind:datetime="feedItem.created_at" v-bind:calendar="true"></date-time>
                </dd>
                <dt>{{__("Date of item's publication")}}</dt>
                <dd>
                    <date-time v-bind:datetime="feedItem.published_at" v-bind:calendar="true"></date-time>
                </dd>
                <dt>{{ __("Published in") }}</dt>
                <dd>
                    <button
                        class="bg-gray-400 hover:bg-gray-500"
                        v-for="feed in feedItem.feeds"
                        v-bind:key="feed.id"
                    >
                        <img v-bind:src="feed.favicon" class="favicon" />
                        <div class="py-0.5">{{ feed.title }}</div>
                    </button>
                </dd>
            </dl>
        </div>
    </article>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
    data: function () {
        return {
            /**
             * Feed item details, dynamically loaded
             */
            details: null,
        };
    },
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            feedItem: "feedItems/selectedFeedItem",
        }),
    },
    mounted: function () {
        const self = this;

        self.loadDetails(self.feedItem);
    },
    /**
     * Watchers
     */
    watch: {
        feedItem: function (feedItem) {
            const self = this;

            self.loadDetails(feedItem);
        },
    },
    /**
     * Methods
     */
    methods: {
        ...mapActions({
            loadFolders: "folders/loadFolders",
        }),

        /**
         * Mark as read button clicked
         */
        onMarkAsReadClicked: function () {
            const self = this;

            self.$emit('feeditems-read', {
                feed_items: [self.feedItem.id]
            });
        },

        /**
         * Load all details about specified feed item
         */
        loadDetails: function (feedItem) {
            const self = this;

            if (!feedItem) {
                return;
            }

            axios
                .get(route("feed_item.show", feedItem))
                .then(function (response) {
                    self.details = response.data;
                });
        },
        /**
         * Share button clicked
         */
        onShareClicked: function () {
            const self = this;

            var content = self.feedItem.description
                ? self.feedItem.description
                : self.feedItem.content;

            if (content) {
                content = content.substring(0, 200);
            }

            const sharedData = {
                title: self.feedItem.title,
                text: content,
                url: self.feedItem.url,
            };

            try {
                navigator.share(sharedData);
            } catch (err) {
                console.log(err);
            }
        },
    },
};
</script>
