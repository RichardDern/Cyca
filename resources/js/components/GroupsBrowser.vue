<template>
    <div id="groups-browser">
        <div class="left-panel">
            <article id="my-groups">
                <div class="body mt-4">
                    <draggable
                        group="my_groups"
                        handle=".handle"
                        draggable=".group-item"
                        v-model="myGroupsSorted"
                    >
                        <groups-browser-item
                            v-for="group in myGroupsSorted"
                            v-bind:group="group"
                            v-bind:key="group.id"
                            v-bind:movable="true"
                            v-on:selected="selectedGroup = group"
                            v-bind:class="{ selected: selectedGroup === group }"
                        ></groups-browser-item>
                    </draggable>
                </div>
            </article>
            <group-form
                id="created-group-form"
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
        <div class="right-panel"></div>
    </div>
</template>

<script>
import draggable from "vuedraggable";
import { mapActions, mapGetters } from "vuex";

export default {
    data: function () {
        return {
            positions: [],
            selectedGroup: null,
        };
    },
    mounted: function () {
        const self = this;

        self.indexMyGroups();
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
    methods: {
        ...mapActions({
            indexMyGroups: "groups/indexMyGroups",
            createGroup: "groups/createGroup",
            updateGroup: "groups/updateGroup",
            updateGroupProperties: "groups/updateProperties",
            deleteGroup: "groups/deleteGroup",
            updatePositions: "groups/updatePositions",
        }),

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
                self.selectedGroup = null;
            });

            //TODO: Handle errors
        },
    },
};
</script>
