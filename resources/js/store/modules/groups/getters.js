/**
 * Groups getters
 */
export default {
    /**
     * Return groups
     */
    groups: state => {
        return state.groups;
    },
    /**
     * Return currently selected group
     */
    selectedGroup: state => {
        var groups = state.groups ? state.groups : [];

        return groups.find(group => group.is_selected);
    }
}
