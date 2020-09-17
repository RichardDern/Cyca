<template>
    <time v-bind:datetime="iso">
        {{ formatted }}
    </time>
</template>
<script>
export default {
    props: ['datetime', 'only-date', 'only-time', 'with-seconds', 'calendar'],
    computed: {
        iso: function() {
            if(!this.datetime) {
                return null;
            }

            return moment(this.datetime).toISOString();
        },
        formatted: function() {
            if(!this.datetime) {
                return null;
            }

            if(this.calendar) {
                return moment(this.datetime).calendar();
            }

            let format = "LLLL";

            if(this.onlyDate) {
                format = 'LL';
            } else if(this.onlyTime) {
                if(this.withSeconds) {
                    format = 'LTS';
                } else {
                    format = 'LT';
                }
            }

            return moment(this.datetime).format(format);
        }
    }
}
</script>
