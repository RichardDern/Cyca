<template>
    <div class="vertical list spaced striped items-rounded">
        <document-item
            v-for="document in sortedList"
            v-bind:key="document.id"
            v-bind:document="document"
            v-on:selected-documents-changed="onSelectedDocumentsChanged"
        ></document-item>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import DocumentItem from "./DocumentItem";

export default {
    components: { DocumentItem },
    data: function () {
        return {
            selectedFeeds: [],
        };
    },
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            documents: "documents/documents",
            selectedFolder: "folders/selectedFolder",
        }),
        sortedList: function () {
            const self = this;
            let collection = collect(self.documents).sortBy("title");

            if (
                self.selectedFolder &&
                self.selectedFolder.type === "unread_items"
            ) {
                collection = collection.where("feed_item_states_count", ">", 0);
            }

            return collection.all();
        },
    },
    /**
     * Methods
     */
    methods: {
        /**
         * Selected documents have changed
         */
        onSelectedDocumentsChanged: function (documents) {
            const self = this;

            self.$emit("selected-documents-changed", documents);
        },
    },
};
</script>
