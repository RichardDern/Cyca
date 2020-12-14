<template>
    <div class="flex flex-col">
        <div
            class="list vertical items-rounded spaced compact striped flex-none overflow-auto max-h-36 bg-gray-50 dark:bg-gray-800"
        >
            <group-item
                v-for="group in sortedGroups"
                v-bind:key="group.id"
                v-bind:group="group"
                v-on:selected-group-changed="onSelectedGroupChanged"
            ></group-item>
        </div>
        <div
            class="list vertical items-rounded overflow-auto flex-grow compact"
        >
            <folder-item
                v-for="folder in folders"
                v-bind:key="folder.id"
                v-bind:folder="folder"
                v-on:selected-folder-changed="onSelectedFolderChanged"
                v-on:item-dropped="onItemDropped"
            ></folder-item>
        </div>
        <div
            class="list vertical items-rounded spaced compact striped flex-none bg-gray-50 dark:bg-gray-800"
        >
            <a v-bind:href="route('account')" class="list-item">
                <div class="icons">
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="text-green-500"
                    >
                        <use v-bind:xlink:href="icon('account')" />
                    </svg>
                </div>
                <div class="list-item-text">
                    {{ __("My account") }}
                </div>
            </a>
            <a href="#" class="list-item" v-on:click="logout">
                <div class="icons">
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="text-red-500"
                    >
                        <use v-bind:xlink:href="icon('logout')" />
                    </svg>
                </div>
                <div class="list-item-text">
                    {{ __("Logout") }}
                </div>
            </a>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import GroupItem from "./GroupItem.vue";
import FolderItem from "./FolderItem.vue";

export default {
    components: { GroupItem, FolderItem },
    mounted: function () {
        const self = this;

        self.indexGroups().then(function () {
            self.showGroup({});
        });
    },
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            folders: "folders/folders",
            groups: "groups/groups",
        }),
        csrf: function () {
            return document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
        },
        sortedGroups: function () {
            return collect(this.groups).sortBy("position");
        },
    },
    /**
     * Methods
     */
    methods: {
        ...mapActions({
            indexGroups: "groups/indexActive",
            showGroup: "groups/show",
        }),

        onSelectedGroupChanged: function (group) {
            const self = this;

            self.$emit("selected-group-changed", group);
        },

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

            self.$emit("item-dropped", folder);
        },

        logout: function () {
            document.forms.logout_form.submit();
        },
    },
};
</script>
