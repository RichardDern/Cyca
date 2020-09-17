<template>
    <div id="documents-list">
        <document-item
            v-for="document in documents"
            v-bind:key="document.id"
            v-bind:document="document"
            v-on:selected-documents-changed="onSelectedDocumentsChanged"
        ></document-item>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
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
            documents: "documents/documents"
        })
    },
    /**
     * Methods
     */
    methods: {
        /**
         * Selected documents have changed
         */
        onSelectedDocumentsChanged: function(documents) {
            const self = this;

            self.$emit("selected-documents-changed", documents);
        }
    },
};
</script>
