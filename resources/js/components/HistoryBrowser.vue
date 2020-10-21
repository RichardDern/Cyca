<template>
    <div
        class="timeline h-screen w-full overflow-auto px-4"
        v-on:scroll.passive="onScroll"
    >
        <div v-for="(sortedEntries, date) in sortedList">
            <h2 class="text-xl bold my-8">
                <date-time
                    v-bind:datetime="date"
                    v-bind:only-date="true"
                ></date-time>
            </h2>
            <ol class="timeline">
                <li
                    class="mb-2"
                    v-for="entry in sortedEntries"
                    v-bind:key="entry.id"
                >
                    <div class="flex w-full items-center">
                        <date-time
                            class="time text-xs w-1/12 text-center"
                            v-bind:datetime="entry.created_at"
                            v-bind:only-time="true"
                        ></date-time>
                        <div class="event flex-grow" v-html="entry.text"></div>
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
