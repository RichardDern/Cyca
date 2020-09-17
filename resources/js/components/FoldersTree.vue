<template>
    <div id="folders-tree">
        <folder-item
            v-for="folder in folders"
            v-bind:key="folder.id"
            v-bind:folder="folder"
            v-on:selected-folder-changed="onSelectedFolderChanged"
            v-on:item-dropped="onItemDropped"
        ></folder-item>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    mounted: function () {
        const self = this;

        self.index().then(function () {
            self.$emit("folders-loaded");
            self.onSelectedFolderChanged(self.selectedFolder);
        });
    },
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            folders: "folders/folders",
            selectedFolder: "folders/selectedFolder",
        }),
    },
    /**
     * Methods
     */
    methods: {
        ...mapActions({
            index: "folders/index",
        }),

        /**
         * Folder has been selected
         */
        onSelectedFolderChanged: function (folder) {
            const self = this;

            self.$emit("selected-folder-changed", folder);
        },

        /**
         * Something had been dropped into a folder
         */
        onItemDropped: function (folder) {
            const self = this;

            self.$emit('item-dropped', folder);
        },
    },
};
</script>
