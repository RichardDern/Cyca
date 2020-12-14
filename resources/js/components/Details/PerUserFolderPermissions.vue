<template>
    <stateful-details
        name="per_user_folders_permissions_details"
        class="mt-4"
        v-on:toggle="onToggle"
    >
        <summary>{{ __("Per-user permissions") }}:</summary>

        <div class="body">
            <form v-if="usersWithoutPermissions.length > 0">
                <div class="input-group">
                    <label for="addPermissionsForUser"
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

            <div
                class="list vertical spaced striped items-rounded compact"
                v-for="user in users"
                v-bind:key="user.id"
            >
                <div class="list-item">
                    <div>
                        {{ user.name }}
                        <small class="feed-item-meta inline ml-22">{{
                            user.email
                        }}</small>
                        <div class="flex items-center space-x-2">
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
                    <div>
                        <button
                            class="info"
                            v-on:click="onRemovePermissions(user)"
                        >
                            <svg fill="currentColor" width="16" height="16">
                                <use v-bind:xlink:href="icon('cancel')" /></svg
                            ><span>{{ __("Apply default permissions") }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </stateful-details>
</template>

<script>
import PermissionBox from "./PermissionBox.vue";
import StatefulDetails from "../StatefulDetails.vue";

export default {
    components: { PermissionBox, StatefulDetails },
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