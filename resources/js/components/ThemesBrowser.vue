<template>
    <div id="themes-browser">
        <h2>{{ __("Official themes") }}</h2>

        <div class="themes-category">
            <theme-card
                v-for="(repository_url, name) in themes.official"
                v-bind:repository_url="repository_url"
                v-bind:name="name"
                v-bind:key="name"
                v-bind:is_selected="name === selected"
                v-on:selected="useTheme(name)"
            ></theme-card>
        </div>

        <div v-if="themes.community && themes.community.length > 0">
            <h2>{{ __("Community themes") }}</h2>

            <p>{{ __("These themes were hand-picked by Cyca's author.") }}</p>

            <div class="themes-category">
                <theme-card
                    v-for="(repository_url, name) in themes.community"
                    v-bind:repository_url="repository_url"
                    v-bind:name="name"
                    v-bind:key="name"
                    v-bind:is_selected="name === selected"
                    v-on:selected="useTheme(name)"
                ></theme-card>
            </div>
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
                .getAttribute("content"),
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
        useTheme: function (theme) {
            const self = this;

            api.post(route("account.setTheme"), {
                theme: theme,
            }).then(function () {
                document
                    .querySelector('meta[name="theme"]')
                    .setAttribute("content", theme);
                document
                    .getElementById("main-stylesheet")
                    .setAttribute("href", "/themes/" + theme + "/theme.css");

                self.selected = theme;
            });
        },
    },
};
</script>
