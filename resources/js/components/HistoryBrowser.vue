<template>
    <div class="timeline" v-on:scroll.passive="onScroll">
        <div v-for="(sortedEntries, date) in sortedList" v-bind:key="date">
            <h2>
                <date-time
                    v-bind:datetime="date"
                    v-bind:only-date="true"
                ></date-time>
            </h2>
            <ol>
                <li v-for="entry in sortedEntries" v-bind:key="entry.id">
                    <div class="history-entry">
                        <date-time
                            v-bind:datetime="entry.created_at"
                            v-bind:only-time="true"
                        ></date-time>
                        <div class="event" v-html="entry.text"></div>
                    </div>
                </li>
            </ol>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            entries: [],
            nextPageUrl: null,
        };
    },
    mounted: function () {
        this.loadEntries();
    },
    computed: {
        sortedList: function () {
            return collect(this.entries).groupBy("date").all();
        },
    },
    watch: {
        sortedList: function () {
            const self = this;

            self.$nextTick(function () {
                const scrollHeight = self.$el.scrollHeight;
                const innerHeight = self.$el.clientHeight;

                if (scrollHeight === innerHeight && self.nextPageUrl) {
                    self.loadMoreEntries();
                }
            });
        },
    },
    methods: {
        loadEntries: function () {
            const self = this;

            api.get(route("history_entry.index")).then(function (response) {
                self.entries = response.data;
                self.nextPageUrl = response.next_page_url;
            });
        },

        loadMoreEntries: function () {
            const self = this;

            if (self.nextPageUrl) {
                api.get(self.nextPageUrl).then(function (response) {
                    self.entries = [...self.entries, ...response.data];
                    self.nextPageUrl = response.next_page_url;
                });
            }
        },

        onScroll: function ($event) {
            const scrollTop = $event.target.scrollTop;
            const innerHeight = $event.target.clientHeight;
            const scrollHeight = $event.target.scrollHeight;

            if (
                scrollTop + innerHeight >= scrollHeight &&
                this.nextPageUrl !== null
            ) {
                this.loadMoreEntries();
            }
        },
    },
};
</script>
