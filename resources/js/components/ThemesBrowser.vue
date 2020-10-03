<template>
    <div id="themes-browser">
        <div
            class="card"
            v-bind:class="{ selected: dirname === selected }"
            v-for="(data, dirname) in themes"
            v-bind:key="dirname"
            v-on:click.capture="selectTheme($event, dirname)"
        >
            <h2>
                <div>{{ data["name"] }}</div>
                <div>
                    <a
                        v-bind:title="__('Preview')"
                        v-bind:href="route('home', { theme: dirname })"
                        target="_blank"
                        ><svg
                            fill="currentColor"
                            width="16"
                            height="16"
                            class="favicon"
                        >
                            <use
                                v-bind:xlink:href="icon('unread_items')"
                            /></svg
                    ></a>
                </div>
            </h2>
            <img v-bind:src="'/themes/' + dirname + '/' + data['screenshot']" />
            <p class="meta">
                {{ __("Created by") }}:
                <a
                    v-bind:href="data['url']"
                    target="_blank"
                    rel="noopener noreferrer"
                    >{{ data["author"] }}</a
                >
            </p>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            themes: [],
            selected: document
                .querySelector('meta[name="theme"]')
                .getAttribute("content")
        };
    },
    mounted: function () {
        const self = this;

        self.loadThemes();
    },
    methods: {
        loadThemes: async function () {
            const self = this;

            self.themes = await api.get(route("account.getThemes"));
        },
        selectTheme: function (event, theme) {
            const self = this;

            if (
                event.target.tagName === "A" ||
                event.target.tagName === "use"
            ) {
                return false;
            }

            api.post(route('account.setTheme'), {
                theme: theme
            }).then(function() {
                self.selected = theme;

                document.getElementById('main-stylesheet').setAttribute('href', '/themes/' + theme + '/theme.css');
            });
        },
    },
};
</script>
