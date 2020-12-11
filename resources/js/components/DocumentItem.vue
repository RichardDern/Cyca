<template>
    <a
        class="list-item"
        v-bind:class="{ selected: is_selected }"
        v-bind:draggable="true"
        v-bind:href="url"
        v-on:click.meta.left.exact.prevent.stop="onAddToSelection"
        v-on:click.left.exact.prevent.stop="onClicked"
        v-on:click.middle.exact="
            incrementVisits({ document: document, folder: selectedFolder })
        "
        v-on:dblclick="
            openDocument({ document: document, folder: selectedFolder })
        "
        v-on:dragstart="onDragStart"
        v-on:dragend="onDragEnd"
        rel="noopener noreferrer"
    >
        <div class="icons">
            <img v-bind:src="document.favicon" />
        </div>
        <div class="list-item-text">{{ document.title }}</div>
        <div class="badges">
            <div
                v-if="
                    document.http_status_code < 200 ||
                    document.http_status_code > 299
                "
                v-bind:class="statusClass"
                v-bind:title="document.http_status_text"
            >
                <svg fill="currentColor" width="16" height="16">
                    <use v-bind:xlink:href="icon(statusIcon)" />
                </svg>
            </div>
            <div
                class="badge default"
                v-if="document.feed_item_states_count > 0"
            >
                <span v-if="document.has_new_unread_items">
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="text-blue-300"
                    >
                        <use v-bind:xlink:href="icon('update')" />
                    </svg>
                </span>
                {{ document.feed_item_states_count }}
            </div>
        </div>
    </a>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
    props: ["document"],
    data: function () {
        return {
            enableEnsureVisible: false,
        };
    },
    computed: {
        ...mapGetters({
            selectedDocuments: "documents/selectedDocuments",
            selectedFolder: "folders/selectedFolder",
        }),

        /**
         * Return a boolean value indicating if this document belongs to
         * currently selected documents
         */
        is_selected: function () {
            const self = this;

            return (
                self.selectedDocuments &&
                self.selectedDocuments.find((d) => d.id === self.document.id)
            );
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
    },
    methods: {
        ...mapActions({
            startDraggingDocuments: "documents/startDraggingDocuments",
            stopDraggingDocuments: "documents/stopDraggingDocuments",
            incrementVisits: "documents/incrementVisits",
            openDocument: "documents/openDocument",
        }),

        /**
         * Document has been clicked
         */
        onClicked: function () {
            const self = this;

            self.$emit("selected-documents-changed", [self.document]);
        },

        /**
         * Add this document to selection
         */
        onAddToSelection: function () {
            const self = this;

            let selectedDocuments = [...self.selectedDocuments];

            const index = selectedDocuments.findIndex(
                (doc) => doc.id === self.document.id
            );

            if (index === -1) {
                selectedDocuments.push(self.document);
            } else {
                selectedDocuments.splice(index, 1);
            }

            self.$emit("selected-documents-changed", selectedDocuments);
        },

        /**
         * Begin drag'n'drop
         */
        onDragStart: function () {
            const self = this;

            var documents = [self.document];

            if (self.selectedDocuments && self.selectedDocuments.length > 0) {
                documents = self.selectedDocuments;
            }

            self.startDraggingDocuments(documents);
        },

        /**
         * Stop drag'n'drop
         */
        onDragEnd: function () {
            const self = this;

            self.stopDraggingDocuments();
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
