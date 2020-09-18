require("./bootstrap");

import store from "./store";
import { mapGetters, mapActions } from "vuex";

if (document.getElementById("app")) {
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
                feedItems: "feedItems/feedItems"
            })
        },
        methods: {
            ...mapActions({
                showFolder: "folders/show",
                indexFolders: "folders/index",
                indexDocuments: "documents/index",
                selectDocuments: "documents/selectDocuments",
                dropIntoFolder: "folders/dropIntoFolder",
                indexFeedItems: "feedItems/index",
                selectFeedItems: "feedItems/selectFeedItems",
                markFeedItemsAsRead: "feedItems/markAsRead",
                updateDocument: "documents/update",
                deleteDocuments: "documents/destroy"
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
                                self.indexFolders().then(function() {
                                    self.indexDocuments();
                                });
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
             * Selected folder has changed
             * @param {*} folder
             */
            onSelectedFolderChanged: function(folder) {
                const self = this;

                self.detailsViewComponent = "details-folder";

                self.selectDocuments([]);
                self.showFolder(folder).then(function() {
                    self.indexDocuments().then(function() {
                        self.selectFeedItems();
                        self.loadFeedItems();
                    });
                });
            },

            /**
             * Something has been dropped into a folder
             */
            onItemDropped: function(folder) {
                const self = this;

                self.dropIntoFolder(folder).then(function() {
                    self.indexFolders().then(function() {
                        self.indexDocuments();
                    });
                });
            },

            /**
             * Selected documents has changed
             */
            onSelectedDocumentsChanged: function(documents) {
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

                self.selectDocuments(documents).then(function() {
                    self.selectFeedItems();
                    self.loadFeedItems(documents);
                });
            },

            /**
             * Refresh documents list after adding one
             */
            onDocumentAdded: function() {
                const self = this;

                self.indexDocuments();
            },

            /**
             * Refresh documents list after deleting one (or more)
             */
            onDocumentsDeleted: function({ folder, documents }) {
                const self = this;

                self.onSelectedDocumentsChanged([]);

                self.deleteDocuments({
                    documents: documents,
                    folder: folder
                }).then(function() {
                    self.indexFolders().then(function() {
                        self.indexDocuments();
                    });
                });
            },

            /**
             * Initial loading of feed items
             */
            loadFeedItems: async function(documents) {
                const self = this;

                if (!documents) {
                    documents = self.documents;
                }

                const feeds = collect(documents)
                    .pluck("feeds")
                    .flatten(1)
                    .pluck("id")
                    .all();

                await self.indexFeedItems(feeds);
            },

            /**
             * Selected feed items has changed
             */
            onSelectedFeedItemsChanged: function(feedItems) {
                const self = this;

                self.selectFeedItems(feedItems).then(function() {
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
                });
            },

            /**
             * Feed items marked as read
             */
            onFeedItemsRead: function(data) {
                const self = this;

                self.markFeedItemsAsRead(data).then(function() {
                    if ("folders" in data) {
                        self.indexFolders().then(function() {
                            self.onSelectedFolderChanged(self.selectedFolder);
                        });
                    } else if ("documents" in data) {
                        self.indexFolders().then(function() {
                            self.indexDocuments().then(function() {
                                if (
                                    self.selectedFolder.type ===
                                    "unread_feed_items"
                                ) {
                                    self.onSelectedFolderChanged(
                                        self.selectedFolder
                                    );
                                } else {
                                    let selectedDocuments =
                                        self.selectedDocuments;

                                    if (!selectedDocuments) {
                                        selectedDocuments = self.documents;
                                    }

                                    self.onSelectedDocumentsChanged(
                                        selectedDocuments
                                    );
                                }
                            });
                        });
                    } else if ("feed_items" in data) {
                        self.indexFolders().then(function() {
                            self.indexDocuments().then(function() {
                                let selectedDocuments = self.selectedDocuments;

                                if (!selectedDocuments) {
                                    selectedDocuments = self.documents;
                                }

                                self.loadFeedItems(selectedDocuments).then(
                                    function() {
                                        if (
                                            self.feedItems &&
                                            self.feedItems.length > 0
                                        ) {
                                            self.onSelectedFeedItemsChanged(
                                                collect(self.feedItems).first()
                                            );
                                        } else {
                                            self.onSelectedFeedItemsChanged();
                                        }
                                    }
                                );
                            });
                        });
                    }
                });
            }
        }
    });
}
