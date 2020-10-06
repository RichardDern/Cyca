<template>
    <div>
        <label for="file">{{ __("File to import") }}</label>

        <input type="file" id="file" />

        <button
            type="submit"
            class="info p-2 mt-6"
            v-on:click="onImport"
            v-bind:disabled="uploading"
        >
            {{ __("Import") }}
        </button>

        <div v-if="uploading" class="mt-2">{{__("Uploading...")}}</div>
        <div v-if="uploaded" class="alert success">
            {{ __("Data imported") }}
        </div>
        <div v-if="error" class="alert error">
            {{ __("A problem occured, interrupting importation") }}
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            uploading: false,
            uploaded: false,
            error: false,
        };
    },
    methods: {
        onImport: function () {
            const self = this;

            if (self.uploading) {
                return;
            }

            self.uploaded = false;
            self.uploading = true;
            self.error = false;

            const input = document.getElementById("file");
            let formData = new FormData();

            formData.append("file", input.files[0]);

            fetch(route("account.import"), {
                body: formData,
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
                .then((response) => {
                    self.uploading = false;

                    if (!response.ok) {
                        self.uploaded = false;
                        self.error = true;
                    } else {
                        self.uploaded = true;
                        self.error = false;
                    }
                })
                .catch((error) => {
                    self.uploading = false;
                    self.uploaded = false;
                    self.error = true;
                });
        },
    },
};
</script>
