<template>
    <div>
        <div class="form-group">
            <label for="file">{{ __("File to import") }}</label>

            <input type="file" id="file" />
        </div>

        <div class="form-group">
            <button type="submit" v-on:click="onImport">
                → {{ __("Import") }}
            </button>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        onImport: function () {
            const input = document.getElementById("file");
            let formData = new FormData();

            formData.append("file", input.files[0]);

            this.$emit("import", formData);
        },
    },
};
</script>
