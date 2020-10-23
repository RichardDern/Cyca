require("./modules/bootstrap");
require("./modules/websockets");
require("./modules/components")("app");

import store from "./store";
import { mapGetters, mapActions, mapMutations } from "vuex";

const app = new Vue({
    el: "#app",
    store,
    data: function() {
        return {
            detailsViewComponent: null
        };
    },
    mounted: function() {
        const self = this;

        self.listenToBroadcast();
    },
    computed: {
        ...mapGetters({
            selectedFolder: "folders/selectedFolder",
            documents: "documents/documents",
            selectedDocuments: "documents/selectedDocuments",
            feedItems: "feedItems/feedItems",
            selectedFeedItems: "feedItems/selectedFeedItems",
            getUnreadItemsFolder: "folders/getUnreadItemsFolder"
        })
    },
    watch: {
        selectedFolder: function(oldFolder, newFolder) {
            const self = this;

            if (oldFolder && newFolder && oldFolder.id !== newFolder.id) {
                self.detailsViewComponent = "details-folder";
            }
        },
        selectedDocuments: function(documents) {
            const self = this;

            if (documents && documents.length > 0) {
                if (documents.length === 1) {
                    self.detailsViewComponent = "details-document";
                } else {
                    self.detailsViewComponent = "details-documents";
                }
            } else {
                self.detailsViewComponent = "details-folder";
            }
        },
        selectedFeedItems: function(feedItems) {
            const self = this;

            if (feedItems && feedItems.length > 0) {
                if (feedItems.length === 1) {
                    self.detailsViewComponent = "details-feed-item";
                } else {
                    //TODO: Handle multiple selected feed items ?
                    self.detailsViewComponent = null;
                }
            } else {
                if (
                    self.selectedDocuments &&
                    self.selectedDocuments.length > 0
                ) {
                    self.detailsViewComponent = "details-document";
                } else {
                    self.detailsViewComponent = "details-folder";
                }
            }
        }
    },
    methods: {
        ...mapActions({
            showFolder: "folders/show",
            indexFolders: "folders/index",
            selectDocuments: "documents/selectDocuments",
            indexDocuments: "documents/index",
            dropIntoFolder: "folders/dropIntoFolder",
            selectFeedItems: "feedItems/selectFeedItems",
            markFeedItemsAsRead: "feedItems/markAsRead",
            updateDocument: "documents/update",
            deleteDocuments: "documents/destroy",
            updateFolder: "folders/updateProperties"
        }),

        /**
         * Listen to broadcast events
         */
        listenToBroadcast: function() {
            const self = this;
            const userId = document
                .querySelector('meta[name="user-id"]')
                .getAttribute("content");

            window.Echo.private("App.Models.User." + userId).notification(
                notification => {
                    switch (notification.type) {
                        case "App\\Notifications\\UnreadItemsChanged":
                            for (var documentId in notification.documents) {
                                self.updateDocument({
                                    documentId: documentId,
                                    newProperties: {
                                        feed_item_states_count:
                                            notification.documents[documentId]
                                    }
                                });
                            }

                            for (var folderId in notification.folders) {
                                self.updateFolder({
                                    folderId: folderId,
                                    newProperties: {
                                        feed_item_states_count:
                                            notification.folders[folderId]
                                    }
                                });
                            }

                            self.updateFolder({
                                folderId: self.getUnreadItemsFolder.id,
                                newProperties: {
                                    feed_item_states_count: notification.total
                                }
                            });

                            self.showFolder();
                            break;
                        case "App\\Notifications\\DocumentUpdated":
                            self.updateDocument({
                                documentId: notification.document.id,
                                newProperties: notification.document
                            });
                            break;
                    }
                }
            );
        },

        /**
         * Folders tree has been loaded
         */
        onFoldersLoaded: function() {
            //
        },

        /**
         * User-action - Selected folder has changed
         * @param {*} folder
         */
        onSelectedFolderChanged: function(folder) {
            const self = this;

            self.showFolder(folder);

            self.detailsViewComponent = "details-folder";
        },

        /**
         * User-action - Something has been dropped into a folder
         */
        onItemDropped: function(folder) {
            const self = this;

            self.dropIntoFolder(folder);
        },

        /**
         * User-action - Selected documents has changed
         */
        onSelectedDocumentsChanged: function(documents) {
            const self = this;

            self.selectDocuments(documents);
        },

        /**
         * User-action - Refresh documents list after adding one
         */
        onDocumentAdded: function() {
            //
        },

        /**
         * User-action - Refresh documents list after deleting one (or more)
         */
        onDocumentsDeleted: function({ folder, documents }) {
            const self = this;

            self.deleteDocuments({
                documents: documents,
                folder: folder
            });
        },

        /**
         * User-action - Selected feed items has changed
         */
        onSelectedFeedItemsChanged: function(feedItems) {
            const self = this;

            self.selectFeedItems(feedItems);
        },

        /**
         * User-action - Feed items marked as read
         */
        onFeedItemsRead: function(data) {
            const self = this;

            self.markFeedItemsAsRead(data);
        }
    }
});
