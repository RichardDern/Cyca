<template>
    <details class="mt-4">
        <summary>{{ __("Users without explicit permissions can") }}:</summary>

        <div class="body flex items-center mt-2 space-x-2">
            <permission-box
                v-bind:text="__('Create folder')"
                ability="can_create_folder"
                v-bind:folder="folder"
            ></permission-box>
            <permission-box
                v-bind:text="__('Update folder')"
                ability="can_update_folder"
                v-bind:folder="folder"
            ></permission-box>
            <permission-box
                v-bind:text="__('Delete folder')"
                ability="can_delete_folder"
                v-bind:folder="folder"
            ></permission-box>
            <permission-box
                v-bind:text="__('Create document')"
                ability="can_create_document"
                v-bind:folder="folder"
            ></permission-box>
            <permission-box
                v-bind:text="__('Delete document')"
                ability="can_delete_document"
                v-bind:folder="folder"
            ></permission-box>
        </div>
    </details>
</template>

<script>
import PermissionBox from "./PermissionBox.vue";

export default {
    components: { PermissionBox },
    props: ["folder"],
    methods: {
        can: function (permission) {
            const self = this;

            if (
                "user_permissions" in self.folder &&
                permission in self.folder.user_permissions
            ) {
                return self.folder.user_permissions[permission];
            }

            return false;
        },
    },
};
</script>