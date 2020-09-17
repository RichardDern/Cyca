/**
 * Actions on folders
 */
export default {
    /*
     * -------------------------------------------------------------------------
     * ----| CRUD |-------------------------------------------------------------
     * -------------------------------------------------------------------------
     */

    /**
     * Display a listing of the resource.
     */
    async index({ commit }) {
        const response = await axios.get(route("folder.index"));

        commit("setFolders", response.data);
    },

    /**
     * Display the specified resource.
     */
    async show({ commit, getters }, folder) {
        if (!folder) {
            return;
        }

        const currentSelectedFolder = getters.selectedFolder;

        if (currentSelectedFolder && currentSelectedFolder.id === folder.id) {
            // Selected folder has not changed
            return;
        }

        const response = await axios.get(route("folder.show", folder));

        commit("setSelectedFolder", folder);
    },

    /**
     * Store a newly created resource in storage.
     */
    async store({}, { title, parent_id }) {
        await axios.post(route("folder.store"), { title, parent_id });
    },

    /**
     * Update the specified resource in storage.
     */
    async update({ commit }, { folder, newProperties }) {
        const response = await axios.put(
            route("folder.update", folder),
            newProperties
        );

        commit("update", { folder: folder, newProperties: response.data });
    },

    /**
     * Remove the specified resource from storage.
     */
    async destroy({}, folder) {
        await axios.delete(route("folder.destroy", folder));
    },

    /**
     * Toggle specified folder expanded/collapsed
     */
    toggleExpanded({ dispatch }, folder) {
        dispatch("update", {
            folder: folder,
            newProperties: {
                ...folder,
                ...{
                    is_expanded: !folder.is_expanded
                }
            }
        });
    },

    /*
     * -------------------------------------------------------------------------
     * ----| Drag'n'drop |------------------------------------------------------
     * -------------------------------------------------------------------------
     */

    /**
     * Remind folder being dragged
     */
    startDraggingFolder({ commit }, folder) {
        commit("setDraggedFolder", folder);
    },

    /**
     * Forget folder being dragged
     */
    stopDraggingFolder({ commit }) {
        commit("setDraggedFolder", null);
    },

    /**
     * Drop something into specified folder
     */
    async dropIntoFolder({ getters, dispatch }, folder) {
        var sourceFolder = getters.draggedFolder;

        if (!sourceFolder) {
            sourceFolder = getters.selectedFolder;

            await dispatch(
                "documents/dropIntoFolder",
                { sourceFolder: sourceFolder, targetFolder: folder },
                { root: true }
            );

            return;
        }

        const newProperties = {
            parent_id: folder.id
        };

        if (
            sourceFolder.parent_id === folder.id ||
            sourceFolder.id === folder.id
        ) {
            return;
        }

        await dispatch("update", {
            folder: sourceFolder,
            newProperties: {
                ...sourceFolder,
                ...newProperties
            }
        });
    }
};
