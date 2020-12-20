<template>
    <div class="vertical list striped items-rounded compact">
        <div
            class="list-item"
            v-for="(value, item) in metaData"
            v-bind:key="item"
        >
            <details v-if="typeof value !== 'string'" class="w-full">
                <summary>{{ item }}</summary>
                <div class="vertical list striped items-rounded compact">
                    <div
                        class="list-item"
                        v-for="(itemValue, itemName) in value"
                        v-bind:key="itemName"
                    >
                        <div class="list-item-title">
                            {{ itemName }}
                        </div>
                        <div class="list-item-value">
                            {{ itemValue }}
                        </div>
                    </div>
                </div>
            </details>
            <div class="list-item-title" v-if="typeof value === 'string'">
                {{ item }}
            </div>
            <div class="list-item-value" v-if="typeof value === 'string'">
                {{ value }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["metaData"],
};
</script>