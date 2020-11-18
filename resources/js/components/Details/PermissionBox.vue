<template>
    <label class="my-2 mx-2">
        <input
            type="checkbox"
            v-model="granted"
            v-on:input="
                updatePermission({
                    ability: ability,
                    granted: $event.target.checked,
                    folder: folder,
                })
            "
        />
        {{ text }}
    </label>
</template>

<script>
import { mapActions } from "vuex";
export default {
    props: ["folder", "ability", "text"],
    data: function() {
        return {
            granted: false
        };
    },
    watch: {
        folder: function() {
            this.granted = this.canByDefault(this.ability);
            this.$forceUpdate();
        }
    },
    mounted: function() {
        this.granted = this.canByDefault(this.ability);
    },
    methods: {
        ...mapActions({
            updatePermission: "folders/updatePermission",
        }),
        canByDefault: function (permission) {
            const self = this;

            if (
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