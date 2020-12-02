require("./modules/bootstrap");
require("./modules/websockets");

import { createApp } from "vue";
import mixins from "./mixins";
import store from "./store";
import { mapGetters, mapActions } from "vuex";

import DetailsDocument from "./components/Details/DetailsDocument.vue";
import DetailsDocuments from "./components/Details/DetailsDocuments.vue";
import DetailsFeedItem from "./components/Details/DetailsFeedItem.vue";
import DetailsFolder from "./components/Details/DetailsFolder.vue";
import DocumentsList from "./components/DocumentsList.vue";
import FeedItemsList from "./components/FeedItemsList.vue";
import FoldersTree from "./components/FoldersTree.vue";

createApp({
    components: {
        DetailsDocument,
        DetailsDocuments,
        DetailsFeedItem,
        DetailsFolder,
        DocumentsList,
        FeedItemsList,
        FoldersTree,
    },
    data: function () {
        return {
            detailsViewComponent: null,
        };
    },
    mounted: function () {
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
            getUnreadItemsFolder: "folders/getUnreadItemsFolder",
            selectedGroup: "groups/selectedGroup",
        }),
    },
    watch: {
        selectedFolder: function (oldFolder, newFolder) {
            const self = this;

            if (newFolder) {
                self.detailsViewComponent = "details-folder";
            }
        },
        selectedDocuments: function (documents) {
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
        selectedFeedItems: function (feedItems) {
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
        },
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
            updateFolder: "folders/updateProperties",
            showGroup: "groups/show",
            updateGroup: "groups/updateProperties",
            resetUnreadCount: "folders/resetUnreadCount",
            updateUnreadFeedItemsCount: "feedItems/updateUnreadFeedItemsCount",
        }),

        /**
         * Listen to broadcast events
         */
        listenToBroadcast: function () {
            const self = this;
            const userId = document
                .querySelector('meta[name="user-id"]')
                .getAttribute("content");

            window.Echo.private("App.Models.User." + userId).notification(
                (notification) => {
                    switch (notification.type) {
                        case "App\\Notifications\\UnreadItemsChanged":
                            self.updateUnreadFeedItemsCount(notification);
                            self.showFolder({
                                deselectDocuments: false,
                                updateFeedItems: true,
                            });
                            break;
                        case "App\\Notifications\\DocumentUpdated":
                            self.updateDocument({
                                documentId: notification.document.id,
                                newProperties: notification.document,
                            });
                            break;
                    }
                }
            );
        },

        /**
         * User-action - Selected group has changed
         * @param {*} group
         */
        onSelectedGroupChanged: function (group) {
            const self = this;

            self.showGroup(group);
        },

        /**
         * Groups have been loaded
         */
        onGroupsLoaded: function () {
            //
        },

        /**
         * Folders tree has been loaded
         */
        onFoldersLoaded: function () {
            //
        },

        /**
         * User-action - Selected folder has changed
         * @param {*} folder
         */
        onSelectedFolderChanged: function (folder, group) {
            const self = this;

            if (!folder) {
                return;
            }

            if (group && group != self.selectedGroup.id) {
                self.showGroup(group, folder);
            } else {
                self.showFolder({ folder });
            }

            self.detailsViewComponent = "details-folder";
        },

        /**
         * User-action - Something has been dropped into a folder
         */
        onItemDropped: function (folder) {
            const self = this;

            self.dropIntoFolder(folder);
        },

        /**
         * User-action - Selected documents has changed
         */
        onSelectedDocumentsChanged: function (documents) {
            const self = this;

            self.selectDocuments({ documents: documents });

            document.getElementById("feed-items-list").scrollTop = 0;
        },

        /**
         * User-action - Refresh documents list after adding one
         */
        onDocumentAdded: function () {
            //
        },

        /**
         * User-action - Refresh documents list after deleting one (or more)
         */
        onDocumentsDeleted: function ({ folder, documents }) {
            const self = this;

            self.deleteDocuments({
                documents: documents,
                folder: folder,
            });
        },

        /**
         * User-action - Selected feed items has changed
         */
        onSelectedFeedItemsChanged: function (feedItems) {
            const self = this;

            self.selectFeedItems(feedItems);
        },

        /**
         * User-action - Feed items marked as read
         */
        onFeedItemsRead: function (data) {
            const self = this;

            self.markFeedItemsAsRead(data);
        },
    },
})
    .mixin(mixins)
    .use(store)
    .mount("#app");
