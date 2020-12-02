<template>
    <div class="list-item">
        <div class="handle">
            <svg fill="currentColor" width="16" height="16">
                <use v-bind:xlink:href="icon('move-v')" />
            </svg>
        </div>
        <div class="flex-grow flex justify-between">
            <div>
                <h2 class="text-black dark:text-white">{{ group.name }}</h2>
                <div v-if="group.creator" class="mb-1">
                    {{ __("Created by") }} {{ group.creator.name }}
                </div>
                <div class="mb-1">{{ group.description }}</div>
                <div class="flex items-center space-x-1">
                    <span
                        v-if="group.pivot"
                        class="badge"
                        v-bind:class="'group-status-' + group.pivot.status"
                        >{{ status }}</span
                    >
                    <span
                        class="badge default"
                        v-bind:class="{
                            'group-invite-only': group.invite_only,
                        }"
                        v-if="group.invite_only"
                        >{{ __("Invite only") }}</span
                    >
                    <span
                        class="badge"
                        v-bind:class="{
                            success: group.auto_accept_users,
                            warning: !group.auto_accept_users,
                        }"
                        v-if="!group.invite_only"
                        >{{
                            group.auto_accept_users
                                ? __("Auto-accept users")
                                : __("New users must be approved")
                        }}</span
                    >
                </div>
            </div>
        </div>
        <div class="badges">
            <span
                v-if="group.pending_users_count > 0"
                class="badge warning"
                v-bind:title="__('Pending users')"
                ><svg
                    fill="currentColor"
                    width="16"
                    height="16"
                    class="inline mr-2"
                >
                    <use v-bind:xlink:href="icon('group')" /></svg
                >{{ group.pending_users_count }}</span
            >
            <span class="badge success" v-bind:title="__('Active users')"
                ><svg
                    fill="currentColor"
                    width="16"
                    height="16"
                    class="inline mr-2"
                >
                    <use v-bind:xlink:href="icon('group')" /></svg
                >{{ group.active_users_count }}</span
            >
            <button
                class="info ml-2"
                v-if="group.pivot"
                v-on:click="$emit('selected', group)"
            >
                <svg
                    fill="currentColor"
                    width="16"
                    height="16"
                    class="inline mr-2"
                >
                    <use v-bind:xlink:href="icon('update')" /></svg
                >{{ __("Edit") }}
            </button>
            <button
                class="success inline ml-2"
                v-if="!group.pivot"
                v-on:click="$emit('join', group)"
            >
                <svg
                    fill="currentColor"
                    width="16"
                    height="16"
                    class="inline mr-2"
                >
                    <use v-bind:xlink:href="icon('join')" /></svg
                >{{ group.auto_accept_users ? __("Join") : __("Apply") }}
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ["group", "movable"],
    computed: {
        status: function () {
            const self = this;

            if (!self.group.pivot) {
                return;
            }

            switch (self.group.pivot.status) {
                case "own":
                    return self.__("My own group");
                case "created":
                    return self.__("Created by me");
                case "invited":
                    return self.__("Invited to join");
                case "accepted":
                    return self.__("Accepted");
                case "rejected":
                    return self.__("Rejected");
                case "left":
                    return self.__("Group left");
                case "joining":
                    return self.__("Waiting for joining");
            }
        },
    },
    methods: {
        select: function () {
            this.$emit("selected");
        },
    },
};
</script>
