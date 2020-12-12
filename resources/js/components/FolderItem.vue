<template>
    <a
        class="list-item compact"
        v-bind:class="{
            selected: folder.is_selected,
            'dragged-over': is_dragged_over,
            'cannot-drop': is_dragged_over && cannot_drop,
            deleted: folder.deleted_at,
        }"
        v-bind:href="route('folder.show', folder)"
        v-bind:draggable="isDraggable"
        v-on:click.stop.prevent="onClick"
        v-on:dragstart="onDragStart"
        v-on:dragend="onDragEnd"
        v-on:drop="onDrop"
        v-on:dragleave="onDragLeave"
        v-on:dragover="onDragOver"
        v-if="branchIsExpanded"
        v-bind:style="'padding-left:' + indent"
    >
        <div class="icons">
            <span class="caret">
                <svg
                    fill="currentColor"
                    width="16"
                    height="16"
                    v-on:mouseup.capture.stop="onToggleExpandedClicked"
                    v-on:mousedown.capture.stop="startTimer"
                    v-if="folder.children_count > 0"
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
        </div>
        <div class="list-item-text">{{ folder.title }}</div>
        <div class="badges">
            <div class="badge default" v-if="folder.feed_item_states_count > 0">
                <span v-if="folder.has_new_unread_items">
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="text-blue-300"
                    >
                        <use v-bind:xlink:href="icon('update')" />
                    </svg>
                </span>
                {{ folder.feed_item_states_count }}
            </div>
        </div>
    </a>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    props: ["folder"],
    data: function () {
        return {
            is_dragged_over: false,
            cannot_drop: false,
            timer: null,
            longClick: false,
            enableEnsureVisible: false,
        };
    },
    mounted: function () {
        const self = this;

        if (self.folder.is_selected) {
            self.enableEnsureVisible = true;
            self.ensureVisible();
            self.enableEnsureVisible = false;
        }

        self.$watch(
            () => self.folder.is_selected,
            function (value) {
                if (value) {
                    if (self.folder.type === "unread_items") {
                        self.enableEnsureVisible = true;
                    }

                    self.ensureVisible();
                    self.enableEnsureVisible = false;
                }
            }
        );
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
            return this.folder.depth + "rem";
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

            if (!parentId || !self.folders || self.folder.type === "root") {
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
            toggleBranch: "folders/toggleBranch",
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

            self.$emit("item-dropped", self.folder);

            self.is_dragged_over = false;
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

            self.$emit("selected-folder-changed", self.folder);
        },

        /**
         * Keep track of current timestamp
         */
        startTimer: function (event) {
            const self = this;

            self.timer = window.setTimeout(function () {
                self.longClick = true;

                self.toggleBranch(self.folder);
            }, 500);
        },

        /**
         * Expand/collapse button clicked
         */
        onToggleExpandedClicked: function (event) {
            const self = this;

            if (!self.longClick) {
                self.toggleExpanded(self.folder);
            }

            clearTimeout(self.timer);
            self.longClick = false;
        },

        ensureVisible: function () {
            const self = this;

            if (self.enableEnsureVisible) {
                self.$el.scrollIntoView({
                    block: "center",
                });
            }
        },
    },
};
</script>
