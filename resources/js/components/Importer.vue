<template>
    <div>
        <label for="importer">{{ __("Import from") }}:</label>
        <select v-model="importer" id="importer">
            <option
                v-for="availableImporter in availableImporters"
                v-bind:key="availableImporter.view"
                v-bind:value="availableImporter.view"
            >
                {{ availableImporter.name }}
            </option>
        </select>

        <component v-bind:is="importer"></component>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            availableImporters: [
                {
                    name: "Cyca",
                    view: "import-from-cyca"
                }
            ],
            importer: "import-from-cyca",
        };
    }
};
</script>
