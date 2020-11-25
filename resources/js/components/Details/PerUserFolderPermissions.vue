<template>
    <details class="mt-4" v-on:toggle="onToggle">
        <summary>{{ __("Per-user permissions") }}:</summary>

        <form v-if="usersWithoutPermissions.length > 0" class="mt-2">
            <div class="form-group">
                <label for="addPermissionsForUser" class="inline"
                    >{{ __("Add explicit permissions for") }}:</label
                >
                <select
                    v-model="addPermissionsForUser"
                    id="addPermissionsForUser"
                >
                    <option
                        v-for="user in usersWithoutPermissions"
                        v-bind:key="user.id"
                        v-bind:value="user.id"
                    >
                        {{ user.name }}
                    </option>
                </select>
            </div>
        </form>

        <div class="feeds-list mt-2" v-for="user in users" v-bind:key="user.id">
            <div class="flex justify-between items-center">
                <div>
                    {{ user.name }}
                    <small class="feed-item-meta inline ml-4">{{
                        user.email
                    }}</small>
                </div>
                <div>
                    <button
                        class="info inline"
                        v-on:click="onRemovePermissions(user)"
                    >
                        {{ __("Apply default permissions") }}
                    </button>
                </div>
            </div>
            <div class="mt-2">
                <permission-box
                    v-bind:text="__('Create folder')"
                    ability="can_create_folder"
                    v-bind:folder="folder"
                    v-bind:user="user"
                ></permission-box>
                <permission-box
                    v-bind:text="__('Update folder')"
                    ability="can_update_folder"
                    v-bind:folder="folder"
                    v-bind:user="user"
                ></permission-box>
                <permission-box
                    v-bind:text="__('Delete folder')"
                    ability="can_delete_folder"
                    v-bind:folder="folder"
                    v-bind:user="user"
                ></permission-box>
                <permission-box
                    v-bind:text="__('Create document')"
                    ability="can_create_document"
                    v-bind:folder="folder"
                    v-bind:user="user"
                ></permission-box>
                <permission-box
                    v-bind:text="__('Delete document')"
                    ability="can_delete_document"
                    v-bind:folder="folder"
                    v-bind:user="user"
                ></permission-box>
            </div>
        </div>
    </details>
</template>

<script>
import PermissionBox from "./PermissionBox.vue";

export default {
    components: { PermissionBox },
    props: ["folder"],
    data: function () {
        return {
            users: [],
            usersWithoutPermissions: [],
            addPermissionsForUser: null,
        };
    },
    watch: {
        folder: function (folder) {
            this.loadPermissions();
        },
        addPermissionsForUser: function (userId) {
            const self = this;

            if (userId) {
                api.post(
                    route("folder.set_permission", { folder: self.folder.id }),
                    {
                        user_id: userId,
                    }
                ).then(function (response) {
                    self.users = response;
                    self.addPermissionsForUser = null;

                    self.loadUsersWithoutPermissions();
                });
            }
        },
    },
    methods: {
        onToggle: function (evt) {
            if (evt.target.open) {
                this.loadPermissions();
            }
        },
        loadPermissions: function () {
            this.loadPerUserPermissions();
            this.loadUsersWithoutPermissions();
        },
        loadPerUserPermissions: function () {
            const self = this;

            api.get(route("folder.per_user_permissions", self.folder)).then(
                function (response) {
                    self.users = response;
                }
            );
        },
        loadUsersWithoutPermissions: function () {
            const self = this;

            api.get(
                route("folder.users_without_permissions", self.folder)
            ).then(function (response) {
                self.usersWithoutPermissions = response;
            });
        },

        onRemovePermissions: function (user) {
            const self = this;

            api.delete(
                route("folder.remove_permissions", {
                    folder: self.folder.id,
                    user: user.id,
                })
            ).then(function (response) {
                self.users = response;

                self.loadUsersWithoutPermissions();
            });
        },
    },
};
</script>