<template>
    <div>
        <label for="importer">{{ __("Import from") }}:</label>
        <select v-model="importer" id="importer">
            <option
                v-for="(importerData, importerName) in availableImporters"
                v-bind:key="importerName"
                v-bind:value="importerName"
            >
                {{ importerData["name"] }}
            </option>
        </select>

        <component
            v-bind:is="availableImporters[importer]['view']"
            v-on:import="onImport"
        ></component>

        <div v-if="uploading" class="mt-2">
            {{ __("Importing, please wait...") }}
        </div>
        <div v-if="uploaded" class="alert success">
            {{ __("Data imported") }}
        </div>
        <div v-if="error" class="alert error">
            {{ __("A problem occurred, interrupting importation") }}
        </div>
    </div>
</template>

<script>
import ImportFromCyca from "./Importers/ImportFromCyca";

export default {
    components: { ImportFromCyca },
    props: ["availableImporters"],
    data: function () {
        return {
            importer: "cyca",
            uploading: false,
            uploaded: false,
            error: false,
        };
    },
    methods: {
        onImport: function (formData) {
            const self = this;

            if (self.uploading) {
                return;
            }

            self.uploaded = false;
            self.uploading = true;
            self.error = false;

            formData.append("importer", self.importer);

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
                    if (!response.ok) {
                        self.uploaded = false;
                        self.error = true;
                    } else {
                        self.uploaded = true;
                        self.error = false;
                    }
                })
                .catch((error) => {
                    self.uploaded = false;
                    self.error = true;
                })
                .finally(() => {
                    self.uploading = false;
                });
        },
    },
};
</script>
