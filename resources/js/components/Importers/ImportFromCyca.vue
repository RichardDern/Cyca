<template>
    <div>
        <label for="file">{{ __("File to import") }}</label>

        <input type="file" id="file" />

        <button
            type="submit"
            class="info p-2 mt-6"
            v-on:click="onImport"
        >
            {{ __("Import") }}
        </button>
    </div>
</template>

<script>
export default {
    methods: {
        onImport: function () {
            const input = document.getElementById("file");
            let formData = new FormData();

            formData.append("file", input.files[0]);

            this.$emit('import', formData);
        },
    },
};
</script>
