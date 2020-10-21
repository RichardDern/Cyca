<template>
    <time v-bind:datetime="iso">
        {{ formatted }}
    </time>
</template>
<script>
export default {
    props: ['datetime', 'onlyDate', 'onlyTime'],
    computed: {
        iso: function() {
            if(!this.datetime) {
                return null;
            }

            return new Date(this.datetime).toISOString();
        },
        formatted: function() {
            if(this.onlyTime) {
                return new Date(this.datetime).toLocaleTimeString();
            } else if(this.onlyDate) {
                return new Date(this.datetime).toLocaleDateString();
            }

            return new Date(this.datetime).toLocaleString();
        }
    }
}
</script>
