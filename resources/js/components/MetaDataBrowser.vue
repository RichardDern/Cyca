<template>
    <div>
        <div v-for="(value, item) in metaData" v-bind:key="item">
            <stateful-details
                class="list-item"
                v-if="isArrayOrObject(value)"
                v-bind:name="getStatefulDetailsName(item)"
            >
                <summary>{{ item }}</summary>
                <browser
                    class="vertical list striped items-rounded compact alt"
                    v-bind:meta-data="value"
                    v-bind:parent-name="getStatefulDetailsName(item)"
                ></browser>
            </stateful-details>
            <div class="list-item" v-if="!isArrayOrObject(value)">
                <div class="list-item-title">
                    {{ item }}
                </div>
                <div class="list-item-value">
                    <code>{{ value }}</code>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import StatefulDetails from "./StatefulDetails.vue";
export default {
    components: { StatefulDetails },
    name: "browser",
    props: ["metaData", "parentName"],
    methods: {
        getStatefulDetailsName: function (name) {
            const self = this;

            return collect(["details", self.parentName, name]).join("_");
        },
        isArrayOrObject: function (variable) {
            return typeof variable === "array" || typeof variable === "object";
        },
    },
};
</script>