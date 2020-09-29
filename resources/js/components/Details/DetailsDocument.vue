<template>
    <article>
        <h1>
            <div class="title">
                <img v-bind:src="document.favicon" class="favicon" />
                <span>{{ document.title }}</span>
                <div
                    class="badge"
                    v-if="document.unread_feed_items_count > 0"
                >{{ document.unread_feed_items_count }}</div>
            </div>
            <div class="flex items-center">
                <button
                    v-if="document.unread_feed_items_count > 0"
                    class="button info"
                    v-on:click="onMarkAsReadClicked"
                >
                    <svg fill="currentColor" width="16" height="16" class="mr-1">
                        <use v-bind:xlink:href="icon('unread_items')" />
                    </svg>
                    {{ __("Mark as read") }}
                </button>
                <a
                    class="button info ml-2"
                    v-bind:href="url"
                    rel="noopener noreferrer"
                    v-on:click.left.stop.prevent="openDocument({document: document, folder: selectedFolder})"
                    v-on:click.middle.exact="incrementVisits({document: document, folder: selectedFolder})"
                >
                    <svg fill="currentColor" width="16" height="16" class="mr-1">
                        <use v-bind:xlink:href="icon('open')" />
                    </svg>
                    {{ __("Open") }}
                </a>
                <button class="button info ml-2" v-on:click="onShareClicked">
                    <svg fill="currentColor" width="16" height="16" class="mr-1">
                        <use v-bind:xlink:href="icon('share')" />
                    </svg>
                    {{ __("Share") }}
                </button>
            </div>
        </h1>

        <div class="body">
            <div v-if="document.description" v-html="document.description"></div>

            <dl>
                <dt>{{ __("Real URL") }}</dt>
                <dd>
                    <a
                        v-bind:href="document.url"
                        rel="noopener noreferrer"
                        v-on:click.left.stop.prevent="openDocument({document: document, folder: selectedFolder})"
                    >{{ document.url }}</a>
                </dd>
                <dt v-if="document.bookmark.visits">{{ __("Visits") }}</dt>
                <dd v-if="document.bookmark.visits">{{ document.bookmark.visits }}</dd>
                <dt>{{__("Date of document's last check")}}</dt>
                <dd>
                    <date-time v-bind:datetime="document.checked_at" v-bind:calendar="true"></date-time>
                </dd>
                <dt v-if="dupplicateInFolders.length > 0">{{ __("Also exists in") }}</dt>
                <dd v-if="dupplicateInFolders.length > 0">
                    <button
                        class="bg-gray-400 hover:bg-gray-500"
                        v-for="dupplicateInFolder in dupplicateInFolders"
                        v-bind:key="dupplicateInFolder.id"
                        v-on:click="$emit('folder-selected', dupplicateInFolder)"
                    >
                        <svg
                            fill="currentColor"
                            width="16"
                            height="16"
                            class="favicon"
                            v-bind:class="dupplicateInFolder.iconColor"
                        >
                            <use
                                v-bind:xlink:href="icon(dupplicateInFolder.icon)"
                            />
                        </svg>
                        <span class="truncate flex-grow py-0.5">{{ dupplicateInFolder.title }}</span>
                    </button>
                </dd>
            </dl>

            <h2 v-if="document.feeds && document.feeds.length > 0">{{ __("Feeds") }}</h2>

            <div
                v-for="feed in document.feeds"
                v-bind:key="feed.id"
                class="rounded bg-gray-600 mb-2 p-2"
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
                    >{{ __("Follow") }}</button>
                    <button
                        class="button danger"
                        v-if="!feed.is_ignored"
                        v-on:click="ignore(feed)"
                    >{{ __("Ignore") }}</button>
                </div>
                <div v-if="feed.description" v-html="feed.description"></div>

                <dl>
                    <dt>{{ __("Real URL") }}</dt>
                    <dd>
                        <div>{{ feed.url }}</div>
                    </dd>
                    <dt>{{__("Date of document's last check")}}</dt>
                    <dd>
                        <date-time v-bind:datetime="feed.checked_at" v-bind:calendar="true"></date-time>
                    </dd>
                </dl>
            </div>

            <div class="mt-6">
                <button class="danger" v-on:click="onDeleteDocument">
                    <svg fill="currentColor" width="16" height="16" class="mr-1">
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
    methods: {
        ...mapActions({
            incrementVisits: "documents/incrementVisits",
            openDocument: "documents/openDocument",
            ignoreFeed: "documents/ignoreFeed",
            followFeed: "documents/followFeed"
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

            try {
                navigator.share(sharedData);
            } catch (err) {
                console.log(err);
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
