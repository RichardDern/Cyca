<template>
    <a
        class="list-item"
        v-bind:class="{'selected': is_selected}"
        v-bind:draggable="true"
        v-bind:href="url"
        v-on:click.meta.left.exact.stop.prevent="onAddToSelection"
        v-on:click.left.exact.stop.prevent="onClicked"
        v-on:click.middle.exact="incrementVisits({document: document, folder: selectedFolder})"
        v-on:dblclick="openDocument({document: document, folder: selectedFolder})"
        v-on:dragstart="onDragStart"
        v-on:dragend="onDragEnd"
        rel="noopener noreferrer"
    >
        <div class="list-item-label">
            <img v-bind:src="document.favicon" class="favicon" />
            <div class="truncate flex-grow py-0.5">{{ document.title }}</div>
        </div>
        <div class="badge" v-if="document.unread_feed_items_count > 0">{{ document.unread_feed_items_count }}</div>
    </a>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
    props: ["document"],
    computed: {
        ...mapGetters({
            selectedDocuments: "documents/selectedDocuments",
            selectedFolder: "folders/selectedFolder"
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
        url: function() {
            const self = this;

            if(self.document.bookmark.initial_url) {
                return self.document.bookmark.initial_url;
            }

            return self.document.url;
        }
    },
    methods: {
        ...mapActions({
            selectDocument: "documents/selectDocument",
            addDocumentToSelection: "documents/addDocumentToSelection",
            startDraggingDocuments: "documents/startDraggingDocuments",
            stopDraggingDocuments: "documents/stopDraggingDocuments",
            incrementVisits: "documents/incrementVisits",
            openDocument: "documents/openDocument",
        }),

        /**
         * Document has been clicked
         */
        onClicked: function() {
            const self = this;

            self.$emit('selected-documents-changed', [self.document]);
        },

        /**
         * Add this document to selection
         */
        onAddToSelection: function () {
            const self = this;

            let selectedDocuments = [...self.selectedDocuments];

            const index = selectedDocuments.findIndex(
                doc => doc.id === self.document.id
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
    },
};
</script>
