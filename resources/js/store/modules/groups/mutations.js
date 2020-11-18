/**
 * Groups mutations
 */
export default {
    /**
     * Store groups list
     * @param {*} state
     * @param {*} groups
     */
    setGroups(state, groups) {
        state.groups = groups;
    },

    /**
     * Unselect all groups, and mark specified group as selected
     * @param {*} state
     * @param {*} group
     */
    setSelectedGroup(state, group) {
        state.groups.find(g => (g.is_selected = false));

        group.is_selected = true;
    },

    /**
     * Update group's properties
     * @param {*} state
     * @param {*} param1
     */
    update(state, { group, newProperties }) {
        for (var property in newProperties) {
            group[property] = newProperties[property];
        }
    },

    updatePosition(state, { group, position }) {
        group.pivot.position = position;
    }
};
