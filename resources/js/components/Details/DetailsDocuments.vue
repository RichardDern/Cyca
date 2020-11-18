<template>
    <article>
        <h1>
            <span></span>
            <div class="flex items-center">
                <button
                    v-if="totalUnreadFeedItemsCount > 0"
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
                <button
                    class="info ml-2"
                    v-on:click.left.stop.prevent="onOpenClicked"
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
                </button>
            </div>
        </h1>

        <div class="body">
            <img
                v-for="document in documents"
                v-bind:key="document.id"
                v-bind:title="document.title"
                v-bind:src="document.favicon"
                class="favicon inline mr-1 mb-1"
            />
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
            documents: "documents/selectedDocuments",
            selectedFolder: "folders/selectedFolder",
        }),
        totalUnreadFeedItemsCount: function () {
            const self = this;

            return collect(self.documents).sum("feed_item_states_count");
        },
    },
    methods: {
        ...mapActions({
            openDocument: "documents/openDocument",
        }),

        /**
         * Click on the "Open" button
         */
        onOpenClicked: function () {
            const self = this;

            self.documents.forEach(function (document) {
                self.openDocument({
                    document: document,
                    folder: self.selectedFolder,
                });
            });
        },

        /**
         * Mark as read button clicked
         */
        onMarkAsReadClicked: function () {
            const self = this;

            self.$emit("feeditems-read", {
                documents: collect(self.documents).pluck("id").all(),
            });
        },

        /**
         * Delete button clicked
         */
        onDeleteDocument: function () {
            const self = this;

            self.$emit("documents-deleted", {
                folder: self.selectedFolder,
                documents: self.documents,
            });
        },
    },
};
</script>
