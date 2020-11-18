<template>
    <article>
        <h1>
            <div class="title">
                <svg
                    fill="currentColor"
                    width="16"
                    height="16"
                    class="mr-1"
                    v-bind:class="folder.iconColor"
                >
                    <use v-bind:xlink:href="icon(folder.icon)" />
                </svg>
                <span>{{ folder.title }}</span>
                <div
                    class="badge article"
                    v-if="folder.feed_item_states_count > 0"
                >
                    {{ folder.feed_item_states_count }}
                </div>
            </div>
            <button
                v-if="folder.feed_item_states_count > 0"
                class="button info"
                v-on:click="onMarkAsReadClicked"
            >
                <svg fill="currentColor" width="16" height="16" class="mr-1">
                    <use v-bind:xlink:href="icon('unread_items')" />
                </svg>
                {{ __("Mark as read") }}
            </button>
        </h1>

        <div class="body">
            <form
                v-bind:action="route('folder.update', folder)"
                v-if="can('can_update_folder') && folder.type === 'folder'"
                v-on:submit.prevent="onUpdateFolder"
            >
                <div class="form-group items-stretched">
                    <input
                        type="text"
                        name="title"
                        v-bind:value="folder.title"
                        v-on:input="updateFolderTitle = $event.target.value"
                    />
                    <button type="submit" class="success ml-2">
                        <svg
                            fill="currentColor"
                            width="16"
                            height="16"
                            class="mr-1"
                        >
                            <use v-bind:xlink:href="icon('update')" />
                        </svg>
                        {{ __("Update folder") }}
                    </button>
                </div>
            </form>

            <form
                v-bind:action="route('folder.store')"
                v-on:submit.prevent="onAddFolder"
                v-if="can('can_create_folder')"
            >
                <div class="form-group items-stretched">
                    <input type="text" v-model="addFolderTitle" />
                    <button type="submit" class="success ml-2">
                        <svg
                            fill="currentColor"
                            width="16"
                            height="16"
                            class="mr-1"
                        >
                            <use v-bind:xlink:href="icon('add')" />
                        </svg>
                        {{ __("Add folder") }}
                    </button>
                </div>
            </form>

            <form
                v-bind:action="route('document.store')"
                v-on:submit.prevent="onAddDocument"
                v-if="can('can_create_document')"
            >
                <div class="form-group items-stretched">
                    <input type="url" v-model="addDocumentUrl" />
                    <button type="submit" class="success ml-2">
                        <svg
                            fill="currentColor"
                            width="16"
                            height="16"
                            class="mr-1"
                        >
                            <use v-bind:xlink:href="icon('add')" />
                        </svg>
                        {{ __("Add document") }}
                    </button>
                </div>
            </form>

            <div class="mt-6" v-if="can('can_delete_folder')">
                <button class="danger" v-on:click="onDeleteFolder">
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

            <details
                v-if="
                    can('can_change_permissions') &&
                    (folder.type === 'root' || folder.type === 'folder')
                "
                class="feeds-list mt-4"
            >
                <summary>
                    {{ __("Users without explicit permissions can") }}:
                </summary>

                <div class="grid grid-cols-3">
                    <permission-box
                        v-bind:text="__('Create folder')"
                        ability="can_create_folder"
                        v-bind:folder="folder"
                    ></permission-box>
                    <permission-box
                        v-bind:text="__('Update folder')"
                        ability="can_update_folder"
                        v-bind:folder="folder"
                    ></permission-box>
                    <permission-box
                        v-bind:text="__('Delete folder')"
                        ability="can_delete_folder"
                        v-bind:folder="folder"
                    ></permission-box>
                    <permission-box
                        v-bind:text="__('Create document')"
                        ability="can_create_document"
                        v-bind:folder="folder"
                    ></permission-box>
                    <permission-box
                        v-bind:text="__('Delete document')"
                        ability="can_delete_document"
                        v-bind:folder="folder"
                    ></permission-box>
                </div>
            </details>
        </div>
    </article>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
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
                self.updateFolderTitle = self.folder.title;
            }
        },
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
