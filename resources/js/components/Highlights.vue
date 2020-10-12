<template>
    <table class="w-full">
        <thead>
            <tr>
                <th class="w-1/12"></th>
                <th class="pb-2 px-1">{{ __("Expression") }}</th>
                <th class="w-1/12"></th>
            </tr>
            <tr>
                <td class="w-1/12 pb-4">
                    <input
                        type="color"
                        class="h-7 w-full"
                        v-model="newColor"
                        v-bind:aria-label="__('Color')"
                    />
                </td>
                <td class="pb-4 px-1">
                    <input
                        type="text"
                        class="w-full"
                        v-model.lazy="newExpression"
                        v-bind:aria-label="__('Expression')"
                    />
                </td>
                <td class="w-1/12 pb-4">
                    <button
                        type="submit"
                        class="h-7 success"
                        v-on:click="addHighlight"
                        v-bind:title="__('Add highlight')"
                    >
                        <svg fill="currentColor" width="16" height="16">
                            <use v-bind:xlink:href="icon('add')" />
                        </svg>
                    </button>
                </td>
            </tr>
        </thead>
        <tbody>
            <highlight
                v-for="highlight in sortedHighlights"
                v-bind:key="highlight.id"
                v-bind:highlight="highlight"
                v-on:destroy="onDestroy"
            ></highlight>
        </tbody>
    </table>
</template>

<script>
export default {
    data: function () {
        return {
            highlights: highlights,
            newExpression: null,
            newColor: '#000000',
        };
    },
    computed: {
        sortedHighlights: function () {
            return collect(this.highlights).sortBy("expression");
        },
    },
    methods: {
        addHighlight: async function () {
            const self = this;

            if (!self.newExpression) {
                return;
            }

            self.highlights = await api.post(route("highlight.store"), {
                expression: self.newExpression,
                color: self.newColor,
            });

            self.newExpression = null;
            self.newColor = '#000000';
        },
        onDestroy: async function (id) {
            const self = this;
            self.highlights = await api.delete(route("highlight.destroy", id));
        },
    },
};
</script>
