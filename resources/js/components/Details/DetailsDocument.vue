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
                    class="info"
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
                v-if="document.description"
                v-html="document.description"
            ></div>

            <stateful-details name="document_details">
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
                <div class="list-item" v-if="document.meta_data">
                    <stateful-details name="document_meta_data_details">
                        <summary>{{ __("Metadata") }}</summary>
                        <meta-data-browser
                            v-bind:meta-data="document.meta_data"
                        ></meta-data-browser>
                    </stateful-details>
                </div>
                <div class="list-item">
                    <stateful-details name="http_response_details">
                        <summary>{{ __("HTTP response") }}</summary>
                        <div
                            class="vertical list striped items-rounded compact"
                        >
                            <div class="list-item">
                                <div class="list-item-title">
                                    {{ __("HTTP Status Code") }}
                                </div>
                                <div
                                    class="list-item-value flex items-center space-x-2"
                                    v-bind:class="statusClass"
                                >
                                    <span v-if="statusIcon">
                                        <svg
                                            fill="currentColor"
                                            width="16"
                                            height="16"
                                        >
                                            <use
                                                v-bind:xlink:href="
                                                    icon(statusIcon)
                                                "
                                            />
                                        </svg>
                                    </span>
                                    <span>{{ document.http_status_code }}</span>
                                    <span>{{ document.http_status_text }}</span>
                                </div>
                            </div>
                            <div class="list-item" v-if="document.mimetype">
                                <div class="list-item-title">
                                    {{ __("MIME type") }}
                                </div>
                                <div class="list-item-value">
                                    {{ document.mimetype }}
                                </div>
                            </div>
                        </div>
                    </stateful-details>
                </div>
            </stateful-details>

            <stateful-details
                name="feeds_details"
                v-if="document.feeds.length > 0"
            >
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
                                    >
                                        <use v-bind:xlink:href="icon('join')" />
                                    </svg>
                                    <span>
                                        {{ __("Follow") }}
                                    </span>
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
                                    >
                                        <use
                                            v-bind:xlink:href="icon('cancel')"
                                        />
                                    </svg>
                                    <span>
                                        {{ __("Ignore") }}
                                    </span>
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
            </stateful-details>

            <div
                class="mt-6"
                v-if="
                    selectedFolder.type !== 'unread_items' &&
                    selectedFolder.user_permissions.can_delete_document
                "
            >
                <button class="danger" v-on:click="onDeleteDocument">
                    <svg fill="currentColor" width="16" height="16">
                        <use v-bind:xlink:href="icon('trash')" />
                    </svg>
                    <span>
                        {{ __("Delete") }}
                    </span>
                </button>
            </div>
        </div>
    </article>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import DateTime from "../DateTime";
import StatefulDetails from "../StatefulDetails.vue";
import MetaDataBrowser from "../MetaDataBrowser.vue";

export default {
    components: { DateTime, StatefulDetails, MetaDataBrowser },
    data: function () {
        return {
            dupplicateInFolders: [],
            localStorage: localStorage,
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

        statusClass: function () {
            const code = this.document.http_status_code;
            let className = null;

            if (code === 0) {
                className = "http-status-general-error";
            } else if (code >= 100 && code <= 199) {
                className = "http-status-info";
            } else if (code >= 200 && code <= 299) {
                className = "http-status-success";
            } else if (code >= 300 && code <= 399) {
                className = "http-status-redirection";
            } else if (code >= 400 && code <= 499) {
                className = "http-status-client-error";
            } else if (code >= 500 && code <= 599) {
                className = "http-status-server-error";
            }

            return className;
        },

        statusIcon: function () {
            const code = this.document.http_status_code;
            let icon = null;

            if (code === 0) {
                icon = "error";
            } else if (code >= 100 && code <= 199) {
                icon = "info";
            } else if (code >= 200 && code <= 299) {
                icon = "success";
            } else if (code >= 300 && code <= 399) {
                icon = "redirect";
            } else if (code >= 400 && code <= 499) {
                icon = "warning";
            } else if (code >= 500 && code <= 599) {
                icon = "error";
            }

            return icon;
        },

        metaData: function () {
            return collect(this.document.meta_data);
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
