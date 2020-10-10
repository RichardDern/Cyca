<template>
    <tr>
        <td class="w-1/2"><input type="text" class="w-full" v-model="highlight.expression" v-bind:aria-label="__('Expression')" /></td>
        <td class="w-1/4"><input type="color" class="w-full" v-model="highlight.color" v-bind:aria-label="__('Color')" /></td>
        <td>
            <button type="button" class="danger" v-on:click="removeHighlight(highlight.id)" v-bind:title="__('Remove highlight')">
                <svg fill="currentColor" width="16" height="16">
                    <use v-bind:xlink:href="icon('trash')" />
                </svg>
            </button>
        </td>
    </tr>
</template>

<script>
export default {
    props: ['highlight'],
    mounted: function() {
        const self = this;

        self.$watch('highlight.expression', self.updateHighlight);
        self.$watch('highlight.color', self.updateHighlight);
    },
    methods: {
        updateHighlight: async function() {
            const self = this;
            const json = await api.put(route('highlight.update', self.highlight), {
                expression: self.highlight.expression,
                color: self.highlight.color
            });
        },
        removeHighlight: function(id) {
            this.$emit('destroy', id);
        }
    }
}
</script>
