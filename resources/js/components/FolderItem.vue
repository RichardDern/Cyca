<template>
    <button
        class="list-item"
        v-bind:class="{'selected': folder.is_selected, 'dragged-over': is_dragged_over, 'cannot-drop': cannot_drop, 'deleted': folder.deleted_at}"
        v-bind:draggable="isDraggable"
        v-on:click.prevent="onClick"
        v-on:dragstart="onDragStart"
        v-on:dragend="onDragEnd"
        v-on:drop="onDrop"
        v-on:dragleave="onDragLeave"
        v-on:dragover="onDragOver"
        v-if="branchIsExpanded"
    >
        <div class="list-item-label" v-bind:style="{'padding-left': indent}">
            <span class="caret" v-if="folder.type === 'folder'">
                <svg
                    fill="currentColor"
                    width="16"
                    height="16"
                    v-on:click.capture.stop="toggleExpanded(folder)"
                    v-if="folder.type === 'folder' && folder.children_count > 0"
                >
                    <use v-bind:xlink:href="icon(expanderIcon)" />
                </svg>
            </span>
            <svg
                fill="currentColor"
                width="16"
                height="16"
                class="favicon"
                v-bind:class="folder.iconColor"
            >
                <use v-bind:xlink:href="icon(folder.icon)" />
            </svg>
            <div class="truncate flex-grow py-0.5">{{ folder.title }}</div>
        </div>
        <div
            class="badge"
            v-if="folder.unread_feed_items_count > 0"
        >{{ folder.unread_feed_items_count }}</div>
    </button>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    props: ["folder"],
    data: function () {
        return {
            is_dragged_over: false,
            cannot_drop: false,
        };
    },
    mounted: function () {
        const self = this;

        if (self.folder.is_selected) {
            self.$el.scrollIntoView();
        }
    },
    /**
     * Computed properties
     */
    computed: {
        ...mapGetters({
            folders: "folders/folders",
        }),
        /**
         * Folder's indentation
         */
        indent: function () {
            const self = this;
            var indent = self.folder.depth - 1;

            indent = indent > 0 ? indent : 0;

            return indent + "rem";
        },
        /**
         * Return a boolean value indicating if folder can be dragged
         */
        isDraggable: function () {
            const self = this;

            if (self.folder.deleted_at) {
                return false;
            }

            return self.folder.type === "folder";
        },
        /**
         * Return a boolean value indicating if content can be dropped into this
         * folder
         */
        canDrop: function () {
            const self = this;

            if (self.folder.deleted_at) {
                return false;
            }

            return self.folder.type === "folder" || self.folder.type === "root";
        },
        /**
         * Return a boolean value indicating if branch is expanded or collapsed
         */
        branchIsExpanded: function () {
            const self = this;
            var parentId = self.folder.parent_id;

            if (!parentId || !self.folders) {
                return true;
            }

            while (parentId != null) {
                var parentFolder = self.folders.find((f) => f.id === parentId);

                if (parentFolder) {
                    if (!parentFolder.is_expanded) {
                        return false;
                    }
                }

                parentId = parentFolder.parent_id;
            }

            return true;
        },
        /**
         * Icon adjacent to folder indicating its state (expanded/collapsed)
         */
        expanderIcon: function () {
            const self = this;

            if (self.folder.is_expanded) {
                return "expanded";
            }

            return "collapsed";
        },
    },
    /**
     * Methods
     */
    methods: {
        ...mapActions({
            startDraggingFolder: "folders/startDraggingFolder",
            stopDraggingFolder: "folders/stopDraggingFolder",
            dropIntoFolder: "folders/dropIntoFolder",
            toggleExpanded: "folders/toggleExpanded",
        }),
        /**
         * Dragging started
         */
        onDragStart: function (event) {
            const self = this;

            self.startDraggingFolder(self.folder);
        },
        /**
         * Dragging ended
         */
        onDragEnd: function () {
            const self = this;

            self.stopDraggingFolder();

            self.is_dragged_over = false;
        },
        /**
         * Dropping content in this folder
         */
        onDrop: function () {
            const self = this;

            self.is_dragged_over = false;

            self.$emit("item-dropped", self.folder);
        },
        /**
         * Dragging outside of this folder
         */
        onDragLeave: function () {
            const self = this;

            self.is_dragged_over = false;
        },
        /**
         * Dragging over this folder
         */
        onDragOver: function (event) {
            const self = this;

            self.is_dragged_over = true;

            if (self.canDrop) {
                event.preventDefault();

                self.cannot_drop = false;
            } else {
                self.cannot_drop = true;
            }
        },
        /**
         * Selected folder changed
         */
        onClick: function () {
            const self = this;

            switch (self.folder.type) {
                case "account":
                    window.location.href = route("account");
                    break;
                case "logout":
                    document.getElementById("logout-form").submit();
                    break;
                default:
                    self.$emit("selected-folder-changed", self.folder);
                    break;
            }
        },
    },
};
</script>
