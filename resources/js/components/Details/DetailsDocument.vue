<template>
    <article v-if="document">
        <header>
            <div class="icons">
                <img v-bind:src="document.favicon" />
            </div>

            <h1>{{ document.title }}</h1>

            <div class="badges">
                <div
                    class="badge default"
                    v-if="document.feed_item_states_count > 0"
                >
                    {{ document.feed_item_states_count }}
                </div>
            </div>
            <div class="tools">
                <button
                    v-if="document.feed_item_states_count > 0"
                    class="info"
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
                    class="button info"
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
                <button class="info" v-on:click="onShareClicked">
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
        </header>

        <div class="body">
            <div
                class="cyca-prose"
                v-if="document.description"
                v-html="document.description"
            ></div>

            <details open>
                <summary>{{ __("Details") }}</summary>
                <div class="vertical list striped items-rounded compact">
                    <div class="list-item">
                        <div class="list-item-title">{{ __("Real URL") }}</div>
                        <div class="list-item-value">
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
                        </div>
                    </div>
                    <div class="list-item" v-if="document.visits">
                        <div class="list-item-title">{{ __("Visits") }}</div>
                        <div class="list-item-value">
                            {{ document.visits }}
                        </div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">
                            {{ __("Date of document's last check") }}
                        </div>
                        <div class="list-item-value">
                            <date-time
                                v-bind:datetime="document.checked_at"
                                v-bind:calendar="true"
                            ></date-time>
                        </div>
                    </div>
                    <div
                        class="list-item"
                        v-if="dupplicateInFolders.length > 0"
                    >
                        <div class="list-item-title">
                            {{ __("Also exists in") }}
                        </div>
                        <div class="list-item-value">
                            <div
                                v-for="dupplicateInFolder in dupplicateInFolders"
                                v-bind:key="dupplicateInFolder.id"
                                v-on:click="
                                    $emit(
                                        'folder-selected',
                                        dupplicateInFolder.id,
                                        dupplicateInFolder.group_id
                                    )
                                "
                                v-html="dupplicateInFolder.breadcrumbs"
                                class="cursor-pointer mb-1"
                            ></div>
                        </div>
                    </div>
                </div>
            </details>

            <details v-if="document.feeds.length > 0">
                <summary>{{ __("Feeds") }}</summary>

                <div class="list vertical striped items-rounded">
                    <div v-for="feed in document.feeds" v-bind:key="feed.id">
                        <div class="list-item">
                            <div class="icons">
                                <img v-bind:src="feed.favicon" />
                            </div>
                            <div class="list-item-text">{{ feed.title }}</div>
                            <div class="badges">
                                <button
                                    class="success"
                                    v-if="feed.is_ignored"
                                    v-on:click="follow(feed)"
                                >
                                    <svg
                                        fill="currentColor"
                                        width="16"
                                        height="16"
                                        class="mr-1"
                                    >
                                        <use v-bind:xlink:href="icon('join')" />
                                    </svg>
                                    {{ __("Follow") }}
                                </button>
                                <button
                                    class="danger"
                                    v-if="!feed.is_ignored"
                                    v-on:click="ignore(feed)"
                                >
                                    <svg
                                        fill="currentColor"
                                        width="16"
                                        height="16"
                                        class="mr-1"
                                    >
                                        <use
                                            v-bind:xlink:href="icon('cancel')"
                                        />
                                    </svg>
                                    {{ __("Ignore") }}
                                </button>
                            </div>
                        </div>

                        <div
                            class="vertical list striped items-rounded compact mt-2 bg-gray-100 dark:bg-gray-800 rounded"
                        >
                            <div class="list-item" v-if="feed.description">
                                <div v-html="feed.description"></div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-title">
                                    {{ __("Real URL") }}
                                </div>
                                <div class="list-item-value">
                                    <div
                                        class="readable"
                                        v-html="feed.ascii_url"
                                    ></div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-title">
                                    {{ __("Date of document's last check") }}
                                </div>
                                <div class="list-item-value">
                                    <date-time
                                        v-bind:datetime="feed.checked_at"
                                        v-bind:calendar="true"
                                    ></date-time>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </details>

            <div
                class="mt-6"
                v-if="
                    selectedFolder.type !== 'unread_items' &&
                    selectedFolder.user_permissions.can_delete_document
                "
            >
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
import DateTime from "../DateTime";

export default {
    components: { DateTime },
    data: function () {
        return {
            dupplicateInFolders: [],
        };
    },
    computed: {
        ...mapGetters({
            document: "documents/selectedDocument",
            selectedFolder: "folders/selectedFolder",
            feeds: "feeds/feeds",
        }),

        /**
         * Return document's initial URL instead of the real URL, unless there
         * is not attached bookmark
         */
        url: function () {
            const self = this;

            return self.document.url;
        },
    },
    watch: {
        document: function (document) {
            const self = this;

            if (document && document.id) {
                self.load(document).then(function () {
                    self.findDupplicateInFolders();
                });
            }
        },
    },
    mounted: function () {
        const self = this;

        if (self.document && self.document.id) {
            self.load(self.document).then(function () {
                self.findDupplicateInFolders();
            });
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
         * Filters dupplicates provided by the document by excluding current
         * folder
         */
        findDupplicateInFolders: function () {
            const self = this;

            const dupplicates = collect(self.document.dupplicates)
                .reject((folder) => folder.id === self.selectedFolder.id)
                .all();

            self.dupplicateInFolders = dupplicates;
        },

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
