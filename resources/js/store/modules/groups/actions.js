/**
 * Actions on groups
 */
export default {
    /*
     * -------------------------------------------------------------------------
     * ----| CRUD |-------------------------------------------------------------
     * -------------------------------------------------------------------------
     */

    /**
     * Display a listing of the resource - Groups in which user is active
     */
    async indexActive({ commit }) {
        const data = await api.get(route("group.index_active"));

        commit("setGroups", data);
    },

    /**
     * Display a listing of the resource - Groups associated with this user
     */
    async indexMyGroups({ commit }) {
        const data = await api.get(route("group.my_groups"));

        commit("setGroups", data);
    },

    /**
     * Display the specified resource.
     */
    async show({ getters, dispatch }, group, folder) {
        const currentSelectedGroup = getters.selectedGroup;

        if (!group) {
            group = currentSelectedGroup;
        } else if (Number.isInteger(group)) {
            group = getters.groups.find(g => g.id === group);
        }

        dispatch("selectGroup", group);
        dispatch("documents/selectDocuments", [], { root: true });

        const folders = await api.get(route("group.show", group));

        dispatch("folders/index", folders, { root: true });

        if (folder) {
            dispatch("folders/show", folder, { root: true });
        }
    },

    /**
     * Mark specified group as selected
     */
    selectGroup({ commit, getters }, group) {
        if (Number.isInteger(group)) {
            group = getters.groups.find(g => g.id === group);
        }

        commit("setSelectedGroup", group);
    },

    /**
     * Update a group
     */
    updateGroup({ dispatch }, { group, newProperties }) {
        api.put(route("group.update", group), newProperties).then(function(
            response
        ) {
            dispatch("updateProperties", {
                groupId: group.id,
                newProperties: response
            });
        });
    },

    /**
     * Delete group
     */
    deleteGroup({ commit }, group) {
        api.delete(route("group.destroy", group)).then(function(response) {
            commit("setGroups", response);
        });
    },

    /**
     * Update the specified resource in storage.
     */
    updateProperties({ commit, getters }, { groupId, newProperties }) {
        const group = getters.groups.find(g => g.id == groupId);

        if (!group) {
            console.warn("Group #" + groupId + " not found");
            return;
        }

        commit("update", { group: group, newProperties: newProperties });
    },

    /**
     * Create a group
     */
    createGroup({ commit }, properties) {
        api.post(route("group.store"), properties).then(function(response) {
            commit("setGroups", response);
        });
    },

    /**
     * Update my groups positions
     */
    updatePositions({ getters, commit }, { positions }) {
        for (var groupId in positions) {
            const group = getters.groups.find(g => g.id == groupId);

            if (!group) {
                console.warn("Group #" + groupId + " not found");
                return;
            }

            commit("updatePosition", {
                group: group,
                position: positions[groupId]
            });
        }

        api.post(route("group.update_positions"), {
            positions: positions
        });
    }
};
