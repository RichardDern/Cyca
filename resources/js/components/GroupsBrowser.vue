<template>
    <div class="flex w-full h-screen">
        <div class="w-1/2 h-screen flex flex-col">
            <div class="h-1/2 overflow-auto">
                <draggable
                    class="list vertical striped spaced items-rounded"
                    v-model="myGroupsSorted"
                    group="myGroups"
                    @start="drag = true"
                    @end="drag = false"
                    item-key="id"
                    v-bind:force-fallback="true"
                    v-bind:fallback-tolerance="10"
                    handle=".handle"
                >
                    <template #item="{ element }">
                        <groups-browser-item
                            class="select-none"
                            v-bind:group="element"
                            v-bind:movable="true"
                            v-on:selected="selectedGroup = element"
                            v-bind:class="{
                                selected: selectedGroup === element,
                            }"
                        ></groups-browser-item>
                    </template>
                </draggable>
            </div>
            <group-form
                class="h-1/2"
                v-bind:group="selectedGroup"
                v-on:unselect="selectedGroup = null"
                v-on:group-created="onGroupCreated"
                v-on:group-updated="onGroupUpdated"
                v-on:group-deleted="onGroupDeleted"
                v-on:invitation-sent="onInvitationSent"
                v-on:invitation-accepted="onInvitationAccepted"
                v-on:invitation-declined="onInvitationDeclined"
                v-on:leave="onLeaveGroup"
            >
            </group-form>
        </div>
        <div class="w-1/2 bg-gray-50 dark:bg-gray-850">
            <article>
                <header>
                    <h1>
                        <div class="flex justify-between items-center w-full">
                            <div class="mr-4">
                                {{ __("Public groups") }}
                            </div>
                            <input
                                type="search"
                                v-bind:placeholder="__('Search')"
                                v-model="search"
                            />
                        </div>
                    </h1>
                </header>
                <div class="list vertical striped spaced items-rounded">
                    <groups-browser-item
                        v-for="group in publicGroups"
                        v-bind:group="group"
                        v-bind:key="group.id"
                        v-bind:movable="false"
                        v-on:join="onJoinGroup"
                    ></groups-browser-item>
                </div>
            </article>
        </div>
    </div>
</template>

<script>
import GroupsBrowserItem from "./GroupsBrowser/GroupsBrowserItem";
import GroupForm from "./GroupsBrowser/GroupForm";
import draggable from "vuedraggable";
import { mapActions, mapGetters } from "vuex";

export default {
    components: { draggable, GroupForm, GroupsBrowserItem },
    data: function () {
        return {
            positions: [],
            selectedGroup: null,
            publicGroups: [],
            search: "",
            drag: false,
        };
    },
    mounted: function () {
        const self = this;

        self.indexMyGroups();
        self.loadPublicGroups();
    },
    computed: {
        ...mapGetters({
            myGroups: "groups/groups",
        }),
        myGroupsSorted: {
            get() {
                return collect(this.myGroups).sortBy("pivot.position").all();
            },
            set(value) {
                const self = this;
                const collection = collect(value);
                let positions = {};

                collection.each(function (group, index) {
                    positions[group.id] = index;
                });

                self.updatePositions({ positions: positions });
            },
        },
    },
    watch: {
        search: function (value) {
            this.loadPublicGroups();
        },
    },
    methods: {
        ...mapActions({
            indexMyGroups: "groups/indexMyGroups",
            createGroup: "groups/createGroup",
            updateGroup: "groups/updateGroup",
            updateGroupProperties: "groups/updateProperties",
            deleteGroup: "groups/deleteGroup",
            updatePositions: "groups/updatePositions",
        }),

        loadPublicGroups: function () {
            const self = this;

            api.get(route("group.index"), { search: self.search }).then(
                function (response) {
                    self.publicGroups = response.data;
                }
            );
        },

        onGroupCreated: function (group) {
            const self = this;

            self.createGroup(group);
        },

        onGroupUpdated: function (group) {
            const self = this;

            self.updateGroup({
                group: group,
                newProperties: group,
            });
        },

        onGroupDeleted: function (group) {
            const self = this;

            self.deleteGroup(group).then(function () {
                self.selectedGroup = null;
            });
        },

        onInvitationSent: function (response) {
            const self = this;

            self.updateGroupProperties({
                groupId: self.selectedGroup.id,
                newProperties: response,
            });
        },

        onInvitationAccepted: function (group) {
            const self = this;

            api.post(route("group.accept_invitation", group)).then(function (
                response
            ) {
                self.updateGroupProperties({
                    groupId: response.id,
                    newProperties: response,
                });
            });

            //TODO: Handle errors
        },

        onInvitationDeclined: function (group) {
            const self = this;

            api.post(route("group.reject_invitation", group)).then(function (
                response
            ) {
                self.updateGroupProperties({
                    groupId: response.id,
                    newProperties: response,
                });
            });

            //TODO: Handle errors
        },

        onLeaveGroup: function (group) {
            const self = this;

            api.post(route("group.leave", group)).then(function () {
                self.indexMyGroups();
                self.loadPublicGroups();
                self.selectedGroup = null;
            });

            //TODO: Handle errors
        },

        onJoinGroup: function (group) {
            const self = this;

            api.post(route("group.join", group)).then(function () {
                self.indexMyGroups();
                self.loadPublicGroups();
                self.selectedGroup = null;
            });

            //TODO: Handle errors
        },
    },
};
</script>
