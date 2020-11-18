<template>
    <div class="group-item" v-on:click="select">
        <div class="handle" v-if="movable">
            <svg fill="currentColor" width="16" height="16">
                <use v-bind:xlink:href="icon('move-v')" />
            </svg>
        </div>
        <div class="flex-grow p-2">
            <div class="title">{{ group.name }}</div>
            <div class="flex items-center justify-between mb-2">
                <div>
                    <span
                        class="badge ml-0"
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
                </div>
                <div>
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
                    <span
                        class="badge success"
                        v-bind:title="__('Active users')"
                        ><svg
                            fill="currentColor"
                            width="16"
                            height="16"
                            class="inline mr-2"
                        >
                            <use v-bind:xlink:href="icon('group')" /></svg
                        >{{ group.active_users_count }}</span
                    >
                </div>
            </div>
            <div class="feed-item-meta">{{ group.description }}</div>
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
