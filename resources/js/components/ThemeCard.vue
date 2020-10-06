<template>
    <div class="card" v-bind:class="{ selected: is_selected }">
        <header>
            <h2>{{ meta.name }}</h2>
            <svg
                v-if="is_selected"
                fill="currentColor"
                viewBox="0 0 16 16"
                width="32"
                height="32"
                class="checkmark"
            >
                <use v-bind:xlink:href="icon('checkmark')" />
            </svg>
            <div v-if="!is_selected" class="flex">
                <a
                    class="button info mr-2"
                    v-bind:title="__('Preview')"
                    v-bind:href="route('home', { theme: name })"
                    target="_blank"
                >
                    <svg fill="currentColor" width="16" height="16">
                        <use v-bind:xlink:href="icon('unread_items')" />
                    </svg>
                </a>
                <button
                    class="success"
                    v-on:click="useTheme"
                    v-bind:title="__('Use this theme')"
                >
                    <svg fill="currentColor" width="16" height="16">
                        <use v-bind:xlink:href="icon('check')" />
                    </svg>
                </button>
            </div>
        </header>
        <div class="card-body" v-if="meta">
            <p>{{ meta.description }}</p>
            <img v-bind:src="meta.screenshot" />
        </div>
        <footer>
            <small>
                {{ __("Created by") }}
                <a v-bind:href="meta.url">{{ meta.author }}</a>
            </small>
        </footer>
    </div>
</template>

<script>
export default {
    props: ["name", "repository_url", "is_selected"],
    data: function () {
        return {
            meta: {
                name: null,
                author: null,
                url: null,
            },
        };
    },
    mounted: async function () {
        const self = this;

        self.meta = await api.get(
            route("account.theme.details", {
                name: self.name,
            })
        );
    },
    methods: {
        useTheme: function () {
            this.$emit("selected");
        },
    },
};
</script>
