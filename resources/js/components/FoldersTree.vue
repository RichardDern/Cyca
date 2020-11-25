<template>
    <div id="folders-tree">
        <div id="tree-top" v-if="sortedGroups.count() > 1">
            <group-item
                v-for="group in sortedGroups"
                v-bind:key="group.id"
                v-bind:group="group"
                v-on:selected-group-changed="onSelectedGroupChanged"
            ></group-item>
        </div>
        <div id="tree" class="flex-grow">
            <folder-item
                v-for="folder in folders"
                v-bind:key="folder.id"
                v-bind:folder="folder"
                v-on:selected-folder-changed="onSelectedFolderChanged"
                v-on:item-dropped="onItemDropped"
            ></folder-item>
        </div>
        <div id="tree-bottom">
            <a class="list-item" v-bind:href="route('account')">
                <div class="list-item-label pl-0">
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="favicon folder-account"
                    >
                        <use v-bind:xlink:href="icon('account')" />
                    </svg>
                    <div class="truncate flex-grow py-0.5">
                        {{ __("My account") }}
                    </div>
                </div>
            </a>
            <form
                id="logout-form"
                v-bind:action="route('logout')"
                method="POST"
            >
                <input type="hidden" name="_token" v-bind:value="csrf" />
                <button type="submit" class="list-item">
                    <div class="list-item-label pl-0">
                        <svg
                            fill="currentColor"
                            width="16"
                            height="16"
                            class="favicon folder-logout"
                        >
                            <use v-bind:xlink:href="icon('logout')" />
                        </svg>
                        <div class="truncate flex-grow py-0.5">
                            {{ __("Logout") }}
                        </div>
                    </div>
                </button>
            </form>
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
            self.$emit("groups-loaded");

            self.showGroup();
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
    },
};
</script>
