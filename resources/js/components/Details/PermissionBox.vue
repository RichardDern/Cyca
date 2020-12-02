<template>
    <label
        class="badge cursor-pointer"
        v-bind:class="{ success: granted, danger: !granted }"
    >
        <input
            type="checkbox"
            v-model="granted"
            class="hidden"
            v-on:input="
                updatePermission({
                    ability: ability,
                    granted: $event.target.checked,
                    folder: folder,
                    user: user ? user.id : null,
                })
            "
        />
        <svg fill="currentColor" width="16" height="16" class="mr-1">
            <use
                v-bind:xlink:href="
                    granted
                        ? icon('permission_granted')
                        : icon('permission_denied')
                "
            />
        </svg>
        <span>
            {{ text }}
        </span>
    </label>
</template>

<script>
import { mapActions } from "vuex";
export default {
    props: ["folder", "user", "ability", "text"],
    data: function () {
        return {
            granted: false,
        };
    },
    watch: {
        folder: function () {
            this.granted = this.canByDefault(this.ability);
            this.$forceUpdate();
        },
        user: function () {
            this.granted = this.canByDefault(this.ability);
            this.$forceUpdate();
        },
    },
    mounted: function () {
        this.granted = this.canByDefault(this.ability);
    },
    methods: {
        ...mapActions({
            updatePermission: "folders/updatePermission",
        }),
        canByDefault: function (permission) {
            const self = this;

            if (
                self.user &&
                "permissions" in self.user &&
                permission in self.user.permissions[0]
            ) {
                return self.user.permissions[0][permission];
            } else if (
                "default_permissions" in self.folder &&
                permission in self.folder.default_permissions
            ) {
                return self.folder.default_permissions[permission];
            }

            return false;
        },
    },
};
</script>