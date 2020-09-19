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
                    <use v-bind:xlink:href="'/images/icons.svg#' + folder.icon" />
                </svg>
                <span>{{ folder.title }}</span>
            </div>
            <button
                v-if="folder.unread_feed_items_count > 0"
                class="button info"
                v-on:click="onMarkAsReadClicked"
            >
                <svg fill="currentColor" width="16" height="16" class="mr-1">
                    <use xlink:href="/images/icons.svg#unread_items" />
                </svg>
                {{ __("Mark as read") }}
            </button>
        </h1>

        <div class="body">
            <form
                v-bind:action="route('folder.update', folder)"
                v-if="folder.type === 'folder' && !folder.deleted_at"
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
                        <svg fill="currentColor" width="16" height="16" class="mr-1">
                            <use xlink:href="/images/icons.svg#update" />
                        </svg>
                        {{ __("Update folder") }}
                    </button>
                </div>
            </form>

            <form
                v-bind:action="route('folder.store')"
                v-on:submit.prevent="onAddFolder"
                v-if="(folder.type === 'folder' || folder.type === 'root') && !folder.deleted_at"
            >
                <div class="form-group items-stretched">
                    <input type="text" v-model="addFolderTitle" />
                    <button type="submit" class="success ml-2">
                        <svg fill="currentColor" width="16" height="16" class="mr-1">
                            <use xlink:href="/images/icons.svg#add" />
                        </svg>
                        {{ __("Add folder") }}
                    </button>
                </div>
            </form>

            <form
                v-bind:action="route('document.store')"
                v-on:submit.prevent="onAddDocument"
                v-if="(folder.type === 'folder' || folder.type === 'root') && !folder.deleted_at"
            >
                <div class="form-group items-stretched">
                    <input type="url" v-model="addDocumentUrl" />
                    <button type="submit" class="success ml-2">
                        <svg fill="currentColor" width="16" height="16" class="mr-1">
                            <use xlink:href="/images/icons.svg#add" />
                        </svg>
                        {{ __("Add document") }}
                    </button>
                </div>
            </form>

            <div class="mt-6" v-if="folder.type === 'folder'">
                <button class="danger" v-on:click="onDeleteFolder">
                    <svg fill="currentColor" width="16" height="16" class="mr-1">
                        <use xlink:href="/images/icons.svg#trash" />
                    </svg>
                    {{ __("Delete") }}
                </button>
            </div>
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
            store: "folders/store",
            destroy: "folders/destroy",
            storeDocument: "documents/store",
            index: "folders/index",
        }),

        /**
         * Mark as read button clicked
         */
        onMarkAsReadClicked: function () {
            const self = this;

            self.$emit('feeditems-read', {
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
                parent_id: self.folder.id,
                title: self.addFolderTitle,
            }).then(function () {
                self.addFolderTitle = null;
            });
        },
        /**
         * Delete folder button clicked
         */
        onDeleteFolder: function () {
            const self = this;

            self.destroy(self.folder).then(function () {
                self.index();
            });
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
                folder_id: self.folder.id,
                url: self.addDocumentUrl,
            }).then(function () {
                self.addDocumentUrl = null;
                self.$emit('document-added');
            });
        },
    },
};
</script>
