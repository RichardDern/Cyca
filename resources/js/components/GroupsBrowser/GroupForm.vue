<template>
    <article>
        <header>
            <h1>
                {{ title }}
            </h1>
        </header>

        <div class="body">
            <div
                v-if="group && group.description"
                v-html="group.description"
            ></div>
            <form
                v-on:submit.prevent="onSubmit"
                v-if="
                    !group ||
                    (group &&
                        (group.pivot.status === 'own' ||
                            group.pivot.status === 'created'))
                "
            >
                <input type="hidden" name="id" v-model="id" />
                <div class="form-group">
                    <input
                        type="text"
                        v-model="name"
                        v-bind:placeholder="__('Group name')"
                    />
                </div>
                <div class="form-group">
                    <input
                        type="text"
                        v-model="description"
                        v-bind:placeholder="__('Description')"
                    />
                </div>

                <div class="form-group">
                    <label class="my-0"
                        ><input type="checkbox" v-model="inviteOnly" />
                        <span class="ml-2">{{ __("Invite only") }}</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="my-0"
                        ><input type="checkbox" v-model="autoAcceptUsers" />
                        <span class="ml-2">{{ __("Auto-accept users") }}</span>
                    </label>
                </div>

                <div class="flex justify-between mt-4">
                    <button class="success" type="submit">
                        <svg fill="currentColor" width="16" height="16">
                            <use
                                v-bind:xlink:href="icon(!id ? 'add' : 'update')"
                            />
                        </svg>
                        {{ !id ? __("Create group") : __("Update group") }}
                    </button>
                    <div v-if="id !== null" class="flex items-center">
                        <button
                            class="secondary"
                            v-on:click.stop.prevent="$emit('unselect')"
                        >
                            <svg fill="currentColor" width="16" height="16">
                                <use v-bind:xlink:href="icon('cancel')" />
                            </svg>
                            {{ __("Cancel") }}
                        </button>
                        <button
                            v-if="group.pivot.status !== 'own'"
                            class="danger ml-1"
                            v-on:click.stop.prevent="
                                $emit('group-deleted', group)
                            "
                        >
                            <svg fill="currentColor" width="16" height="16">
                                <use v-bind:xlink:href="icon('trash')" />
                            </svg>
                            {{ __("Delete") }}
                        </button>
                    </div>
                </div>
            </form>

            <form
                v-if="
                    group &&
                    (group.pivot.status === 'own' ||
                        group.pivot.status === 'created')
                "
                v-on:submit.prevent="onInviteSubmit"
            >
                <div class="form-group">
                    <div class="input-group">
                        <input
                            type="text"
                            name="title"
                            v-model="inviteEmail"
                            v-bind:placeholder="__('E-Mail Address')"
                        />
                        <button type="submit" class="info">
                            <svg
                                fill="currentColor"
                                width="16"
                                height="16"
                                class="mr-1"
                            >
                                <use v-bind:xlink:href="icon('join')" />
                            </svg>
                            {{ __("Invite in group") }}
                        </button>
                    </div>
                </div>
            </form>

            <div
                v-if="group && group.pivot.status === 'invited'"
                class="mt-4 flex items-center justify-between"
            >
                <button
                    class="success"
                    v-on:click="$emit('invitation-accepted', group)"
                >
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="mr-1"
                    >
                        <use v-bind:xlink:href="icon('check')" />
                    </svg>
                    {{ __("Accept invitation") }}
                </button>
                <button
                    class="danger ml-2"
                    v-on:click="$emit('invitation-declined', group)"
                >
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="mr-1"
                    >
                        <use v-bind:xlink:href="icon('cancel')" />
                    </svg>
                    {{ __("Decline invitation") }}
                </button>
            </div>

            <div
                v-if="
                    group &&
                    (group.pivot.status === 'accepted' ||
                        group.pivot.status === 'joining')
                "
                class="mt-4"
            >
                <button class="danger" v-on:click="$emit('leave', group)">
                    <svg fill="currentColor" width="16" height="16">
                        <use v-bind:xlink:href="icon('logout')" />
                    </svg>
                    {{ __("Leave the group") }}
                </button>
            </div>
        </div>
    </article>
</template>

<script>
export default {
    props: ["group"],
    data: function () {
        return {
            id: null,
            name: null,
            description: null,
            inviteOnly: false,
            autoAcceptUsers: false,
            inviteEmail: null,
        };
    },
    computed: {
        title: function () {
            if (this.group) {
                return this.group.name;
            }

            return this.__("Create group");
        },
    },
    watch: {
        group: function (group) {
            if (!group) {
                this.id = null;
                this.name = null;
                this.description = null;
                this.inviteOnly = false;
                this.autoAcceptUsers = false;
            } else {
                this.id = group.id;
                this.name = group.name;
                this.description = group.description;
                this.inviteOnly = group.invite_only;
                this.autoAcceptUsers = group.auto_accept_users;
            }

            this.$forceUpdate();
        },
    },
    methods: {
        onSubmit: function () {
            const self = this;
            const properties = {
                name: self.name,
                description: self.description,
                invite_only: self.inviteOnly,
                auto_accept_users: self.autoAcceptUsers,
            };

            if (!properties.name) {
                return;
            }

            if (self.id) {
                properties["id"] = self.id;

                self.$emit("group-updated", properties);
            } else {
                self.$emit("group-created", properties);

                self.id = null;
                self.name = null;
                self.description = null;
                self.inviteOnly = false;
                self.autoAcceptUsers = false;
            }
        },

        onInviteSubmit: function () {
            const self = this;

            if (!self.inviteEmail) {
                return false;
            }

            api.post(route("group.invite_user", self.group), {
                email: self.inviteEmail,
            }).then(function (response) {
                self.inviteEmail = null;
                self.$emit("invitation-sent");
                //TODO: Handle errors
            });
        },
    },
};
</script>