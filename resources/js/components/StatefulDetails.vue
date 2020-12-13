<template>
    <details v-bind:open="opened" v-on:toggle="onToggle"><slot></slot></details>
</template>

<script>
export default {
    props: ["name"],
    computed: {
        opened: function () {
            return localStorage.getItem(this.name);
        },
    },
    methods: {
        onToggle: function (event) {
            if (event.target.open) {
                localStorage.setItem(this.name, event.target.open);
            } else {
                localStorage.removeItem(this.name);
            }
        },
    },
};
</script>