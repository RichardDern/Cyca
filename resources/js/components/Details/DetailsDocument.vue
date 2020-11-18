<template>
    <article>
        <h1>
            <div class="title">
                <img v-bind:src="document.favicon" class="favicon" />
                <span>{{ document.title }}</span>
                <div
                    class="badge article"
                    v-if="document.feed_item_states_count > 0"
                >
                    {{ document.feed_item_states_count }}
                </div>
            </div>
            <div class="flex items-center">
                <button
                    v-if="document.feed_item_states_count > 0"
                    class="button info"
                    v-on:click="onMarkAsReadClicked"
                >
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="mr-1"
                    >
                        <use v-bind:xlink:href="icon('unread_items')" />
                    </svg>
                    {{ __("Mark as read") }}
                </button>
                <a
                    class="button info ml-2"
                    v-bind:href="url"
                    rel="noopener noreferrer"
                    v-on:click.left.stop.prevent="
                        openDocument({
                            document: document,
                        })
                    "
                    v-on:click.middle.exact="
                        incrementVisits({
                            document: document,
                        })
                    "
                >
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="mr-1"
                    >
                        <use v-bind:xlink:href="icon('open')" />
                    </svg>
                    {{ __("Open") }}
                </a>
                <button class="button info ml-2" v-on:click="onShareClicked">
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="mr-1"
                    >
                        <use v-bind:xlink:href="icon('share')" />
                    </svg>
                    {{ __("Share") }}
                </button>
            </div>
        </h1>

        <div class="body">
            <div
                v-if="document.description"
                v-html="document.description"
            ></div>

            <dl>
                <dt>{{ __("Real URL") }}</dt>
                <dd>
                    <a
                        v-bind:href="document.url"
                        rel="noopener noreferrer"
                        v-on:click.left.stop.prevent="
                            openDocument({
                                document: document,
                            })
                        "
                        class="readable"
                        v-html="document.ascii_url"
                    ></a>
                </dd>
                <dt v-if="document.visits">{{ __("Visits") }}</dt>
                <dd v-if="document.visits">
                    {{ document.visits }}
                </dd>
                <dt>{{ __("Date of document's last check") }}</dt>
                <dd>
                    <date-time
                        v-bind:datetime="document.checked_at"
                        v-bind:calendar="true"
                    ></date-time>
                </dd>
                <dt v-if="dupplicateInFolders.length > 0">
                    {{ __("Also exists in") }}
                </dt>
                <dd v-if="dupplicateInFolders.length > 0">
                    <button
                        class="bg-gray-400 hover:bg-gray-500"
                        v-for="dupplicateInFolder in dupplicateInFolders"
                        v-bind:key="dupplicateInFolder.id"
                        v-on:click="
                            $emit('folder-selected', dupplicateInFolder)
                        "
                    >
                        <svg
                            fill="currentColor"
                            width="16"
                            height="16"
                            class="favicon"
                            v-bind:class="dupplicateInFolder.iconColor"
                        >
                            <use
                                v-bind:xlink:href="
                                    icon(dupplicateInFolder.icon)
                                "
                            />
                        </svg>
                        <span class="truncate flex-grow py-0.5">{{
                            dupplicateInFolder.title
                        }}</span>
                    </button>
                </dd>
            </dl>

            <h2 v-if="document.feeds && document.feeds.length > 0">
                {{ __("Feeds") }}
            </h2>

            <div
                v-for="feed in document.feeds"
                v-bind:key="feed.id"
                class="feeds-list"
            >
                <div class="flex justify-between items-center">
                    <div class="flex items-center my-0 py-0">
                        <img v-bind:src="feed.favicon" class="favicon" />
                        <div>{{ feed.title }}</div>
                    </div>
                    <button
                        class="button success"
                        v-if="feed.is_ignored"
                        v-on:click="follow(feed)"
                    >
                        {{ __("Follow") }}
                    </button>
                    <button
                        class="button danger"
                        v-if="!feed.is_ignored"
                        v-on:click="ignore(feed)"
                    >
                        {{ __("Ignore") }}
                    </button>
                </div>
                <div v-if="feed.description" v-html="feed.description"></div>

                <dl>
                    <dt>{{ __("Real URL") }}</dt>
                    <dd>
                        <div class="readable" v-html="feed.ascii_url"></div>
                    </dd>
                    <dt>{{ __("Date of document's last check") }}</dt>
                    <dd>
                        <date-time
                            v-bind:datetime="feed.checked_at"
                            v-bind:calendar="true"
                        ></date-time>
                    </dd>
                </dl>
            </div>

            <div class="mt-6" v-if="selectedFolder.type !== 'unread_items'">
                <button class="danger" v-on:click="onDeleteDocument">
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="mr-1"
                    >
                        <use v-bind:xlink:href="icon('trash')" />
                    </svg>
                    {{ __("Delete") }}
                </button>
            </div>
        </div>
    </article>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
    computed: {
        ...mapGetters({
            document: "documents/selectedDocument",
            selectedFolder: "folders/selectedFolder",
            feeds: "feeds/feeds",
        }),

        /**
         * Filters dupplicates provided by the document by excluding current
         * folder
         */
        dupplicateInFolders: function () {
            const self = this;

            return collect(self.document.dupplicates)
                .reject((folder) => folder.id === self.selectedFolder.id)
                .all();
        },

        /**
         * Return document's initial URL instead of the real URL, unless there
         * is not attached bookmark
         */
        url: function () {
            const self = this;

            if (self.document.bookmark.initial_url) {
                return self.document.bookmark.initial_url;
            }

            return self.document.url;
        },
    },
    watch: {
        document: function (document) {
            if (document && document.id) {
                this.load(document);
            }
        },
    },
    mounted: function () {
        if (this.document && this.document.id) {
            this.load(this.document);
        }
    },
    methods: {
        ...mapActions({
            incrementVisits: "documents/incrementVisits",
            openDocument: "documents/openDocument",
            ignoreFeed: "documents/ignoreFeed",
            followFeed: "documents/followFeed",
            load: "documents/load",
        }),

        /**
         * Mark as read button clicked
         */
        onMarkAsReadClicked: function () {
            const self = this;

            self.$emit("feeditems-read", {
                documents: [self.document.id],
            });
        },

        /**
         * Delete button clicked
         */
        onDeleteDocument: function () {
            const self = this;

            self.$emit("documents-deleted", {
                folder: self.selectedFolder,
                documents: [self.document],
            });
        },

        /**
         * Share button clicked
         */
        onShareClicked: function () {
            const self = this;
            const sharedData = {
                title: self.document.title,
                text: self.document.description,
                url: self.document.url,
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

        /**
         * Follow specified feed
         */
        follow: function (feed) {
            const self = this;

            self.followFeed(feed);
        },

        /**
         * Ignore specified feed
         */
        ignore: function (feed) {
            const self = this;

            self.ignoreFeed(feed);
        },
    },
};
</script>
