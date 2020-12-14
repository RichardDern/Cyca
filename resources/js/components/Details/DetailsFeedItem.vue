<template>
    <article>
        <header>
            <h1>
                <span v-html="feedItem.title"></span>
            </h1>
            <div class="tools">
                <button
                    v-if="feedItem.feed_item_states_count > 0"
                    class="button info"
                    v-on:click="onMarkAsReadClicked"
                    v-bind:title="__('Mark as read')"
                >
                    <svg fill="currentColor" width="16" height="16">
                        <use v-bind:xlink:href="icon('unread_items')" />
                    </svg>
                    <span class="hidden xl:inline-block">
                        {{ __("Mark as read") }}
                    </span>
                </button>
                <a
                    class="button info"
                    v-bind:href="feedItem.url"
                    rel="noopener noreferrer"
                    v-bind:title="__('Open')"
                >
                    <svg fill="currentColor" width="16" height="16">
                        <use v-bind:xlink:href="icon('open')" />
                    </svg>
                    <span class="hidden xl:inline-block">
                        {{ __("Open") }}
                    </span>
                </a>
                <button
                    class="button info"
                    v-on:click="onShareClicked"
                    v-bind:title="__('Share')"
                >
                    <svg fill="currentColor" width="16" height="16">
                        <use v-bind:xlink:href="icon('share')" />
                    </svg>
                    <span class="hidden xl:inline-block">
                        {{ __("Share") }}
                    </span>
                </button>
            </div>
        </header>

        <div class="body">
            <div
                class="cyca-prose"
                v-html="
                    feedItem.content ? feedItem.content : feedItem.description
                "
            ></div>

            <stateful-details name="feed_item_details">
                <summary>{{ __("Details") }}</summary>
                <div class="vertical list striped items-rounded compact">
                    <div class="list-item">
                        <div class="list-item-title">{{ __("Real URL") }}</div>
                        <div class="list-item-value">
                            <a
                                v-bind:href="feedItem.url"
                                rel="noopener noreferrer"
                                class="readable"
                                v-html="feedItem.ascii_url"
                            ></a>
                        </div>
                    </div>

                    <div class="list-item">
                        <div class="list-item-title">
                            {{ __("Date of item's creation") }}
                        </div>
                        <div class="list-item-value">
                            <date-time
                                v-bind:datetime="feedItem.created_at"
                                v-bind:calendar="true"
                            ></date-time>
                        </div>
                    </div>

                    <div class="list-item">
                        <div class="list-item-title">
                            {{ __("Date of item's publication") }}
                        </div>
                        <div class="list-item-value">
                            <date-time
                                v-bind:datetime="feedItem.published_at"
                                v-bind:calendar="true"
                            ></date-time>
                        </div>
                    </div>

                    <div class="list-item">
                        <div class="list-item-title">
                            {{ __("Published in") }}
                        </div>
                        <div class="list-item-value">
                            <div class="list horizontal compact">
                                <div
                                    class="list-item px-0 ml-2"
                                    v-for="feed in feedItem.feeds"
                                    v-bind:key="feed.id"
                                    v-bind:title="feed.url"
                                >
                                    <div class="icons">
                                        <img v-bind:src="feed.favicon" />
                                    </div>
                                    <div class="list-item-text">
                                        {{ feed.title }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </stateful-details>
        </div>
    </article>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import DateTime from "../DateTime";
import StatefulDetails from "../StatefulDetails.vue";

export default {
    components: { DateTime, StatefulDetails },
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            feedItem: "feedItems/selectedFeedItem",
        }),
    },
    watch: {
        feedItem: function () {
            this.$el.getElementsByClassName("body")[0].scrollTop = 0;
        },
    },
    /**
     * Methods
     */
    methods: {
        ...mapActions({
            loadFolders: "folders/loadFolders",
            loadFeedItemDetails: "feedItems/loadDetails",
        }),

        /**
         * Mark as read button clicked
         */
        onMarkAsReadClicked: function () {
            const self = this;

            self.$emit("feeditems-read", {
                feed_items: [self.feedItem.id],
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

            if (navigator.share) {
                navigator.share(sharedData);
            } else {
                location.href =
                    "mailto:?subject=" +
                    sharedData.title +
                    "&body=" +
                    sharedData.url;
            }
        },
    },
};
</script>
