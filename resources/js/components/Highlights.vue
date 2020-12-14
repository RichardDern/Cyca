<template>
    <div class="flex w-full h-screen">
        <div class="w-1/2 h-screen flex flex-col">
            <div class="h-2/3 overflow-auto">
                <draggable
                    class="list vertical striped spaced items-rounded"
                    v-model="sortedHighlights"
                    group="myHighlights"
                    @start="drag = true"
                    @end="drag = false"
                    item-key="id"
                    v-bind:force-fallback="true"
                    v-bind:fallback-tolerance="10"
                    handle=".handle"
                >
                    <template #item="{ element }">
                        <div
                            class="list-item select-none"
                            v-bind:class="{
                                selected:
                                    selectedHighlight &&
                                    selectedHighlight.id === element.id,
                            }"
                        >
                            <div class="handle">
                                <svg fill="currentColor" width="16" height="16">
                                    <use v-bind:xlink:href="icon('move-v')" />
                                </svg>
                            </div>
                            <div class="icons">
                                <div
                                    class="badge font-mono"
                                    v-html="highlightText(element.color)"
                                ></div>
                            </div>
                            <div class="list-item-text">
                                {{ element.expression }}
                            </div>
                            <div class="tools">
                                <button
                                    class="info"
                                    v-on:click="selectedHighlight = element"
                                >
                                    <svg
                                        fill="currentColor"
                                        width="16"
                                        height="16"
                                    >
                                        <use
                                            v-bind:xlink:href="icon('update')"
                                        />
                                    </svg>
                                    <span>{{ __("Edit") }}</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </draggable>
            </div>
            <div class="h-1/3">
                <article>
                    <header>
                        <h1>
                            {{
                                selectedHighlight
                                    ? selectedHighlight.expression
                                    : __("Create highlight")
                            }}
                        </h1>
                    </header>
                    <div class="body">
                        <div class="flex items-center">
                            <div class="flex-none mr-2">
                                <div class="form-group">
                                    <label for="color">{{ __("Color") }}</label>
                                    <input
                                        type="color"
                                        id="color"
                                        v-model="newColor"
                                    />
                                </div>
                            </div>
                            <div class="flex-grow">
                                <div class="form-group">
                                    <label for="expression">{{
                                        __("Expression")
                                    }}</label>
                                    <input
                                        type="text"
                                        id="expression"
                                        v-model="newExpression"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button
                                type="submit"
                                class="success"
                                v-on:click="saveHighlight"
                            >
                                <svg fill="currentColor" width="16" height="16">
                                    <use
                                        v-bind:xlink:href="
                                            icon(
                                                !selectedHighlight
                                                    ? 'add'
                                                    : 'update'
                                            )
                                        "
                                    />
                                </svg>
                                <span>
                                    {{ __("Save") }}
                                </span>
                            </button>
                            <div class="flex items-center space-x-1">
                                <button
                                    class="secondary"
                                    v-on:click="resetForm"
                                >
                                    <svg
                                        fill="currentColor"
                                        width="16"
                                        height="16"
                                    >
                                        <use
                                            v-bind:xlink:href="icon('cancel')"
                                        />
                                    </svg>
                                    <span>
                                        {{ __("Cancel") }}
                                    </span>
                                </button>
                                <button
                                    class="danger"
                                    v-on:click="onDestroy"
                                    v-if="selectedHighlight"
                                >
                                    <svg
                                        fill="currentColor"
                                        width="16"
                                        height="16"
                                    >
                                        <use
                                            v-bind:xlink:href="icon('trash')"
                                        />
                                    </svg>
                                    <span>
                                        {{ __("Delete") }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
        <div class="w-1/2 bg-gray-50 dark:bg-gray-850"></div>
    </div>
</template>

<script>
import draggable from "vuedraggable";
import TEXTColor from "textcolor";

export default {
    components: { draggable },
    data: function () {
        return {
            positions: [],
            highlights: highlights,
            newExpression: null,
            newColor: "#000000",
            selectedHighlight: null,
        };
    },
    computed: {
        sortedHighlights: {
            get() {
                return collect(this.highlights).sortBy("position").all();
            },
            set(value) {
                const self = this;
                const collection = collect(value);
                let positions = {};

                collection.each(function (highlight, index) {
                    positions[highlight.id] = index;
                });

                self.updatePositions({ positions: positions });
            },
        },
    },
    watch: {
        selectedHighlight: function (highlight) {
            const self = this;

            if (highlight) {
                self.newExpression = highlight.expression;
                self.newColor = highlight.color;
            } else {
                self.resetForm();
            }
        },
    },
    methods: {
        resetForm: function () {
            const self = this;

            self.selectedHighlight = null;
            self.newExpression = null;
            self.newColor = "#000000";
        },
        saveHighlight: function () {
            const self = this;

            if (!self.newExpression) {
                return;
            }

            if (self.selectedHighlight) {
                api.put(route("highlight.update", self.selectedHighlight), {
                    expression: self.newExpression,
                    color: self.newColor,
                }).then(function (response) {
                    self.highlights = response;
                    self.resetForm();
                });
            } else {
                api.post(route("highlight.store"), {
                    expression: self.newExpression,
                    color: self.newColor,
                }).then(function (response) {
                    self.highlights = response;
                    self.resetForm();
                });
            }
        },
        onDestroy: function () {
            const self = this;
            api.delete(route("highlight.destroy", self.selectedHighlight)).then(
                function (response) {
                    self.highlights = response;
                    self.selectedHighlight = null;
                }
            );
        },

        updatePositions: function ({ positions }) {
            const self = this;

            for (var id in positions) {
                const highlight = self.highlights.find((h) => h.id == id);

                if (!highlight) {
                    console.warn("Highlight #" + id + " not found");
                    return;
                }

                highlight.position = positions[id];
            }

            api.post(route("highlight.update_positions"), {
                positions: positions,
            });
        },

        highlightText: function (color) {
            let textColor = TEXTColor.findTextColor(color);
            color =
                '<span class="highlight" style="color: ' +
                textColor +
                "; background-color: " +
                color +
                '">' +
                color +
                "</span>";

            return color;
        },
    },
};
</script>
