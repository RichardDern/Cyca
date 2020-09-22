<template>
    <time v-bind:datetime="iso">
        {{ formatted }}
    </time>
</template>
<script>
export default {
    props: ['datetime'],
    computed: {
        iso: function() {
            if(!this.datetime) {
                return null;
            }

            return new Date(this.datetime).toISOString();
        },
        formatted: function() {
            return new Date(this.datetime).toLocaleString();
        }
    }
}
</script>
