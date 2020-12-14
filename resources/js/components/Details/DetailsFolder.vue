<template>
    <article v-if="folder">
        <header>
            <div class="icons">
                <svg
                    fill="currentColor"
                    width="16"
                    height="16"
                    v-bind:class="folder.iconColor"
                >
                    <use v-bind:xlink:href="icon(folder.icon)" />
                </svg>
            </div>
            <h1>{{ folder.title }}</h1>
            <div class="badges">
                <div
                    class="badge default"
                    v-if="folder.feed_item_states_count > 0"
                >
                    {{ folder.feed_item_states_count }}
                </div>
            </div>
            <div class="tools">
                <button
                    v-if="folder.feed_item_states_count > 0"
                    class="info"
                    v-on:click="onMarkAsReadClicked"
                >
                    <svg fill="currentColor" width="16" height="16">
                        <use v-bind:xlink:href="icon('unread_items')" />
                    </svg>
                    <span>
                        {{ __("Mark as read") }}
                    </span>
                </button>
            </div>
        </header>

        <div class="body">
            <form
                v-bind:action="route('folder.update', folder)"
                v-if="can('can_update_folder') && folder.type === 'folder'"
                v-on:submit.prevent="onUpdateFolder"
            >
                <div class="input-group">
                    <input
                        type="text"
                        name="title"
                        v-bind:value="folder.title"
                        v-on:input="updateFolderTitle = $event.target.value"
                    />
                    <button type="submit" class="success">
                        <svg fill="currentColor" width="16" height="16">
                            <use v-bind:xlink:href="icon('update')" />
                        </svg>
                        <span>
                            {{ __("Update folder") }}
                        </span>
                    </button>
                </div>
            </form>

            <form
                v-bind:action="route('folder.store')"
                v-on:submit.prevent="onAddFolder"
                v-if="can('can_create_folder')"
            >
                <div class="input-group">
                    <input type="text" v-model="addFolderTitle" />
                    <button type="submit" class="success">
                        <svg fill="currentColor" width="16" height="16">
                            <use v-bind:xlink:href="icon('add')" />
                        </svg>
                        <span>
                            {{ __("Add folder") }}
                        </span>
                    </button>
                </div>
            </form>

            <form
                v-bind:action="route('document.store')"
                v-on:submit.prevent="onAddDocument"
                v-if="can('can_create_document')"
                class="mb-6"
            >
                <div class="input-group">
                    <input type="url" v-model="addDocumentUrl" />
                    <button type="submit" class="success">
                        <svg fill="currentColor" width="16" height="16">
                            <use v-bind:xlink:href="icon('add')" />
                        </svg>
                        <span>
                            {{ __("Add document") }}
                        </span>
                    </button>
                </div>
            </form>

            <div
                v-if="folder.group && folder.group.active_users_count > 1"
                class="mb-6"
            >
                <default-folder-permissions
                    v-bind:folder="folder"
                    v-if="
                        can('can_change_permissions') &&
                        (folder.type === 'root' || folder.type === 'folder')
                    "
                ></default-folder-permissions>

                <per-user-folder-permissions
                    v-bind:folder="folder"
                    v-if="
                        can('can_change_permissions') &&
                        (folder.type === 'root' || folder.type === 'folder')
                    "
                ></per-user-folder-permissions>
            </div>

            <div v-if="can('can_delete_folder')">
                <button class="danger" v-on:click="onDeleteFolder">
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
import { mapActions, mapGetters } from "vuex";
import DefaultFolderPermissions from "./DefaultFolderPermissions.vue";
import PerUserFolderPermissions from "./PerUserFolderPermissions.vue";

export default {
    components: {
        DefaultFolderPermissions,
        PerUserFolderPermissions,
    },
    data: function () {
        return {
            updateFolderTitle: null,
            addFolderTitle: null,
            addDocumentUrl: null,
        };
    },
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            group: "groups/selectedGroup",
            folder: "folders/selectedFolder",
        }),
    },
    /**
     * Watchers
     */
    watch: {
        folder: function () {
            const self = this;

            if (self.folder) {
                self.loadDetails(self.folder).then(function () {
                    self.$forceUpdate();
                });
            }
        },
    },
    mounted: function () {
        const self = this;

        if (self.folder) {
            self.loadDetails(self.folder).then(function () {
                self.$forceUpdate();
            });
        }
    },
    /**
     * Methods
     */
    methods: {
        ...mapActions({
            update: "folders/update",
            updateProperties: "folders/updateProperties",
            updatePermission: "folders/updatePermission",
            store: "folders/store",
            destroy: "folders/destroy",
            storeDocument: "documents/store",
            index: "folders/index",
            loadDetails: "folders/loadDetails",
        }),

        /**
         * Mark as read button clicked
         */
        onMarkAsReadClicked: function () {
            const self = this;

            self.$emit("feeditems-read", {
                folders: [self.folder.id],
            });
        },

        /**
         * Update folder form submitted
         */
        onUpdateFolder: function () {
            const self = this;

            if (
                !self.updateFolderTitle ||
                self.updateFolderTitle === self.folder.title
            ) {
                return;
            }

            self.update({
                folder: self.folder,
                newProperties: {
                    ...self.folder,
                    ...{
                        title: self.updateFolderTitle,
                    },
                },
            });
        },
        /**
         * Add folder form submitted
         */
        onAddFolder: function () {
            const self = this;

            if (!self.addFolderTitle) {
                return;
            }

            self.store({
                group_id: self.group.id,
                parent_id: self.folder.id,
                title: self.addFolderTitle,
            })
                .then(function () {
                    self.addFolderTitle = null;
                })
                .catch((error) => console.error(error));
        },
        /**
         * Delete folder button clicked
         */
        onDeleteFolder: function () {
            const self = this;

            self.destroy(self.folder);
        },
        /**
         * Add document form submitted
         */
        onAddDocument: function () {
            const self = this;

            if (!self.addDocumentUrl) {
                return;
            }

            self.storeDocument({
                group_id: self.group.id,
                folder_id: self.folder.id,
                url: self.addDocumentUrl,
            }).then(function () {
                self.addDocumentUrl = null;
                self.$emit("document-added");
            });
        },

        can: function (permission) {
            const self = this;

            if (
                "user_permissions" in self.folder &&
                permission in self.folder.user_permissions
            ) {
                return self.folder.user_permissions[permission];
            }

            return false;
        },
    },
};
</script>
