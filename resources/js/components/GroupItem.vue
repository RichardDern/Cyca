<template>
    <a
        v-bind:href="route('group.show', group)"
        class="list-item"
        v-bind:class="{ selected: group.is_selected }"
        v-on:click.stop.prevent="$emit('selected-group-changed', group)"
    >
        <div class="icons">
            <svg
                fill="currentColor"
                width="16"
                height="16"
                class="text-green-500"
            >
                <use v-bind:xlink:href="icon('group')" />
            </svg>
        </div>
        <div class="list-item-text">
            {{ group.name }}
        </div>
        <div class="badges">
            <div class="badge default" v-if="group.feed_item_states_count > 0">
                <span v-if="group.has_new_unread_items">
                    <svg
                        fill="currentColor"
                        width="16"
                        height="16"
                        class="text-blue-300"
                    >
                        <use v-bind:xlink:href="icon('update')" />
                    </svg>
                </span>
                {{ group.feed_item_states_count }}
            </div>
        </div>
    </a>
</template>

<script>
export default {
    props: ["group"],
};
</script>