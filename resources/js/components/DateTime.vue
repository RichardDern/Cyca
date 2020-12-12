<template>
    <time v-bind:datetime="iso">
        {{ formatted }}
    </time>
</template>
<script>
export default {
    props: ["datetime", "onlyDate", "onlyTime"],
    computed: {
        iso: function () {
            if (!this.datetime) {
                return null;
            }

            return new Date(this.datetime).toISOString();
        },
        formatted: function () {
            const date = new Date(this.datetime);

            if (this.onlyTime) {
                return date.toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit",
                });
            } else if (this.onlyDate) {
                return date.toLocaleDateString();
            }

            return (
                date.toLocaleDateString() +
                ", " +
                date.toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit",
                })
            );
        },
    },
};
</script>
