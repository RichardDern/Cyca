<template>
    <tr>
        <td class="w-1/12 pb-1">
            <input
                type="color"
                class="w-full h-7 mr-1"
                v-model="highlight.color"
                v-bind:aria-label="__('Color')"
            />
        </td>
        <td class="pb-1 px-1">
            <input
                type="text"
                class="w-full"
                v-model="highlight.expression"
                v-bind:aria-label="__('Expression')"
            />
        </td>
        <td class="w-1/12 pb-1">
            <button
                type="button"
                class="danger h-7"
                v-on:click="removeHighlight(highlight.id)"
                v-bind:title="__('Remove highlight')"
            >
                <svg fill="currentColor" width="16" height="16">
                    <use v-bind:xlink:href="icon('trash')" />
                </svg>
            </button>
        </td>
    </tr>
</template>

<script>
export default {
    props: ["highlight"],
    mounted: function () {
        const self = this;

        self.$watch("highlight.expression", self.updateHighlight);
        self.$watch("highlight.color", self.updateHighlight);
    },
    methods: {
        updateHighlight: async function () {
            const self = this;
            const json = await api.put(
                route("highlight.update", self.highlight),
                {
                    expression: self.highlight.expression,
                    color: self.highlight.color,
                }
            );
        },
        removeHighlight: function (id) {
            this.$emit("destroy", id);
        },
    },
};
</script>
