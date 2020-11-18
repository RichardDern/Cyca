<template>
    <button
        class="list-item"
        v-bind:class="{ selected: group.is_selected }"
        v-on:click="$emit('selected-group-changed', group)"
    >
        <div class="list-item-label pl-0">
            <svg
                fill="currentColor"
                width="16"
                height="16"
                class="favicon folder-group"
            >
                <use v-bind:xlink:href="icon('group')" />
            </svg>
            <div class="truncate flex-grow py-0.5">
                {{ group.name }}
            </div>
        </div>
        <div class="badge default" v-if="group.feed_item_states_count > 0">
            {{ group.feed_item_states_count }}
        </div>
    </button>
</template>

<script>
export default {
    props: ["group"],
};
</script>